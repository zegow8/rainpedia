<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\BorrowingController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\NotificationController;

// ================= PUBLIC =================
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/book/{id}', [PageController::class, 'bookDetail'])->name('book.detail');
Route::get('/about', [PageController::class, 'about'])->name('about');

// ================= AUTH =================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= USER AREA (DILINDUNGI MIDDLEWARE USER) =================
Route::middleware(['user'])->group(function () {
    Route::get('/user', fn() => redirect('/user/dashboard'));
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/catalog', [PageController::class, 'userCatalog'])->name('user.catalog');
    
    // Chat ke Admin
    Route::get('/user/chat', [App\Http\Controllers\User\ChatController::class, 'index'])->name('user.chat');
    Route::post('/user/chat/send', [App\Http\Controllers\User\ChatController::class, 'send']);
    
    // Keranjang
    Route::get('/user/cart', [CartController::class, 'index']);
    Route::post('/user/cart/add', [CartController::class, 'add']);
    Route::post('/user/cart/update/{id}', [CartController::class, 'update']);
    Route::post('/user/cart/remove/{id}', [CartController::class, 'remove']);

    // Checkout
    Route::get('/user/checkout', [CheckoutController::class, 'index']);
    Route::post('/user/checkout', [CheckoutController::class, 'process']);

    // Peminjaman
    Route::get('/user/borrowings', [BorrowingController::class, 'index']);
    Route::get('/user/borrow/{bookId}', [BorrowingController::class, 'showBorrowForm']);
    Route::post('/user/borrowings/process', [BorrowingController::class, 'processBorrow']);
    Route::post('/user/borrowings/return', [BorrowingController::class, 'requestReturn']);
    Route::post('/user/borrowings/pay-fine', [BorrowingController::class, 'payFine']);

    // Transaksi
    Route::get('/user/transactions', [OrderController::class, 'index']);
    Route::get('/user/transactions/{id}', [OrderController::class, 'show']);
    Route::post('/user/transactions/cancel', [OrderController::class, 'cancel']);

    // Profil & Notifikasi
    Route::get('/user/profile', [ProfileController::class, 'index']);
    Route::post('/user/profile', [ProfileController::class, 'update']);
    Route::post('/user/profile/password', [ProfileController::class, 'changePassword']);
    Route::get('/user/notifications', [NotificationController::class, 'index']);
});

// ================= ADMIN AREA (DILINDUNGI MIDDLEWARE ADMIN) =================
Route::middleware(['admin'])->group(function () {
    Route::get('/admin', fn() => redirect('/admin/dashboard'));
    
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);

        // Chat Pelanggan / User
        Route::get('/chats', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('admin.chats');
        Route::post('/chats/send', [App\Http\Controllers\Admin\ChatController::class, 'send']);

        // Buku & Kategori
    Route::get('/books', [App\Http\Controllers\Admin\BookController::class, 'index']);
    Route::get('/books/create', [App\Http\Controllers\Admin\BookController::class, 'create']);
    Route::post('/books', [App\Http\Controllers\Admin\BookController::class, 'store']);
    Route::get('/books/edit/{id}', [App\Http\Controllers\Admin\BookController::class, 'edit']);
    Route::post('/books/update/{id}', [App\Http\Controllers\Admin\BookController::class, 'update']);
    Route::post('/books/delete/{id}', [App\Http\Controllers\Admin\BookController::class, 'destroy']);

    Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index']);
    Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store']);
    Route::post('/categories/delete/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy']);

    // Order & Peminjaman
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index']);
    Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show']);
    Route::post('/orders/update/{id}', [App\Http\Controllers\Admin\OrderController::class, 'update']);

    Route::get('/borrowings', [App\Http\Controllers\Admin\BorrowController::class, 'index']);
    Route::post('/borrowings/action', [App\Http\Controllers\Admin\BorrowController::class, 'action']);
    Route::get('/returns', [App\Http\Controllers\Admin\BorrowController::class, 'returns']);
    Route::post('/returns/action', [App\Http\Controllers\Admin\BorrowController::class, 'returnAction']);

    // Pendapatan (Report)
    Route::get('/payments', [App\Http\Controllers\Admin\ReportController::class, 'index']);
    
    // Data User
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index']);
    Route::post('/users/toggle', [App\Http\Controllers\Admin\UserController::class, 'toggleActive']);
    
    // Profil Admin
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index']);
    Route::post('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update']);
    });
});
