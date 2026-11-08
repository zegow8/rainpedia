@extends('layouts.admin')
@section('title', 'Data Peminjaman')
@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Data Peminjaman Buku</h1>
        <p class="text-muted" style="margin:0;">Kelola permohonan pinjam, tenggat waktu, dan status peminjaman buku</p>
    </div>
</div>

<!-- ============ TABS STATUS ============ -->
<div class="status-tabs">
    <a href="/admin/borrowings" class="status-tab {{ !request('status') ? 'active' : '' }}">
        Semua ({{ $counts['all'] }})
    </a>
    <a href="/admin/borrowings?status=Pending" class="status-tab {{ request('status') === 'Pending' ? 'active' : '' }}">
        Pending ({{ $counts['Pending'] }})
    </a>
    <a href="/admin/borrowings?status=Borrowed" class="status-tab {{ request('status') === 'Borrowed' ? 'active' : '' }}">
        Sedang Dipinjam ({{ $counts['Borrowed'] }})
    </a>
    <a href="/admin/borrowings?status=Overdue" class="status-tab {{ request('status') === 'Overdue' ? 'active' : '' }}">
        Terlambat ({{ $counts['Overdue'] }})
    </a>
    <a href="/admin/borrowings?status=Return Requested" class="status-tab {{ request('status') === 'Return Requested' ? 'active' : '' }}">
        Minta Kembali ({{ $counts['Return Requested'] }})
    </a>
    <a href="/admin/borrowings?status=Returned" class="status-tab {{ request('status') === 'Returned' ? 'active' : '' }}">
        Sudah Kembali ({{ $counts['Returned'] }})
    </a>
    <a href="/admin/borrowings?status=Rejected" class="status-tab {{ request('status') === 'Rejected' ? 'active' : '' }}">
        Ditolak ({{ $counts['Rejected'] }})
    </a>
</div>

<!-- ============ TABEL DATA PEMINJAMAN ============ -->
<div class="panel" style="padding:0; overflow:hidden;">
    <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-size:0.85rem; color:var(--text-muted);">
        Menampilkan <strong>{{ count($borrows) }}</strong> peminjaman
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:70px;">Cover</th>
                    <th>Buku yang Dipinjam</th>
                    <th>Peminjam (User)</th>
                    <th>Periode Peminjaman (Waktu)</th>
                    <th>Status & Denda</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrows as $b)
                <tr>
                    <!-- Preview Cover Buku -->
                    <td style="vertical-align:top;">
                        <div style="width:50px; height:70px; border-radius:6px; overflow:hidden; background:#eee; border:1px solid var(--border); box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                            @if($b->book && $b->book->cover)
                                <img src="{{ asset('uploads/covers/' . $b->book->cover) }}" alt="{{ $b->book->title }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.5rem;">📚</div>
                            @endif
                        </div>
                    </td>

                    <!-- Detail Buku -->
                    <td style="vertical-align:top;">
                        <strong style="font-size:0.95rem;">{{ $b->book->title ?? 'Buku Dihapus' }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Penulis: {{ $b->book->author ?? '-' }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Sisa Stok Pinjam: {{ $b->book->stock_borrow ?? 0 }}</div>
                    </td>

                    <!-- Peminjam -->
                    <td style="vertical-align:top;">
                        <strong>{{ $b->user->name ?? 'User Terhapus' }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $b->user->email ?? '-' }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">HP: {{ $b->user->phone ?? '-' }}</div>
                    </td>

                    <!-- Periode Waktu Peminjaman: Dari Kapan - Sampai Kapan -->
                    <td style="vertical-align:top; min-width:220px;">
                        <div style="background:#f8fafc; border:1px solid var(--border); border-radius:6px; padding:8px 12px; font-size:0.82rem;">
                            <div>⏱️ Durasi Diajukan: <strong>{{ $b->duration_days }} Hari</strong></div>
                            <div style="margin-top:4px;">
                                📅 Mulai: 
                                <strong>{{ $b->start_date ? date('d M Y', strtotime($b->start_date)) : 'Belum dimulai (Pending)' }}</strong>
                            </div>
                            <div style="margin-top:2px; color: {{ $b->status === 'Overdue' ? 'var(--danger)' : 'inherit' }}; font-weight:600;">
                                ⏰ Tenggat: 
                                <strong>{{ $b->due_date ? date('d M Y', strtotime($b->due_date)) : '-' }}</strong>
                            </div>
                            @if($b->return_request_date)
                                <div style="margin-top:2px; font-size:0.75rem; color:var(--text-muted);">
                                    📦 Tgl Minta Kembali: {{ date('d M Y', strtotime($b->return_request_date)) }}
                                </div>
                            @endif
                        </div>
                    </td>

                    <!-- Status & Denda -->
                    <td style="vertical-align:top;">
                        @if($b->status === 'Pending')
                            <span class="status-badge" style="background:#fef3c7; color:#92400e;">Menunggu ACC</span>
                        @elseif($b->status === 'Borrowed')
                            <span class="status-badge" style="background:#dbeafe; color:#1e40af;">Sedang Dipinjam</span>
                        @elseif($b->status === 'Overdue')
                            <span class="status-badge" style="background:#fee2e2; color:#991b1b;">Terlambat</span>
                        @elseif($b->status === 'Return Requested')
                            <span class="status-badge" style="background:#ffedd5; color:#9a3412;">Pengajuan Kembali</span>
                        @elseif($b->status === 'Returned')
                            <span class="status-badge" style="background:#dcfce7; color:#166534;">Dikembalikan</span>
                        @elseif($b->status === 'Rejected')
                            <span class="status-badge" style="background:#f1f5f9; color:#475569;">Ditolak</span>
                        @else
                            <span class="status-badge">{{ $b->status }}</span>
                        @endif

                        @if($b->fine && $b->fine->amount > 0)
                            <div style="margin-top:6px; font-size:0.75rem; padding:4px 8px; border-radius:4px; background:{{ $b->fine->status === 'Paid' ? '#dcfce7' : '#fee2e2' }}; color:{{ $b->fine->status === 'Paid' ? '#166534' : '#991b1b' }}; font-weight:700;">
                                Denda: Rp {{ number_format($b->fine->amount, 0, ',', '.') }} ({{ $b->fine->status }})
                            </div>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td style="vertical-align:top; text-align:right;">
                        @if($b->status === 'Pending')
                            <div style="display:inline-flex; gap:6px;">
                                <form action="/admin/borrowings/action" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="borrowing_id" value="{{ $b->id }}">
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-primary btn-sm" onclick="return confirm('Setujui peminjaman buku ini?')">ACC</button>
                                </form>
                                <form action="/admin/borrowings/action" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="borrowing_id" value="{{ $b->id }}">
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman buku ini?')">Tolak</button>
                                </form>
                            </div>
                        @else
                            <span class="text-muted" style="font-size:0.8rem;">Selesai diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Belum ada data peminjaman dengan status ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination 10 per halaman -->
    <div style="margin-top: 16px;">
        {{ $borrows->links() }}
    </div>
</div>
@endsection
