<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Fine;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $source = $request->get('source', 'all');

        $transactions = collect();

        // Ambil data order sukses
        if ($source === 'all' || $source === 'order') {
            $orders = Order::with('user')
                ->whereIn('status', ['Processing', 'Shipped', 'Completed'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($o) {
                    return (object)[
                        'date' => $o->created_at,
                        'source' => 'Pembelian Buku',
                        'desc' => $o->invoice_code . ' (' . ($o->user->name ?? 'User') . ')',
                        'amount' => $o->total,
                        'link' => '/admin/orders/' . $o->id,
                        'badge' => 'order'
                    ];
                });
            $transactions = $transactions->concat($orders);
        }

        // Ambil data denda lunas
        if ($source === 'all' || $source === 'fine') {
            $fines = Fine::with(['borrowing.book', 'borrowing.user'])
                ->where('status', 'Paid')
                ->orderBy('paid_at', 'desc')
                ->get()
                ->map(function($f) {
                    $bookTitle = $f->borrowing->book->title ?? 'Buku Dihapus';
                    $userName = $f->borrowing->user->name ?? 'User';
                    return (object)[
                        'date' => $f->paid_at,
                        'source' => 'Denda Peminjaman',
                        'desc' => 'Denda: ' . $bookTitle . ' - ' . $userName,
                        'amount' => $f->amount,
                        'link' => '/admin/borrowings?status=Returned',
                        'badge' => 'fine'
                    ];
                });
            $transactions = $transactions->concat($fines);
        }

        // Gabung dan urutkan tanggal terbaru
        $transactions = $transactions->sortByDesc('date');
        $total = $transactions->sum('amount');

        // Total per sumber
        $totalOrders = Order::whereIn('status', ['Processing', 'Shipped', 'Completed'])->sum('total');
        $totalFines = Fine::where('status', 'Paid')->sum('amount');

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
            'source' => $source
        ]);
    }
}
