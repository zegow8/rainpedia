@extends('layouts.admin')

@section('title', 'Layanan Chat Pelanggan')

@section('content')
<style>
    @media (max-width: 868px) {
        .admin-chat-layout {
            grid-template-columns: 1fr !important;
            height: auto !important;
            min-height: auto !important;
        }
        .admin-chat-sidebar {
            max-height: 280px !important;
        }
        .admin-chat-main {
            height: 520px !important;
        }
    }
</style>

<div style="margin-bottom: 24px;">
    <h1 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">💬 Layanan Chat Pelanggan</h1>
    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">Kelola pesan masuk dan balas pertanyaan pelanggan perpustakaan secara interaktif.</p>
</div>

<!-- Layout Dua Kolom: Daftar Kontak (Kiri) & Kotak Obrolan (Kanan) -->
<div style="display: grid; grid-template-columns: 340px 1fr; gap: 20px; height: calc(100vh - 200px); min-height: 600px;" class="admin-chat-layout">
    
    <!-- KOLOM KIRI: DAFTAR USER & STATUS TABS -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.03);" class="admin-chat-sidebar">
        
        <!-- Header & Pencarian Kontak -->
        <div style="padding: 16px; border-bottom: 1px solid #e2e8f0; background: #fafafa;">
            <form action="/admin/chats" method="GET" style="margin-bottom: 12px;">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <div style="position: relative;">
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Cari nama atau email..." 
                           style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px 8px 32px; font-size: 0.85rem; outline: none; font-family: inherit;">
                    <span style="position: absolute; left: 10px; top: 9px; font-size: 0.85rem; color: #94a3b8;">🔍</span>
                </div>
            </form>

            <!-- TABS FILTER STATUS (SEMUA - BELUM DIBALAS - SUDAH DIBALAS) -->
            <div style="display: flex; gap: 4px; background: #f1f5f9; padding: 4px; border-radius: 8px;">
                <a href="/admin/chats?status=all{{ request('q') ? '&q='.request('q') : '' }}" 
                   style="flex: 1; text-align: center; font-size: 0.75rem; font-weight: 700; padding: 6px 4px; border-radius: 6px; text-decoration: none; transition: all 0.2s; {{ $statusFilter === 'all' ? 'background: white; color: #2563eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                    Semua ({{ $totalAll }})
                </a>
                <a href="/admin/chats?status=unreplied{{ request('q') ? '&q='.request('q') : '' }}" 
                   style="flex: 1.2; text-align: center; font-size: 0.75rem; font-weight: 700; padding: 6px 4px; border-radius: 6px; text-decoration: none; transition: all 0.2s; {{ $statusFilter === 'unreplied' ? 'background: #fee2e2; color: #dc2626; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                    Belum Dibalas ({{ $totalUnreplied }})
                </a>
                <a href="/admin/chats?status=replied{{ request('q') ? '&q='.request('q') : '' }}" 
                   style="flex: 1.2; text-align: center; font-size: 0.75rem; font-weight: 700; padding: 6px 4px; border-radius: 6px; text-decoration: none; transition: all 0.2s; {{ $statusFilter === 'replied' ? 'background: #ecfdf5; color: #059669; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                    Sudah Dibalas ({{ $totalReplied }})
                </a>
            </div>
        </div>

        <!-- List Kontak Percakapan -->
        <div style="flex: 1; overflow-y: auto;">
            @if($conversations->isEmpty())
                <div style="padding: 30px 16px; text-align: center; color: #94a3b8; font-size: 0.85rem;">
                    Tidak ada percakapan ditemukan.
                </div>
            @else
                @foreach($conversations as $item)
                    @php 
                        $isSelected = $activeUser && $activeUser->id === $item->user->id;
                    @endphp
                    <a href="/admin/chats?user_id={{ $item->user->id }}&status={{ $statusFilter }}{{ request('q') ? '&q='.request('q') : '' }}" 
                       style="display: block; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; text-decoration: none; transition: background 0.15s; {{ $isSelected ? 'background: #eff6ff; border-left: 4px solid #2563eb;' : 'background: white;' }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                            <strong style="color: #1e293b; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px;">
                                {{ $item->user->name }}
                            </strong>
                            <span style="font-size: 0.72rem; color: #94a3b8;">
                                {{ $item->latestChat ? $item->latestChat->created_at->diffForHumans(null, true) : '' }}
                            </span>
                        </div>
                        <div style="font-size: 0.8rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 6px;">
                            @if($item->latestChat)
                                <span style="font-weight: 600;">{{ $item->latestChat->sender === 'admin' ? 'Anda: ' : '' }}</span>
                                {{ $item->latestChat->message }}
                            @else
                                <em>Belum ada pesan</em>
                            @endif
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                @if($item->isUnreplied)
                                    <span style="background: #fee2e2; color: #ef4444; font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 10px;">
                                        ● Belum Dibalas
                                    </span>
                                @else
                                    <span style="background: #ecfdf5; color: #059669; font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 10px;">
                                        ✓ Sudah Dibalas
                                    </span>
                                @endif
                            </div>
                            @if($item->unreadCount > 0)
                                <span style="background: #ef4444; color: white; font-size: 0.68rem; font-weight: 800; padding: 2px 6px; border-radius: 10px;">
                                    {{ $item->unreadCount }} baru
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- KOLOM KANAN: KOTAK CHAT & BALASAN -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.03);" class="admin-chat-main">
        @if($activeUser)
            <!-- Topbar Chat Aktif -->
            <div style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; background: #fafafa; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #0f172a; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.05rem;">
                        {{ strtoupper(substr($activeUser->name, 0, 2)) }}
                    </div>
                    <div>
                        <strong style="color: #1e293b; font-size: 1rem; display: block;">{{ $activeUser->name }}</strong>
                        <span style="font-size: 0.8rem; color: #64748b;">{{ $activeUser->email }} • Bergabung {{ $activeUser->created_at->format('M Y') }}</span>
                    </div>
                </div>
                <div>
                    <a href="/admin/users" class="btn btn-outline btn-sm" style="font-size: 0.78rem;">Lihat Detail User</a>
                </div>
            </div>

            <!-- Template Balasan Cepat Admin -->
            <div style="padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">
                    ⚡ Balasan Cepat Admin (Klik untuk memasukkan):
                </div>
                <div style="display: flex; gap: 6px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: thin;">
                    @foreach($adminTemplates as $tmpl)
                        <button type="button" 
                                onclick="fillAdminReply('{{ addslashes($tmpl) }}')"
                                style="background: white; border: 1px solid #cbd5e1; border-radius: 16px; padding: 5px 12px; font-size: 0.78rem; color: #334155; font-weight: 500; cursor: pointer; white-space: nowrap; transition: all 0.15s; font-family: inherit;"
                                onmouseover="this.style.borderColor='#2563eb'; this.style.color='#2563eb'; this.style.background='#eff6ff';"
                                onmouseout="this.style.borderColor='#cbd5e1'; this.style.color='#334155'; this.style.background='white';">
                            {{ Str::limit($tmpl, 40) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Daftar Riwayat Pesan (Scrollable) -->
            <div id="adminChatMessages" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; background: #fdfdfd;">
                @if($messages->isEmpty())
                    <div style="text-align: center; margin: auto; color: #94a3b8; font-size: 0.85rem;">
                        Belum ada riwayat pesan dengan pengguna ini.
                    </div>
                @else
                    @php $lastDate = null; @endphp
                    @foreach($messages as $msg)
                        @php $msgDate = $msg->created_at->format('d M Y'); @endphp
                        @if($lastDate !== $msgDate)
                            <div style="text-align: center; margin: 8px 0;">
                                <span style="background: #f1f5f9; color: #64748b; font-size: 0.72rem; font-weight: 600; padding: 3px 10px; border-radius: 10px;">
                                    {{ $msgDate }}
                                </span>
                            </div>
                            @php $lastDate = $msgDate; @endphp
                        @endif

                        @if($msg->sender === 'user')
                            <!-- Pesan Pelanggan (Kiri) -->
                            <div style="align-self: flex-start; max-width: 75%; display: flex; flex-direction: column; align-items: flex-start;">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: #1e293b;">{{ $activeUser->name }}</span>
                                    <span style="font-size: 0.7rem; color: #94a3b8;">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                                <div style="background: white; color: #1e293b; border: 1px solid #cbd5e1; padding: 10px 16px; border-radius: 16px 16px 16px 4px; font-size: 0.9rem; line-height: 1.5; word-break: break-word; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                    {{ $msg->message }}
                                </div>
                            </div>
                        @else
                            <!-- Balasan Admin (Kanan) -->
                            <div style="align-self: flex-end; max-width: 75%; display: flex; flex-direction: column; align-items: flex-end;">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                    <span style="font-size: 0.7rem; color: #94a3b8;">{{ $msg->created_at->format('H:i') }}</span>
                                    <span style="background: #2563eb; color: white; font-size: 0.65rem; font-weight: 700; padding: 1px 6px; border-radius: 4px;">ADMIN</span>
                                </div>
                                <div style="background: #2563eb; color: white; padding: 10px 16px; border-radius: 16px 16px 4px 16px; font-size: 0.9rem; line-height: 1.5; word-break: break-word; box-shadow: 0 2px 4px rgba(37,99,235,0.15);">
                                    {{ $msg->message }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- Form Balas Pesan -->
            <div style="padding: 16px 20px; background: white; border-top: 1px solid #e2e8f0;">
                <form action="/admin/chats/send" method="POST" id="adminReplyForm" style="display: flex; gap: 12px; align-items: flex-end;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $activeUser->id }}">
                    <div style="flex: 1;">
                        <textarea name="message" 
                                  id="adminReplyInput" 
                                  rows="2" 
                                  required 
                                  placeholder="Ketik balasan untuk {{ $activeUser->name }}... (Tekan Enter untuk kirim)" 
                                  style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-family: inherit; font-size: 0.9rem; outline: none; resize: none; transition: border-color 0.2s;"
                                  onfocus="this.style.borderColor='#2563eb'"
                                  onblur="this.style.borderColor='#cbd5e1'"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 52px; padding: 0 24px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <span>Balas</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #94a3b8; padding: 40px; text-align: center;">
                <div style="font-size: 3.5rem; margin-bottom: 12px;">💬</div>
                <h3 style="color: #475569; font-size: 1.15rem; margin-bottom: 6px;">Pilih Percakapan Pelanggan</h3>
                <p style="font-size: 0.85rem; max-width: 340px; margin: 0; line-height: 1.5;">
                    Pilih salah satu pelanggan dari daftar di sebelah kiri untuk melihat riwayat pesan dan mengirim balasan.
                </p>
            </div>
        @endif
    </div>

</div>

<script>
    function fillAdminReply(text) {
        const input = document.getElementById('adminReplyInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    // Auto-scroll ke pesan terbawah
    document.addEventListener("DOMContentLoaded", function() {
        const chatBox = document.getElementById('adminChatMessages');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });

    // Enter kirim balasan (Shift+Enter untuk baris baru)
    const replyInput = document.getElementById('adminReplyInput');
    if (replyInput) {
        replyInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim() !== '') {
                    document.getElementById('adminReplyForm').submit();
                }
            }
        });
    }
</script>
@endsection
