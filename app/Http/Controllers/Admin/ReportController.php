<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Fine;

class ReportController extends Controller
{
    private function getFilteredTransactions(Request $request)
    {
        $source = $request->get('source', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $keyword = $request->get('q');

        $transactions = collect();

        // 1. Ambil data pesanan pembelian
        if ($source === 'all' || $source === 'order') {
            $orderQuery = Order::with('user')
                ->whereIn('status', ['Processing', 'Shipped', 'Completed']);

            if (!empty($startDate)) {
                $orderQuery->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $orderQuery->whereDate('created_at', '<=', $endDate);
            }
            if (!empty($keyword)) {
                $orderQuery->where(function($q) use ($keyword) {
                    $q->where('invoice_code', 'like', "%{$keyword}%")
                      ->orWhere('receiver_name', 'like', "%{$keyword}%")
                      ->orWhereHas('user', function($uq) use ($keyword) {
                          $uq->where('name', 'like', "%{$keyword}%")
                             ->orWhere('email', 'like', "%{$keyword}%");
                      });
                });
            }

            $orders = $orderQuery->orderBy('created_at', 'desc')->get()->map(function($o) {
                return (object)[
                    'id' => $o->id,
                    'date' => $o->created_at,
                    'source' => 'Pembelian Buku',
                    'desc' => $o->invoice_code . ' (' . ($o->user->name ?? $o->receiver_name ?? 'User') . ')',
                    'amount' => $o->total,
                    'link' => '/admin/orders/' . $o->id,
                    'badge' => 'order',
                    'ref_code' => $o->invoice_code,
                    'customer' => $o->user->name ?? $o->receiver_name ?? 'Pelanggan'
                ];
            });
            $transactions = $transactions->concat($orders);
        }

        // 2. Ambil data denda peminjaman lunas
        if ($source === 'all' || $source === 'fine') {
            $fineQuery = Fine::with(['borrowing.book', 'borrowing.user'])
                ->where('status', 'Paid');

            if (!empty($startDate)) {
                $fineQuery->whereDate('paid_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $fineQuery->whereDate('paid_at', '<=', $endDate);
            }
            if (!empty($keyword)) {
                $fineQuery->where(function($q) use ($keyword) {
                    $q->whereHas('borrowing.user', function($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%")
                           ->orWhere('email', 'like', "%{$keyword}%");
                    })->orWhereHas('borrowing.book', function($bq) use ($keyword) {
                        $bq->where('title', 'like', "%{$keyword}%");
                    });
                });
            }

            $fines = $fineQuery->orderBy('paid_at', 'desc')->get()->map(function($f) {
                $bookTitle = $f->borrowing->book->title ?? 'Buku Dihapus';
                $userName = $f->borrowing->user->name ?? 'User';
                return (object)[
                    'id' => $f->id,
                    'date' => $f->paid_at,
                    'source' => 'Denda Peminjaman',
                    'desc' => 'Denda: ' . $bookTitle . ' - ' . $userName,
                    'amount' => $f->amount,
                    'link' => '/admin/borrowings?status=Returned',
                    'badge' => 'fine',
                    'ref_code' => 'DND-' . $f->id,
                    'customer' => $userName
                ];
            });
            $transactions = $transactions->concat($fines);
        }

        // Urutkan berdasarkan tanggal terbaru
        return $transactions->sortByDesc('date')->values();
    }

    public function index(Request $request)
    {
        $source = $request->get('source', 'all');
        $transactions = $this->getFilteredTransactions($request);

        $total = $transactions->sum('amount');
        $totalOrders = $transactions->where('badge', 'order')->sum('amount');
        $totalFines = $transactions->where('badge', 'fine')->sum('amount');

        // Pagination 10 per halaman
        $page = $request->get('page', 1);
        $perPage = 10;
        $paginatedTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions->forPage($page, $perPage)->values(),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.payments.index', [
            'transactions' => $paginatedTransactions,
            'total' => $total,
            'totalOrders' => $totalOrders,
            'totalFines' => $totalFines,
            'source' => $source,
            'startDate' => $request->get('start_date'),
            'endDate' => $request->get('end_date'),
            'keyword' => $request->get('q')
        ]);
    }

    public function print(Request $request)
    {
        $transactions = $this->getFilteredTransactions($request);

        $total = $transactions->sum('amount');
        $totalOrders = $transactions->where('badge', 'order')->sum('amount');
        $totalFines = $transactions->where('badge', 'fine')->sum('amount');

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $source = $request->get('source', 'all');

        $period = 'Semua Periode';
        if ($startDate && $endDate) {
            $period = date('d F Y', strtotime($startDate)) . ' s/d ' . date('d F Y', strtotime($endDate));
        } elseif ($startDate) {
            $period = 'Mulai ' . date('d F Y', strtotime($startDate));
        } elseif ($endDate) {
            $period = 'Sampai ' . date('d F Y', strtotime($endDate));
        }

        return view('admin.payments.print', compact(
            'transactions', 'total', 'totalOrders', 'totalFines',
            'period', 'source', 'startDate', 'endDate'
        ));
    }
}
