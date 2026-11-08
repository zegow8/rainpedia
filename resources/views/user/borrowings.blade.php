@extends('layouts.user')

@section('title', 'Peminjaman Saya')

@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Peminjaman Saya</h1>
        <p class="text-muted" style="margin:0;">Pantau riwayat peminjaman buku, waktu tenggat, dan status pengembalian</p>
    </div>
    <a href="/user/catalog" class="btn btn-primary btn-sm">+ Pinjam Buku Baru</a>
</div>

<!-- ============ STATUS TABS ============ -->
<div class="status-tabs">
    <a href="/user/borrowings" class="status-tab {{ !request('status') ? 'active' : '' }}">
        Semua ({{ $counts['all'] }})
    </a>
    <a href="/user/borrowings?status=Pending" class="status-tab {{ request('status') === 'Pending' ? 'active' : '' }}">
        Pending ({{ $counts['Pending'] }})
    </a>
    <a href="/user/borrowings?status=Borrowed" class="status-tab {{ request('status') === 'Borrowed' ? 'active' : '' }}">
        Sedang Dipinjam ({{ $counts['Borrowed'] }})
    </a>
    <a href="/user/borrowings?status=Overdue" class="status-tab {{ request('status') === 'Overdue' ? 'active' : '' }}">
        Terlambat ({{ $counts['Overdue'] }})
    </a>
    <a href="/user/borrowings?status=Return Requested" class="status-tab {{ request('status') === 'Return Requested' ? 'active' : '' }}">
        Pengajuan Kembali ({{ $counts['Return Requested'] }})
    </a>
    <a href="/user/borrowings?status=Returned" class="status-tab {{ request('status') === 'Returned' ? 'active' : '' }}">
        Sudah Kembali ({{ $counts['Returned'] }})
    </a>
    <a href="/user/borrowings?status=Rejected" class="status-tab {{ request('status') === 'Rejected' ? 'active' : '' }}">
        Ditolak ({{ $counts['Rejected'] }})
    </a>
</div>

@if($borrows->isEmpty())
    <div class="panel text-center" style="padding:56px 32px;">
        <div style="font-size:3rem; margin-bottom:12px;">📚</div>
        <h3>Belum Ada Data Peminjaman</h3>
        <p class="text-muted">Tidak ditemukan data peminjaman untuk filter status ini.</p>
        <a href="/user/catalog" class="btn btn-primary mt-4">Jelajahi Katalog Buku</a>
    </div>
@else
    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach($borrows as $b)
            @php
                $isOverdue = $b->status === 'Overdue';
                $cardBg = $isOverdue ? '#fff5f5' : 'var(--white)';
                $cardBorder = $isOverdue ? 'var(--danger)' : 'var(--border)';
            @endphp
            <div style="background:{{ $cardBg }}; border:1.5px solid {{ $cardBorder }}; border-radius:10px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <div style="display:flex; gap:18px; align-items:flex-start; flex-wrap:wrap;">
                    <!-- Cover Buku -->
                    <div style="width:70px; height:100px; background:#eee; border-radius:6px; overflow:hidden; flex-shrink:0; border:1px solid var(--border);">
                        @if($b->book && $b->book->cover)
                            <img src="{{ asset('uploads/covers/' . $b->book->cover) }}" alt="{{ $b->book->title }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:2rem;">📚</div>
                        @endif
                    </div>

                    <!-- Informasi Buku & Tanggal -->
                    <div style="flex:1; min-width:0;">
                        <div class="flex-between" style="margin-bottom:4px;">
                            <strong style="font-size:1.1rem; color:var(--text-main);">{{ $b->book->title ?? 'Buku Dihapus' }}</strong>
                            <div>
                                @if($b->status === 'Pending')
                                    <span class="status-badge" style="background:#fef3c7; color:#92400e;">Menunggu ACC Admin</span>
                                @elseif($b->status === 'Borrowed')
                                    <span class="status-badge" style="background:#dbeafe; color:#1e40af;">Sedang Dipinjam</span>
                                @elseif($b->status === 'Overdue')
                                    <span class="status-badge" style="background:#fee2e2; color:#991b1b;">Terlambat</span>
                                @elseif($b->status === 'Return Requested')
                                    <span class="status-badge" style="background:#ffedd5; color:#9a3412;">Menunggu Konfirmasi Kembali</span>
                                @elseif($b->status === 'Returned')
                                    <span class="status-badge" style="background:#dcfce7; color:#166534;">Telah Dikembalikan</span>
                                @elseif($b->status === 'Rejected')
                                    <span class="status-badge" style="background:#f1f5f9; color:#475569;">Peminjaman Ditolak</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-muted" style="font-size:0.85rem;">Penulis: {{ $b->book->author ?? '-' }}</div>

                        <!-- Periode Waktu Peminjaman -->
                        <div style="display:flex; gap:20px; font-size:0.85rem; color:var(--text-muted); margin-top:10px; background:#f8fafc; padding:8px 12px; border-radius:6px; flex-wrap:wrap;">
                            <span>⏱️ Durasi: <strong>{{ $b->duration_days }} Hari</strong></span>
                            <span>📅 Mulai: <strong>{{ $b->start_date ? date('d M Y', strtotime($b->start_date)) : 'Menunggu ACC' }}</strong></span>
                            <span style="color: {{ $isOverdue ? 'var(--danger)' : 'inherit' }};">
                                ⏰ Tenggat: <strong>{{ $b->due_date ? date('d M Y', strtotime($b->due_date)) : '-' }}</strong>
                            </span>
                            @if($b->return_request_date)
                                <span>📦 Ajukan Kembali: <strong>{{ date('d M Y', strtotime($b->return_request_date)) }}</strong></span>
                            @endif
                        </div>
                        
                        <!-- Notifikasi Denda -->
                        @if($b->fine && $b->fine->amount > 0)
                            <div style="margin-top:10px; font-size:0.85rem; padding:8px 12px; border-radius:6px; background:{{ $b->fine->status === 'Paid' ? '#dcfce7' : '#fee2e2' }}; color:{{ $b->fine->status === 'Paid' ? '#166534' : '#991b1b' }}; display:flex; justify-content:space-between; align-items:center;">
                                <span>
                                    💰 Tagihan Denda Keterlambatan: <strong>Rp {{ number_format($b->fine->amount, 0, ',', '.') }}</strong> ({{ $b->fine->status }})
                                </span>
                                @if($b->fine->status === 'Unpaid')
                                    <form action="/user/borrowings/pay-fine" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="fine_id" value="{{ $b->fine->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bayar denda ini sekarang?')">Bayar Denda</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Aksi Pengembalian -->
                @if($b->status === 'Borrowed' || $b->status === 'Overdue')
                <div style="margin-top:14px; padding-top:14px; border-top:1px solid var(--border); display:flex; justify-content:flex-end;">
                    <form action="/user/borrowings/return" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="borrowing_id" value="{{ $b->id }}">
                        <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Apakah Anda yakin ingin mengajukan pengembalian buku ini?')">
                            📦 Ajukan Pengembalian Buku
                        </button>
                    </form>
                </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
@endsection
