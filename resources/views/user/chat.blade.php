@extends('layouts.user')

@section('title', 'Tanya Admin Perpustakaan')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">💬 Tanya Admin</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Punya pertanyaan seputar peminjaman, pembelian, atau denda? Tanyakan langsung ke admin kami.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; color: #475569;">
                Kuota Pesan: <strong style="color: {{ $isLimitReached ? '#dc2626' : '#2563eb' }};">{{ $remainingQuota }}/{{ $maxConsecutive }}</strong>
            </div>
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: #047857; display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                Admin Online
            </div>
        </div>
    </div>

    <!-- Panel Chat Utama -->
    <div class="panel user-chat-panel" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 650px; background: white; border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        
        <!-- Header Chat -->
        <div style="padding: 14px 16px; border-bottom: 1px solid var(--border); background: #f8fafc; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">
                    RP
                </div>
                <div>
                    <strong style="color: var(--text-main); font-size: 0.95rem; display: block;">Customer Support Rainpedia</strong>
                    <span style="font-size: 0.8rem; color: #10b981; font-weight: 600;">● Aktif Membalas Pertanyaan</span>
                </div>
            </div>
            <span style="font-size: 0.8rem; color: var(--text-muted);">ID Pengguna: #{{ auth()->user()->id }}</span>
        </div>

        <!-- Template Pertanyaan Cepat -->
        <div style="padding: 12px 20px; background: #fafafa; border-bottom: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                    ⚡ Pertanyaan Cepat:
                </div>
                @if($isLimitReached)
                    <span style="font-size: 0.75rem; color: #dc2626; font-weight: 600;">(Nonaktif sementara: menunggu balasan admin)</span>
                @endif
            </div>
            <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: thin; -webkit-overflow-scrolling: touch;">
                @foreach($templates as $tpl)
                    <button type="button" 
                            class="template-chip" 
                            @if(!$isLimitReached) onclick="insertTemplate('{{ addslashes($tpl) }}')" @endif
                            style="background: white; border: 1px solid #cbd5e1; border-radius: 20px; padding: 6px 14px; font-size: 0.8rem; color: #334155; font-weight: 500; cursor: {{ $isLimitReached ? 'not-allowed' : 'pointer' }}; white-space: nowrap; transition: all 0.15s; font-family: inherit; opacity: {{ $isLimitReached ? '0.5' : '1' }};">
                        {{ $tpl }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Kotak Percakapan (Scrollable) -->
        <div id="chatMessages" style="flex: 1; padding: 24px 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; background: #fdfdfd;">
            @if($messages->isEmpty())
                <div style="text-align: center; margin: auto; padding: 40px 20px; color: var(--text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 12px;">👋</div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Mulai Percakapan</h3>
                    <p style="font-size: 0.85rem; max-width: 400px; margin: 0 auto; line-height: 1.5;">
                        Belum ada riwayat pesan. Silakan pilih salah satu pertanyaan cepat di atas atau ketik pesan langsung ke admin perpustakaan.
                    </p>
                </div>
            @else
                @php $lastDate = null; @endphp
                @foreach($messages as $msg)
                    @php 
                        $msgDate = $msg->created_at->format('d M Y');
                    @endphp
                    @if($lastDate !== $msgDate)
                        <div style="text-align: center; margin: 10px 0;">
                            <span style="background: #f1f5f9; color: #64748b; font-size: 0.75rem; font-weight: 600; padding: 4px 12px; border-radius: 12px;">
                                {{ $msgDate }}
                            </span>
                        </div>
                        @php $lastDate = $msgDate; @endphp
                    @endif

                    @if($msg->sender === 'user')
                        <!-- Pesan User (Kanan) -->
                        <div style="align-self: flex-end; max-width: 75%; display: flex; flex-direction: column; align-items: flex-end;">
                            <div style="background: #2563eb; color: white; padding: 12px 18px; border-radius: 18px 18px 4px 18px; font-size: 0.92rem; line-height: 1.5; word-break: break-word; box-shadow: 0 2px 4px rgba(37,99,235,0.15);">
                                {{ $msg->message }}
                            </div>
                            <span style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px; padding-right: 4px;">
                                Anda • {{ $msg->created_at->format('H:i') }}
                            </span>
                        </div>
                    @else
                        <!-- Pesan Admin (Kiri) -->
                        <div style="align-self: flex-start; max-width: 75%; display: flex; flex-direction: column; align-items: flex-start;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                <span style="background: #1e293b; color: white; font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;">ADMIN</span>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">Admin Perpustakaan</span>
                            </div>
                            <div style="background: white; color: #1e293b; border: 1px solid #e2e8f0; padding: 12px 18px; border-radius: 18px 18px 18px 4px; font-size: 0.92rem; line-height: 1.5; word-break: break-word; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                                {{ $msg->message }}
                            </div>
                            <span style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px; padding-left: 4px;">
                                {{ $msg->created_at->format('H:i') }}
                            </span>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Banner Peringatan Batas Pesan Tercapai -->
        @if($isLimitReached)
            <div style="padding: 12px 20px; background: #fffbeb; border-top: 1px solid #fef3c7; border-bottom: 1px solid #fef3c7; color: #92400e; font-size: 0.85rem; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.2rem;">⏳</span>
                <div>
                    <strong>Batas 3 Pesan Tercapai:</strong> Anda telah mengirim {{ $consecutiveUserMsgs }} pesan berturut-turut. Mohon menunggu tanggapan atau balasan dari admin perpustakaan terlebih dahulu sebelum mengirim pesan berikutnya.
                </div>
            </div>
        @endif

        <!-- Input Box -->
        <div style="padding: 16px 20px; background: white; border-top: 1px solid var(--border);">
            <form action="/user/chat/send" method="POST" id="chatForm" style="display: flex; gap: 12px; align-items: flex-end;">
                @csrf
                <div style="flex: 1;">
                    <textarea name="message" 
                              id="chatInput" 
                              rows="2" 
                              required 
                              {{ $isLimitReached ? 'disabled' : '' }}
                              placeholder="{{ $isLimitReached ? 'Batas maksimal 3 pesan tercapai. Menunggu balasan dari admin...' : 'Ketik pertanyaan atau pesan Anda untuk admin...' }}" 
                              style="width: 100%; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; font-family: inherit; font-size: 0.92rem; outline: none; resize: none; transition: border-color 0.2s; {{ $isLimitReached ? 'background: #f1f5f9; cursor: not-allowed;' : '' }}"
                              onfocus="this.style.borderColor='var(--primary)'"
                              onblur="this.style.borderColor='var(--border)'"></textarea>
                </div>
                <button type="submit" 
                        class="btn btn-primary" 
                        {{ $isLimitReached ? 'disabled' : '' }}
                        style="height: 54px; padding: 0 24px; font-weight: 700; display: flex; align-items: center; gap: 8px; {{ $isLimitReached ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
                    <span>Kirim</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>

    </div>
</div>

<style>
    @media (max-width: 768px) {
        .user-chat-panel {
            height: calc(100vh - 200px) !important;
            min-height: 480px !important;
            border-radius: 12px !important;
        }
        .user-chat-panel button[type="submit"] {
            padding: 0 16px !important;
        }
    }
    .template-chip:hover {
        background: #eff6ff !important;
        border-color: #93c5fd !important;
        color: #1d4ed8 !important;
    }
</style>

<script>
    function insertTemplate(text) {
        const input = document.getElementById('chatInput');
        if (input && !input.disabled) {
            input.value = text;
            input.focus();
        }
    }

    // Auto-scroll ke pesan terbawah saat halaman dibuka
    document.addEventListener("DOMContentLoaded", function() {
        const chatContainer = document.getElementById('chatMessages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });

    // Enter kirim pesan (Shift+Enter untuk baris baru)
    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!this.disabled && this.value.trim() !== '') {
                    document.getElementById('chatForm').submit();
                }
            }
        });
    }
</script>
@endsection
