<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\Order;
use App\Models\Borrowing;
use App\Models\ReturnBook;
use App\Models\Fine;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $totalBorrows = Borrowing::count();
        $totalStockSale = Book::sum('stock_sale');
        $totalStockBorrow = Book::sum('stock_borrow');

        // Pendapatan Bulan Ini & Sepanjang Waktu
        $revOrder = Order::whereIn('status', ['Processing', 'Shipped', 'Completed'])
                         ->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('total');
        $revFine = Fine::where('status', 'Paid')
                        ->whereMonth('paid_at', date('m'))->whereYear('paid_at', date('Y'))->sum('amount');
        $revenueThisMonth = $revOrder + $revFine;

        $revAllOrder = Order::whereIn('status', ['Processing', 'Shipped', 'Completed'])->sum('total');
        $revAllFine = Fine::where('status', 'Paid')->sum('amount');
        $revenueAllTime = $revAllOrder + $revAllFine;

        // STATUS PEMBELIAN
        $ordWaiting    = Order::where('status', 'Waiting Confirmation')->count(); // Menunggu Konfirmasi
        $ordProcessing = Order::where('status', 'Processing')->count();           // Dikemas
        $ordShipped    = Order::where('status', 'Shipped')->count();              // Dikirim
        $ordCompleted  = Order::where('status', 'Completed')->count();            // Selesai
        $ordCancelled  = Order::where('status', 'Cancelled')->count();            // Dibatalkan
        $ordRejected   = Order::where('status', 'Rejected')->count();             // Ditolak

        // STATUS PEMINJAMAN
        $bwPending         = Borrowing::where('status', 'Pending')->count();
        $bwBorrowed        = Borrowing::where('status', 'Borrowed')->count();
        $bwOverdue         = Borrowing::where('status', 'Overdue')->count();
        $bwReturnRequested = Borrowing::where('status', 'Return Requested')->count();
        $bwReturned        = Borrowing::where('status', 'Returned')->count();
        $bwRejected        = Borrowing::where('status', 'Rejected')->count();

        // Pengembalian Pending
        $retPending = ReturnBook::where('status', 'Pending')->count();

        // List Pending
        $listBorrowPending = Borrowing::with(['user', 'book'])->where('status', 'Pending')->latest()->take(5)->get();
        $listReturnPending = ReturnBook::with(['borrowing.user', 'borrowing.book'])->where('status', 'Pending')->orderBy('requested_at', 'desc')->take(5)->get();

        // List Terbaru
        $listRecentOrders = Order::with(['user', 'details.book'])->latest()->take(5)->get();
        $listRecentUsers = User::where('role', 'user')->with(['activeBorrowings.book'])->latest()->take(5)->get();
        $listLowStock = Book::where('stock_sale', '<', 5)->orWhere('stock_borrow', '<', 2)->orderBy('stock_sale', 'asc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalBooks', 'totalUsers', 'totalOrders', 'totalBorrows', 'totalStockSale', 'totalStockBorrow',
            'revenueThisMonth', 'revenueAllTime',
            'ordWaiting', 'ordProcessing', 'ordShipped', 'ordCompleted', 'ordCancelled', 'ordRejected',
            'bwPending', 'bwBorrowed', 'bwOverdue', 'bwReturnRequested', 'bwReturned', 'bwRejected',
            'retPending', 'listBorrowPending', 'listReturnPending', 'listRecentOrders', 'listRecentUsers', 'listLowStock'
        ));
    }
}
