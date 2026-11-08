<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use App\Models\Order;
use App\Models\Fine;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Auto update Overdue
        Borrowing::where('user_id', $userId)
            ->where('status', 'Borrowed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', date('Y-m-d'))
            ->update(['status' => 'Overdue']);

        // 1. STATISTIK PEMINJAMAN PER STATUS
        $bwPending   = Borrowing::where('user_id', $userId)->where('status', 'Pending')->count();
        $bwBorrowed  = Borrowing::where('user_id', $userId)->where('status', 'Borrowed')->count();
        $bwOverdue   = Borrowing::where('user_id', $userId)->where('status', 'Overdue')->count();
        $bwReturnReq = Borrowing::where('user_id', $userId)->where('status', 'Return Requested')->count();
        $bwReturned  = Borrowing::where('user_id', $userId)->where('status', 'Returned')->count();
        $bwRejected  = Borrowing::where('user_id', $userId)->where('status', 'Rejected')->count();
        $totalBorrows = Borrowing::where('user_id', $userId)->count();

        // 2. STATISTIK PESANAN PER STATUS
        $ordWaiting    = Order::where('user_id', $userId)->where('status', 'Waiting Confirmation')->count();
        $ordProcessing = Order::where('user_id', $userId)->where('status', 'Processing')->count();
        $ordShipped    = Order::where('user_id', $userId)->where('status', 'Shipped')->count();
        $ordCompleted  = Order::where('user_id', $userId)->where('status', 'Completed')->count();
        $ordCancelled  = Order::where('user_id', $userId)->where('status', 'Cancelled')->count();
        $ordRejected   = Order::where('user_id', $userId)->where('status', 'Rejected')->count();
        $totalOrders   = Order::where('user_id', $userId)->count();

        // 3. DAFTAR BUKU YANG LAGI DIPINJAM (Borrowed / Overdue) + TENGGAT WAKTU
        $activeBorrowings = Borrowing::with('book')
            ->where('user_id', $userId)
            ->whereIn('status', ['Borrowed', 'Overdue', 'Return Requested'])
            ->orderBy('due_date', 'asc')
            ->get();

        // 4. DAFTAR DENDA (Unpaid & Paid)
        $unpaidFines = Fine::with('borrowing.book')
            ->where('user_id', $userId)
            ->where('status', 'Unpaid')
            ->get();
        $totalUnpaidFineAmount = $unpaidFines->sum('amount');

        // 5. DAFTAR BUKU YANG SUDAH DIKEMBALIKAN (Returned)
        $returnedBooks = Borrowing::with('book')
            ->where('user_id', $userId)
            ->where('status', 'Returned')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 6. AKTIFITAS TERBARU & NOTIFIKASI TERBARU
        $recentNotifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'bwPending', 'bwBorrowed', 'bwOverdue', 'bwReturnReq', 'bwReturned', 'bwRejected', 'totalBorrows',
            'ordWaiting', 'ordProcessing', 'ordShipped', 'ordCompleted', 'ordCancelled', 'ordRejected', 'totalOrders',
            'activeBorrowings', 'unpaidFines', 'totalUnpaidFineAmount',
            'returnedBooks', 'recentNotifications'
        ));
    }
}
