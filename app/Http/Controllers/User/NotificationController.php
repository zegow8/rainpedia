<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $category = $request->get('category', 'all');

        // Tandai terbaca saat membuka halaman
        Notification::where('user_id', $userId)->where('is_read', 0)->update(['is_read' => 1]);

        $baseQuery = Notification::where('user_id', $userId);

        // Hitung jumlah data per kategori tab
        $allCount = (clone $baseQuery)->count();

        $orderCount = (clone $baseQuery)->where(function($q) {
            $q->where('link', 'like', '%transaction%')
              ->orWhere('title', 'like', '%Pesanan%')
              ->orWhere('title', 'like', '%Pembelian%');
        })->count();

        $borrowCount = (clone $baseQuery)->where(function($q) {
            $q->where('link', 'like', '%borrow%')
              ->orWhere('title', 'like', '%Peminjaman%')
              ->orWhere('title', 'like', '%Pengembalian%')
              ->orWhere('title', 'like', '%Pinjam%');
        })->count();

        $chatCount = (clone $baseQuery)->where(function($q) {
            $q->where('link', 'like', '%chat%')
              ->orWhere('title', 'like', '%Pesan Baru%')
              ->orWhere('title', 'like', '%Chat%');
        })->count();

        // Query notifikasi dengan filter
        $query = Notification::where('user_id', $userId)->orderBy('created_at', 'desc');

        if ($category === 'order') {
            $query->where(function($q) {
                $q->where('link', 'like', '%transaction%')
                  ->orWhere('title', 'like', '%Pesanan%')
                  ->orWhere('title', 'like', '%Pembelian%');
            });
        } elseif ($category === 'borrow') {
            $query->where(function($q) {
                $q->where('link', 'like', '%borrow%')
                  ->orWhere('title', 'like', '%Peminjaman%')
                  ->orWhere('title', 'like', '%Pengembalian%')
                  ->orWhere('title', 'like', '%Pinjam%');
            });
        } elseif ($category === 'chat') {
            $query->where(function($q) {
                $q->where('link', 'like', '%chat%')
                  ->orWhere('title', 'like', '%Pesan Baru%')
                  ->orWhere('title', 'like', '%Chat%');
            });
        }

        $notifs = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => $allCount,
            'order' => $orderCount,
            'borrow' => $borrowCount,
            'chat' => $chatCount,
        ];

        return view('user.notifications', compact('notifs', 'category', 'counts'));
    }
}
