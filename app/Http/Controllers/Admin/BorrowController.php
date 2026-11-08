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
        $ret = ReturnBook::with('borrowing')->findOrFail($request->return_id);
        
        if ($ret->status !== 'Pending') {
            return back()->with('error', 'Data pengembalian sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update Return menjadi Approved (Buku Diterima)
            $ret->update([
                'status' => 'Approved', 
                'reviewed_at' => now(), 
                'reviewed_by' => auth()->id()
            ]);
            
            // Update status Borrowing menjadi Returned
            $ret->borrowing->update(['status' => 'Returned']);
            
            // Kembalikan Stok Pinjam Buku
            Book::where('id', $ret->borrowing->book_id)->increment('stock_borrow');
            
            // Kirim Notifikasi ke User
            Notification::create([
                'user_id' => $ret->borrowing->user_id, 
                'title' => 'Pengembalian Diterima ✅', 
                'message' => 'Buku telah berhasil diterima kembali oleh perpustakaan. Terima kasih telah meminjam di Rainpedia!',
                'link' => '/user/borrowings'
            ]);

            DB::commit();
            return back()->with('success', 'Pengembalian buku berhasil di-ACC dan stok buku telah dikembalikan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }
}
