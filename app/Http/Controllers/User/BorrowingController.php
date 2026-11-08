<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\ReturnBook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Update status Overdue secara dinamis
        Borrowing::where('user_id', $userId)
            ->where('status', 'Borrowed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', date('Y-m-d'))
            ->update(['status' => 'Overdue']);

        $query = Borrowing::with(['book', 'fine'])
            ->where('user_id', $userId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $borrows = $query->orderBy('created_at', 'desc')->get();

        $counts = [
            'all' => Borrowing::where('user_id', $userId)->count(),
            'Pending' => Borrowing::where('user_id', $userId)->where('status', 'Pending')->count(),
            'Borrowed' => Borrowing::where('user_id', $userId)->where('status', 'Borrowed')->count(),
            'Overdue' => Borrowing::where('user_id', $userId)->where('status', 'Overdue')->count(),
            'Return Requested' => Borrowing::where('user_id', $userId)->where('status', 'Return Requested')->count(),
            'Returned' => Borrowing::where('user_id', $userId)->where('status', 'Returned')->count(),
            'Rejected' => Borrowing::where('user_id', $userId)->where('status', 'Rejected')->count(),
        ];

        return view('user.borrowings', compact('borrows', 'counts'));
    }

    public function showBorrowForm($bookId)
    {
        $book = Book::findOrFail($bookId);
        $userId = Auth::id();

        $activeCount = Borrowing::where('user_id', $userId)->whereIn('status', ['Pending', 'Borrowed', 'Overdue', 'Return Requested'])->count();
        $hasFine = Fine::where('user_id', $userId)->where('status', 'Unpaid')->exists();
        $hasThisBook = Borrowing::where('user_id', $userId)->where('book_id', $bookId)->whereIn('status', ['Pending', 'Borrowed', 'Overdue', 'Return Requested'])->exists();

        return view('user.borrow_request', compact('book', 'activeCount', 'hasFine', 'hasThisBook'));
    }

    public function processBorrow(Request $request)
    {
        $request->validate([
            'book_id' => 'required',
            'duration_days' => 'required|integer|min:1|max:7',
            'agree' => 'required'
        ]);

        $userId = Auth::id();
        $book = Book::findOrFail($request->book_id);

        // Validasi ulang
        $activeCount = Borrowing::where('user_id', $userId)->whereIn('status', ['Pending', 'Borrowed', 'Overdue', 'Return Requested'])->count();
        if ($activeCount >= 2) return back()->with('error', 'Batas maksimal peminjaman aktif tercapai (maks 2 buku).');
        if (Fine::where('user_id', $userId)->where('status', 'Unpaid')->exists()) return back()->with('error', 'Anda memiliki denda yang belum lunas.');
        if ($book->stock_borrow <= 0) return back()->with('error', 'Stok pinjam habis.');

        Borrowing::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'duration_days' => $request->duration_days,
            'status' => 'Pending'
        ]);

        Notification::create([
            'user_id' => $userId,
            'title' => 'Pengajuan Peminjaman',
            'message' => 'Pengajuan pinjam buku "' . $book->title . '" sedang menunggu persetujuan admin.',
            'link' => '/user/borrowings'
        ]);

        return redirect('/user/borrowings')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function requestReturn(Request $request)
    {
        $borrowing = Borrowing::where('user_id', Auth::id())->findOrFail($request->borrowing_id);
        
        if (!in_array($borrowing->status, ['Borrowed', 'Overdue'])) {
            return back()->with('error', 'Data peminjaman tidak valid.');
        }

        DB::beginTransaction();
        try {
            $today = date('Y-m-d');
            $lateDays = 0;
            $fineAmount = 0;

            if ($today > $borrowing->due_date) {
                $diff = strtotime($today) - strtotime($borrowing->due_date);
                $lateDays = floor($diff / (60 * 60 * 24));
                $fineAmount = $lateDays * 10000;
            }

            $borrowing->update([
                'status' => 'Return Requested',
                'return_request_date' => $today,
                'late_days' => $lateDays,
                'fine_amount' => $fineAmount
            ]);

            ReturnBook::create([
                'borrowing_id' => $borrowing->id,
                'status' => 'Pending',
                'requested_at' => now()
            ]);

            if ($fineAmount > 0) {
                Fine::create([
                    'borrowing_id' => $borrowing->id,
                    'user_id' => Auth::id(),
                    'amount' => $fineAmount,
                    'status' => 'Unpaid'
                ]);
            }

            DB::commit();
            return redirect('/user/borrowings')->with('success', 'Pengajuan pengembalian berhasil. Menunggu konfirmasi admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function payFine(Request $request)
    {
        $fine = Fine::where('user_id', Auth::id())->where('status', 'Unpaid')->findOrFail($request->fine_id);

        DB::beginTransaction();
        try {
            $fine->update(['status' => 'Paid', 'paid_at' => now()]);

            Payment::create([
                'user_id' => Auth::id(),
                'type' => 'fine',
                'borrowing_id' => $fine->borrowing_id,
                'amount' => $fine->amount,
                'method' => 'Manual'
            ]);

            DB::commit();
            return redirect('/user/borrowings')->with('success', 'Denda berhasil dibayar. Anda sudah bisa meminjam buku lagi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
