<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'all');
        $search = $request->get('q');

        // Ambil semua user_id unik yang pernah ada riwayat chat
        $userIds = Chat::select('user_id')->distinct()->pluck('user_id')->toArray();

        // Jika admin membuka chat ke user tertentu lewat parameter ?user_id=X, pastikan user_id tersebut masuk ke list
        if ($request->filled('user_id')) {
            $userIds[] = (int)$request->user_id;
            $userIds = array_values(array_unique($userIds));
        }

        $usersQuery = User::whereIn('id', $userIds);

        if ($search) {
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allUsers = $usersQuery->get();

        // Siapkan list percakapan per user
        $conversations = $allUsers->map(function($u) {
            $latestChat = Chat::where('user_id', $u->id)->orderBy('created_at', 'desc')->first();
            $unreadCount = Chat::where('user_id', $u->id)->where('sender', 'user')->where('is_read', 0)->count();
            $isUnreplied = $latestChat ? ($latestChat->sender === 'user') : false;

            return (object)[
                'user' => $u,
                'latestChat' => $latestChat,
                'unreadCount' => $unreadCount,
                'isUnreplied' => $isUnreplied,
                'lastActivity' => $latestChat ? $latestChat->created_at : $u->created_at
            ];
        })->sortByDesc('lastActivity');

        // Hitung total status
        $totalAll = $conversations->count();
        $totalUnreplied = $conversations->where('isUnreplied', true)->count();
        $totalReplied = $conversations->where('isUnreplied', false)->count();

        // Terapkan Filter Status (Semua / Belum Dibalas / Sudah Dibalas)
        if ($statusFilter === 'unreplied') {
            $conversations = $conversations->where('isUnreplied', true);
        } elseif ($statusFilter === 'replied') {
            $conversations = $conversations->where('isUnreplied', false);
        }

        // Tentukan user aktif yang sedang dibuka chat-nya
        $activeUserId = $request->get('user_id');
        if (!$activeUserId && $conversations->isNotEmpty()) {
            $activeUserId = $conversations->first()->user->id;
        }

        $activeUser = null;
        $messages = collect();

        if ($activeUserId) {
            $activeUser = User::find($activeUserId);
            if ($activeUser) {
                // Tandai pesan dari user ini sebagai sudah dibaca admin
                Chat::where('user_id', $activeUserId)
                    ->where('sender', 'user')
                    ->where('is_read', 0)
                    ->update(['is_read' => 1]);

                $messages = Chat::where('user_id', $activeUserId)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        // Ambil daftar seluruh user (role: user) untuk opsi "Mulai Chat Baru"
        $allUserOptions = User::where('role', 'user')->orderBy('name')->get();

        // Template balasan cepat admin
        $adminTemplates = [
            'Halo kak! Pesanan akan dikirim di hari yang sama jika checkout sebelum jam 15.00 WIB.',
            'Halo! Peminjaman buku maksimal 7 hari dan dapat diajukan pengembalian lewat menu Peminjaman Saya.',
            'Halo kak, pembayaran denda Anda telah kami verifikasi dan akun Anda telah aktif kembali.',
            'Halo, restock buku biasanya dilakukan secara berkala setiap awal bulan. Pantau terus katalog kami ya!',
            'Terima kasih atas saran Anda, judul buku tersebut akan kami pertimbangkan untuk pengadaan perpustakaan selanjutnya.'
        ];

        return view('admin.chats.index', compact(
            'conversations', 
            'activeUser', 
            'messages', 
            'statusFilter', 
            'totalAll', 
            'totalUnreplied', 
            'totalReplied', 
            'adminTemplates',
            'allUserOptions'
        ));
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        Chat::create([
            'user_id' => $request->user_id,
            'sender' => 'admin',
            'message' => trim($request->message),
            'is_read' => 0
        ]);

        // Kirim Notifikasi ke User bahwa Admin membalas pesan
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'Pesan Baru dari Admin 💬',
            'message' => 'Admin telah membalas pesan Anda: "' . \Illuminate\Support\Str::limit($request->message, 50) . '"',
            'link' => '/user/chat'
        ]);

        return redirect('/admin/chats?user_id=' . $request->user_id)->with('success', 'Pesan berhasil dikirim ke pelanggan.');
    }
}
