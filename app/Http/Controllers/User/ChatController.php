<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Tandai pesan dari admin sebagai terbaca (is_read = 1)
        Chat::where('user_id', $userId)
            ->where('sender', 'admin')
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $messages = Chat::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Hitung pesan berturut-turut yang dikirim user sejak balasan terakhir dari admin
        $latestAdminMsg = Chat::where('user_id', $userId)
            ->where('sender', 'admin')
            ->orderBy('created_at', 'desc')
            ->first();

        $consecutiveUserMsgs = Chat::where('user_id', $userId)
            ->where('sender', 'user')
            ->when($latestAdminMsg, function($q) use ($latestAdminMsg) {
                $q->where('id', '>', $latestAdminMsg->id);
            })
            ->count();

        $maxConsecutive = 3;
        $isLimitReached = $consecutiveUserMsgs >= $maxConsecutive;
        $remainingQuota = max(0, $maxConsecutive - $consecutiveUserMsgs);

        // Template pertanyaan cepat untuk user
        $templates = [
            'Apakah pesanan buku bisa dikirim hari ini?',
            'Bagaimana prosedur pengembalian buku pinjaman?',
            'Saya sudah bayar denda keterlambatan, tolong verifikasinya ya min.',
            'Kapan stok buku yang kosong akan restock kembali?',
            'Apakah saya bisa request judul buku baru untuk perpustakaan?'
        ];

        return view('user.chat', compact(
            'messages', 
            'templates', 
            'consecutiveUserMsgs', 
            'maxConsecutive', 
            'isLimitReached', 
            'remainingQuota'
        ));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userId = Auth::id();

        // Validasi Anti-Spam: Cek apakah user sudah mengirim 3 pesan berturut-turut tanpa balasan admin
        $latestAdminMsg = Chat::where('user_id', $userId)
            ->where('sender', 'admin')
            ->orderBy('created_at', 'desc')
            ->first();

        $consecutiveUserMsgs = Chat::where('user_id', $userId)
            ->where('sender', 'user')
            ->when($latestAdminMsg, function($q) use ($latestAdminMsg) {
                $q->where('id', '>', $latestAdminMsg->id);
            })
            ->count();

        if ($consecutiveUserMsgs >= 3) {
            return back()->with('error', 'Anda telah mencapai batas maksimal 3 pesan berturut-turut. Harap tunggu tanggapan dari Administrator sebelum mengirim pesan baru.');
        }

        Chat::create([
            'user_id' => $userId,
            'sender' => 'user',
            'message' => trim($request->message),
            'is_read' => 0
        ]);

        return redirect('/user/chat')->with('success', 'Pesan berhasil dikirim ke admin.');
    }
}
