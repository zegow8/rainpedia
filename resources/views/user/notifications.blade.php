@extends('layouts.user')

@section('title', 'Notifikasi Saya')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">🔔 Notifikasi</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Pembaruan terkini terkait transaksi pembelian, aktivitas peminjaman buku, dan pesan dari admin.</p>
    </div>

    <!-- TABS KATEGORI NOTIFIKASI -->
    <div style="display: flex; gap: 8px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 4px; border-bottom: 1px solid var(--border); -webkit-overflow-scrolling: touch;">
        <a href="/user/notifications?category=all" 
           style="padding: 10px 16px; font-size: 0.88rem; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $category === 'all' ? 'var(--primary)' : 'transparent' }}; color: {{ $category === 'all' ? 'var(--primary)' : 'var(--text-muted)' }}; transition: all 0.2s; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
            <span>🔔 Semua</span>
            <span style="font-size: 0.75rem; padding: 2px 7px; border-radius: 10px; background: {{ $category === 'all' ? '#eff6ff' : '#f1f5f9' }}; color: {{ $category === 'all' ? '#2563eb' : '#64748b' }};">
                {{ $counts['all'] }}
            </span>
        </a>

        <a href="/user/notifications?category=order" 
           style="padding: 10px 16px; font-size: 0.88rem; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $category === 'order' ? 'var(--primary)' : 'transparent' }}; color: {{ $category === 'order' ? 'var(--primary)' : 'var(--text-muted)' }}; transition: all 0.2s; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
            <span>🛍️ Pembelian</span>
            <span style="font-size: 0.75rem; padding: 2px 7px; border-radius: 10px; background: {{ $category === 'order' ? '#eff6ff' : '#f1f5f9' }}; color: {{ $category === 'order' ? '#2563eb' : '#64748b' }};">
                {{ $counts['order'] }}
            </span>
        </a>

        <a href="/user/notifications?category=borrow" 
           style="padding: 10px 16px; font-size: 0.88rem; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $category === 'borrow' ? 'var(--primary)' : 'transparent' }}; color: {{ $category === 'borrow' ? 'var(--primary)' : 'var(--text-muted)' }}; transition: all 0.2s; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
            <span>📖 Pinjaman</span>
            <span style="font-size: 0.75rem; padding: 2px 7px; border-radius: 10px; background: {{ $category === 'borrow' ? '#eff6ff' : '#f1f5f9' }}; color: {{ $category === 'borrow' ? '#2563eb' : '#64748b' }};">
                {{ $counts['borrow'] }}
            </span>
        </a>

        <a href="/user/notifications?category=chat" 
           style="padding: 10px 16px; font-size: 0.88rem; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $category === 'chat' ? 'var(--primary)' : 'transparent' }}; color: {{ $category === 'chat' ? 'var(--primary)' : 'var(--text-muted)' }}; transition: all 0.2s; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
            <span>💬 Pesan dari Admin</span>
            <span style="font-size: 0.75rem; padding: 2px 7px; border-radius: 10px; background: {{ $category === 'chat' ? '#eff6ff' : '#f1f5f9' }}; color: {{ $category === 'chat' ? '#2563eb' : '#64748b' }};">
                {{ $counts['chat'] }}
            </span>
        </a>
    </div>

    <!-- DAFTAR NOTIFIKASI -->
    <div class="panel" style="padding: 0; overflow: hidden; background: white; border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        @if($notifs->isEmpty())
            <div style="padding: 56px 20px; text-align: center; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 12px;">📭</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Tidak Ada Notifikasi</h3>
                <p style="font-size: 0.85rem; max-width: 360px; margin: 0 auto; line-height: 1.5;">
                    @if($category === 'order')
                        Belum ada notifikasi terkait transaksi pembelian buku.
                    @elseif($category === 'borrow')
                        Belum ada notifikasi terkait status peminjaman buku Anda.
                    @elseif($category === 'chat')
                        Belum ada notifikasi pesan baru dari admin perpustakaan.
                    @else
                        Kotak notifikasi Anda saat ini masih kosong.
                    @endif
                </p>
            </div>
        @else
            @foreach($notifs as $n)
                @php
                    $isChat = str_contains(strtolower($n->link ?? ''), 'chat') || str_contains(strtolower($n->title), 'pesan baru');
                    $isOrder = str_contains(strtolower($n->link ?? ''), 'transaction') || str_contains(strtolower($n->title), 'pesanan') || str_contains(strtolower($n->title), 'pembelian');
                    $isBorrow = str_contains(strtolower($n->link ?? ''), 'borrow') || str_contains(strtolower($n->title), 'pinjam') || str_contains(strtolower($n->title), 'pengembalian');

                    $icon = '🔔';
                    $badgeText = 'Umum';
                    $badgeBg = '#f1f5f9';
                    $badgeColor = '#475569';

                    if ($isOrder) {
                        $icon = '🛍️';
                        $badgeText = 'Pembelian';
                        $badgeBg = '#e0e7ff';
                        $badgeColor = '#3730a3';
                    } elseif ($isBorrow) {
                        $icon = '📖';
                        $badgeText = 'Peminjaman';
                        $badgeBg = '#fef3c7';
                        $badgeColor = '#92400e';
                    } elseif ($isChat) {
                        $icon = '💬';
                        $badgeText = 'Pesan Admin';
                        $badgeBg = '#dbeafe';
                        $badgeColor = '#1e40af';
                    }
                @endphp

                <div style="padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; gap: 16px; align-items: flex-start; transition: background 0.15s; {{ $n->is_read ? 'background: white;' : 'background: #f8fafc;' }}">
                    <!-- Ikon Kategori -->
                    <div style="font-size: 1.5rem; width: 42px; height: 42px; border-radius: 10px; background: {{ $badgeBg }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        {{ $icon }}
                    </div>

                    <!-- Detail Notifikasi -->
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">
                                    {{ $badgeText }}
                                </span>
                                <strong style="font-size: 0.95rem; color: var(--text-main); word-break: break-word;">{{ $n->title }}</strong>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap;">
                                {{ date('d M Y, H:i', strtotime($n->created_at)) }}
                            </span>
                        </div>

                        <p style="margin: 0 0 10px 0; font-size: 0.88rem; color: #475569; line-height: 1.5;">
                            {{ $n->message }}
                        </p>

                        @if($n->link)
                            <div>
                                <a href="{{ $n->link }}" class="btn btn-outline btn-sm" style="font-size: 0.78rem; padding: 4px 12px;">
                                    Lihat Selengkapnya &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- PAGINATION NOTIFIKASI -->
    <div style="margin-top: 16px;">
        {{ $notifs->links() }}
    </div>

</div>
@endsection
