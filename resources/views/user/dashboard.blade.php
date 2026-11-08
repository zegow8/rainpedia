@extends('layouts.user')

@section('title', 'Dashboard Saya')

@section('content')
<style>
    @media (max-width: 900px) {
        .dashboard-main-grid { grid-template-columns: 1fr !important; }
        .dashboard-status-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 8px !important; }
    }
    @media (max-width: 480px) {
        .dashboard-status-grid { grid-template-columns: repeat(2, 1fr) !important; }
    }
</style>

<div class="flex-between mb-4" style="flex-wrap:wrap; gap:16px;">
    <div>
        <h1 style="margin-bottom:4px;">Halo, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-muted" style="margin:0; font-size:0.95rem;">Selamat datang di Rainpedia • {{ date('l, d F Y') }}</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/user/catalog" class="btn btn-primary btn-sm">📚 Jelajahi Katalog Buku</a>
        <a href="/user/borrowings" class="btn btn-outline btn-sm">📋 Peminjaman Saya</a>
    </div>
</div>

<!-- ============ BANNER DENDA JIKA ADA ============ -->
@if($totalUnpaidFineAmount > 0)
<div class="alert alert-danger" style="margin-bottom:24px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div>
        <strong>⚠️ Anda memiliki denda keterlambatan belum dibayar!</strong>
        <div style="font-size:0.85rem; margin-top:2px;">
            Total tagihan denda: <strong style="font-size:1rem;">Rp {{ number_format($totalUnpaidFineAmount, 0, ',', '.') }}</strong>. Lunasi segera agar dapat meminjam buku kembali.
        </div>
    </div>
    <a href="/user/borrowings" class="btn btn-danger btn-sm">Bayar Denda Sekarang &rarr;</a>
</div>
@endif

<!-- ============ STATUS PESANAN & PEMINJAMAN LENGKAP ============ -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;" class="dashboard-main-grid">
    <!-- STATUS PESANAN USER -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">🛒 Riwayat Pembelian ({{ $totalOrders }} Pesanan)</h3>
            <a href="/user/transactions" class="btn btn-outline btn-sm">Semua Pesanan</a>
        </div>
        <div style="display:grid; grid-template-columns: repeat(6, 1fr); gap:6px; text-align:center;" class="dashboard-status-grid">
            <a href="/user/transactions?status=Waiting Confirmation" style="background:#ffedd5; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#c2410c;">{{ $ordWaiting }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#ea580c; margin-top:2px;">Menunggu ACC</div>
            </a>
            <a href="/user/transactions?status=Processing" style="background:#fef3c7; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#92400e;">{{ $ordProcessing }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#b45309; margin-top:2px;">Dikemas</div>
            </a>
            <a href="/user/transactions?status=Shipped" style="background:#e0e7ff; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#3730a3;">{{ $ordShipped }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#4338ca; margin-top:2px;">Dikirim</div>
            </a>
            <a href="/user/transactions?status=Completed" style="background:#dcfce7; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#166534;">{{ $ordCompleted }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#15803d; margin-top:2px;">Selesai</div>
            </a>
            <a href="/user/transactions?status=Cancelled" style="background:#f1f5f9; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#475569;">{{ $ordCancelled }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#64748b; margin-top:2px;">Batal</div>
            </a>
            <a href="/user/transactions?status=Rejected" style="background:#fee2e2; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#991b1b;">{{ $ordRejected }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#b91c1c; margin-top:2px;">Ditolak</div>
            </a>
        </div>
    </div>

    <!-- STATUS PEMINJAMAN USER -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">📚 Status Peminjaman ({{ $totalBorrows }} Total)</h3>
            <a href="/user/borrowings" class="btn btn-outline btn-sm">Semua Pinjaman</a>
        </div>
        <div style="display:grid; grid-template-columns: repeat(6, 1fr); gap:6px; text-align:center;" class="dashboard-status-grid">
            <a href="/user/borrowings?status=Pending" style="background:#fef3c7; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#92400e;">{{ $bwPending }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#b45309; margin-top:2px;">Pending</div>
            </a>
            <a href="/user/borrowings?status=Borrowed" style="background:#dbeafe; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#1e40af;">{{ $bwBorrowed }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#2563eb; margin-top:2px;">Dipinjam</div>
            </a>
            <a href="/user/borrowings?status=Overdue" style="background:#fee2e2; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#991b1b;">{{ $bwOverdue }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#b91c1c; margin-top:2px;">Terlambat</div>
            </a>
            <a href="/user/borrowings?status=Return Requested" style="background:#ffedd5; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#9a3412;">{{ $bwReturnReq }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#c2410c; margin-top:2px;">Minta Balik</div>
            </a>
            <a href="/user/borrowings?status=Returned" style="background:#dcfce7; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#166534;">{{ $bwReturned }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#15803d; margin-top:2px;">Kembali</div>
            </a>
            <a href="/user/borrowings?status=Rejected" style="background:#f1f5f9; padding:10px 2px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.2rem; font-weight:800; color:#475569;">{{ $bwRejected }}</div>
                <div style="font-size:0.65rem; font-weight:700; color:#64748b; margin-top:2px;">Ditolak</div>
            </a>
        </div>
    </div>
</div>

<!-- ============ BUKU YANG LAGI DIPINJAM (DENGAN COVER + TENGGAT WAKTU) ============ -->
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header flex-between">
        <div>
            <h3 class="panel-title" style="margin-bottom:2px;">📖 Buku yang Sedang Anda Pinjam (Aktif)</h3>
            <span class="text-muted" style="font-size:0.85rem;">Pastikan mengembalikan buku sebelum batas tenggat waktu.</span>
        </div>
        <a href="/user/borrowings" class="btn btn-outline btn-sm">Kelola Peminjaman</a>
    </div>

    @if($activeBorrowings->isEmpty())
        <div style="text-align:center; padding:32px; background:#f8fafc; border-radius:8px; border:1px dashed var(--border);">
            <div style="font-size:2rem; margin-bottom:8px;">📚</div>
            <p style="margin:0; font-weight:600; color:var(--text-main);">Saat ini Anda tidak sedang meminjam buku apapun.</p>
            <p class="text-muted" style="font-size:0.85rem; margin-top:4px; margin-bottom:14px;">Buku di Rainpedia bisa dipinjam 100% gratis maksimal 7 hari!</p>
            <a href="/user/catalog" class="btn btn-primary btn-sm">Pinjam Buku Sekarang</a>
        </div>
    @else
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:16px;">
            @foreach($activeBorrowings as $ab)
                @php
                    $isOverdue = $ab->status === 'Overdue';
                    $bg = $isOverdue ? '#fff5f5' : '#ffffff';
                    $borderColor = $isOverdue ? 'var(--danger)' : 'var(--border)';
                @endphp
                <div style="background:{{ $bg }}; border:1.5px solid {{ $borderColor }}; border-radius:10px; padding:16px; display:flex; gap:14px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <!-- Cover Buku -->
                    <div style="width:65px; height:92px; border-radius:6px; overflow:hidden; background:#eee; flex-shrink:0; border:1px solid var(--border);">
                        @if($ab->book && $ab->book->cover)
                            <img src="{{ asset('uploads/covers/' . $ab->book->cover) }}" alt="{{ $ab->book->title }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.8rem;">📖</div>
                        @endif
                    </div>
                    <!-- Info Buku & Tenggat -->
                    <div style="flex:1; min-width:0; display:flex; flex-direction:column; justify-content:space-between;">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                                <strong style="font-size:0.95rem; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                    {{ $ab->book->title ?? 'Buku Dihapus' }}
                                </strong>
                                <span class="status-badge {{ $isOverdue ? 'badge-overdue' : 'badge-borrowed' }}" style="font-size:0.7rem;">
                                    {{ $ab->status }}
                                </span>
                            </div>
                            <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">{{ $ab->book->author ?? '-' }}</div>
                        </div>

                        <div style="margin-top:8px; padding-top:8px; border-top:1px dashed var(--border); font-size:0.8rem;">
                            <div>📅 Mulai: <strong>{{ $ab->start_date ? date('d M Y', strtotime($ab->start_date)) : '-' }}</strong></div>
                            <div style="color: {{ $isOverdue ? 'var(--danger)' : 'var(--primary)' }}; font-weight:700; margin-top:2px;">
                                ⏰ Tenggat: {{ $ab->due_date ? date('d M Y', strtotime($ab->due_date)) : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- ============ ROW 2: BUKU YANG SUDAH DIKEMBALIKAN & DAFTAR DENDA ============ -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;">
    <!-- BUKU YANG SUDAH DIKEMBALIKAN -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">✅ Buku yang Sudah Dikembalikan</h3>
            <a href="/user/borrowings?status=Returned" style="font-size:0.85rem; font-weight:600;">Lihat Semua &rarr;</a>
        </div>
        @if($returnedBooks->isEmpty())
            <p class="text-muted" style="margin:0; font-size:0.85rem;">Belum ada riwayat buku yang selesai dikembalikan.</p>
        @else
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($returnedBooks as $rb)
                    <div style="display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid var(--border);">
                        <div style="width:36px; height:50px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0;">
                            @if($rb->book && $rb->book->cover)
                                <img src="{{ asset('uploads/covers/' . $rb->book->cover) }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%;">📖</div>
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $rb->book->title ?? 'Buku Dihapus' }}
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                Dipinjam: {{ $rb->start_date ? date('d/m/y', strtotime($rb->start_date)) : '-' }} s/d {{ $rb->due_date ? date('d/m/y', strtotime($rb->due_date)) : '-' }}
                            </div>
                        </div>
                        <span class="status-badge" style="background:#dcfce7; color:#166534; font-size:0.7rem;">Selesai</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- DAFTAR DENDA -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">💰 Daftar Denda Keterlambatan</h3>
            <span style="font-size:0.85rem; font-weight:700; color:{{ $totalUnpaidFineAmount > 0 ? 'var(--danger)' : 'var(--success)' }}">
                {{ $totalUnpaidFineAmount > 0 ? 'Total: Rp ' . number_format($totalUnpaidFineAmount, 0, ',', '.') : 'Bebas Denda 🎉' }}
            </span>
        </div>
        @if($unpaidFines->isEmpty())
            <div style="text-align:center; padding:24px; background:#f0fdf4; border-radius:8px; border:1px solid #bbf7d0;">
                <div style="font-size:1.8rem; margin-bottom:4px;">🎉</div>
                <div style="font-weight:700; color:var(--success); font-size:0.95rem;">Tidak Ada Denda!</div>
                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Terima kasih telah mengembalikan buku tepat pada waktunya.</div>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($unpaidFines as $fn)
                    <div style="background:#fff5f5; border:1px solid #fecaca; border-radius:8px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:700; font-size:0.9rem; color:#991b1b;">
                                {{ $fn->borrowing->book->title ?? 'Peminjaman Buku' }}
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                                Keterlambatan: <strong>{{ $fn->borrowing->late_days ?? 0 }} Hari</strong>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:800; color:var(--danger); font-size:0.95rem;">
                                Rp {{ number_format($fn->amount, 0, ',', '.') }}
                            </div>
                            <form action="/user/borrowings/pay-fine" method="POST" style="margin-top:4px;">
                                @csrf
                                <input type="hidden" name="fine_id" value="{{ $fn->id }}">
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:3px 10px; font-size:0.75rem;" onclick="return confirm('Bayar denda ini sekarang?')">Bayar</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- ============ ROW 3: AKTIFITAS & NOTIFIKASI TERBARU ============ -->
<div class="panel">
    <div class="panel-header flex-between">
        <h3 class="panel-title">🔔 Notifikasi &amp; Aktifitas Terbaru</h3>
        <a href="/user/notifications" style="font-size:0.85rem; font-weight:600;">Lihat Semua Notifikasi &rarr;</a>
    </div>
    @if($recentNotifications->isEmpty())
        <p class="text-muted" style="margin:0; font-size:0.85rem;">Belum ada notifikasi aktifitas baru.</p>
    @else
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($recentNotifications as $notif)
                <div style="padding:10px 14px; border-radius:8px; border:1px solid var(--border); {{ $notif->is_read ? 'background:#ffffff;' : 'background:#eff6ff; border-color:#bfdbfe;' }}; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <strong style="color:var(--primary); font-size:0.9rem;">{{ $notif->title }}</strong>
                        <p style="margin:2px 0 0; font-size:0.825rem; color:var(--text-main);">{{ $notif->message }}</p>
                    </div>
                    <div style="text-align:right; font-size:0.75rem; color:var(--text-muted); white-space:nowrap; margin-left:16px;">
                        {{ date('d M, H:i', strtotime($notif->created_at)) }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
