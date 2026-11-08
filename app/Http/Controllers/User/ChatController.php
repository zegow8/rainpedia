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

        // Template pertanyaan cepat untuk user
        $templates = [
            'Apakah pesanan buku bisa dikirim hari ini?',
            'Bagaimana prosedur pengembalian buku pinjaman?',
            'Saya sudah bayar denda keterlambatan, tolong verifikasinya ya min.',
            'Kapan stok buku yang kosong akan restock kembali?',
            'Apakah saya bisa request judul buku baru untuk perpustakaan?'
        ];

        return view('user.chat', compact('messages', 'templates'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        Chat::create([
            'user_id' => Auth::id(),
            'sender' => 'user',
            'message' => trim($request->message),
            'is_read' => 0
        ]);

        return redirect('/user/chat')->with('success', 'Pesan berhasil dikirim ke admin.');
    }
}
