<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\ReturnBook;
use App\Models\Book;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    /**
     * Halaman Data Peminjaman
     */
    public function index(Request $request)
    {
        // Auto update Overdue
        Borrowing::where('status', 'Borrowed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', date('Y-m-d'))
            ->update(['status' => 'Overdue']);

        $query = Borrowing::with(['user', 'book', 'fine'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->whereHas('user', function($uq) use ($keyword) {
                    $uq->where('name', 'like', "%{$keyword}%")
                       ->orWhere('email', 'like', "%{$keyword}%");
                })->orWhereHas('book', function($bq) use ($keyword) {
                    $bq->where('title', 'like', "%{$keyword}%")
                       ->orWhere('author', 'like', "%{$keyword}%");
                });
            });
        }

        $borrows = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => Borrowing::count(),
            'Pending' => Borrowing::where('status', 'Pending')->count(),
            'Borrowed' => Borrowing::where('status', 'Borrowed')->count(),
            'Overdue' => Borrowing::where('status', 'Overdue')->count(),
            'Return Requested' => Borrowing::where('status', 'Return Requested')->count(),
            'Returned' => Borrowing::where('status', 'Returned')->count(),
            'Rejected' => Borrowing::where('status', 'Rejected')->count(),
        ];

        return view('admin.borrowings.index', compact('borrows', 'counts'));
    }

    /**
     * Cetak Laporan Peminjaman Resmi (Print / PDF)
     */
    public function print(Request $request)
    {
        Borrowing::where('status', 'Borrowed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', date('Y-m-d'))
            ->update(['status' => 'Overdue']);

        $query = Borrowing::with(['user', 'book', 'fine'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->whereHas('user', function($uq) use ($keyword) {
                    $uq->where('name', 'like', "%{$keyword}%")
                       ->orWhere('email', 'like', "%{$keyword}%");
                })->orWhereHas('book', function($bq) use ($keyword) {
                    $bq->where('title', 'like', "%{$keyword}%")
                       ->orWhere('author', 'like', "%{$keyword}%");
                });
            });
        }

        $borrows = $query->get();

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $period = 'Semua Periode';
        if ($startDate && $endDate) {
            $period = date('d F Y', strtotime($startDate)) . ' s/d ' . date('d F Y', strtotime($endDate));
        } elseif ($startDate) {
            $period = 'Mulai ' . date('d F Y', strtotime($startDate));
        } elseif ($endDate) {
            $period = 'Sampai ' . date('d F Y', strtotime($endDate));
        }

        return view('admin.borrowings.print', compact('borrows', 'period', 'request'));
    }

    /**
     * Proses ACC / Tolak Pengajuan Peminjaman
     */
    public function action(Request $request)
    {
        $borrow = Borrowing::findOrFail($request->borrowing_id);
        
        if ($borrow->status !== 'Pending') {
            return back()->with('error', 'Data sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                $book = Book::find($borrow->book_id);
                
                if ($book->stock_borrow <= 0) {
                    throw new \Exception('Stok pinjam buku ini sedang habis.');
                }

                // Potong stok pinjam
                $book->decrement('stock_borrow');
                
                // Update status borrowing
                $borrow->update([
                    'status' => 'Borrowed',
                    'start_date' => date('Y-m-d'),
                    'due_date' => date('Y-m-d', strtotime("+{$borrow->duration_days} days"))
                ]);

                // Kirim Notifikasi
                Notification::create([
                    'user_id' => $borrow->user_id, 
                    'title' => 'Peminjaman Disetujui',
                    'message' => 'Harap kembalikan buku ' . $book->title . ' sebelum ' . date('d M Y', strtotime($borrow->due_date)),
                    'link' => '/user/borrowings'
                ]);

            } else {
                // Tolak Peminjaman
                $borrow->update(['status' => 'Rejected']);
                
                Notification::create([
                    'user_id' => $borrow->user_id, 
                    'title' => 'Peminjaman Ditolak', 
                    'message' => 'Pengajuan peminjaman buku Anda ditolak oleh admin.',
                    'link' => '/user/borrowings'
                ]);
            }

            DB::commit();
            return back()->with('success', 'Peminjaman berhasil diproses.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Data Pengembalian
     */
    public function returns(Request $request)
    {
        $query = ReturnBook::with(['borrowing.user', 'borrowing.book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->orderBy('requested_at', 'desc')->paginate(10)->withQueryString();

        $counts = [
            'all' => ReturnBook::count(),
            'Pending' => ReturnBook::where('status', 'Pending')->count(),
            'Approved' => ReturnBook::where('status', 'Approved')->count(),
        ];

        return view('admin.returns.index', compact('returns', 'counts'));
    }

    /**
     * Proses ACC Penerimaan Pengembalian Buku
     */
    public function returnAction(Request $request)
    {
        if ($request->filled('borrowing_id')) {
            $borrow = Borrowing::with(['fine', 'book', 'user'])->findOrFail($request->borrowing_id);
            $ret = ReturnBook::where('borrowing_id', $borrow->id)->first();
        } else {
            $ret = ReturnBook::with(['borrowing.fine', 'borrowing.book', 'borrowing.user'])->findOrFail($request->return_id);
            $borrow = $ret->borrowing;
        }
        
        if (!$borrow || !in_array($borrow->status, ['Return Requested', 'Borrowed', 'Overdue'])) {
            return back()->with('error', 'Data pengembalian sudah diproses atau status tidak valid.');
        }

        // VALIDASI BEBAS DENDA: Peminjam harus melunasi denda terlebih dahulu jika ada
        $hasUnpaidFine = \App\Models\Fine::where('borrowing_id', $borrow->id)->where('status', 'Unpaid')->exists();
        if ($hasUnpaidFine) {
            return back()->with('error', 'Pengembalian TIDAK DAPAT di-ACC: Peminjam memiliki denda keterlambatan yang belum lunas (Unpaid). Pastikan denda dibayar terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            // Update Return menjadi Approved (Buku Diterima) jika ada record ReturnBook
            if ($ret) {
                $ret->update([
                    'status' => 'Approved', 
                    'reviewed_at' => now(), 
                    'reviewed_by' => auth()->id()
                ]);
            } else {
                ReturnBook::create([
                    'borrowing_id' => $borrow->id,
                    'status' => 'Approved',
                    'requested_at' => now(),
                    'reviewed_at' => now(),
                    'reviewed_by' => auth()->id()
                ]);
            }
            
            // Update status Borrowing menjadi Returned
            $borrow->update(['status' => 'Returned']);
            
            // Kembalikan Stok Pinjam Buku
            Book::where('id', $borrow->book_id)->increment('stock_borrow');
            
            // Kirim Notifikasi ke User
            Notification::create([
                'user_id' => $borrow->user_id, 
                'title' => 'Pengembalian Diterima ✅', 
                'message' => 'Buku ' . ($borrow->book->title ?? '') . ' telah berhasil diterima kembali oleh perpustakaan. Terima kasih telah meminjam di Rainpedia!',
                'link' => '/user/borrowings'
            ]);

            DB::commit();
            return back()->with('success', 'Pengembalian buku berhasil di-ACC! Status peminjaman kini selesai (Returned) dan stok buku pinjam telah bertambah.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}
