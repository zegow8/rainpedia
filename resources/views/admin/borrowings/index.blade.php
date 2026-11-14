@extends('layouts.admin')
@section('title', 'Data Peminjaman')
@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4" style="flex-wrap:wrap; gap:16px;">
    <div>
        <h1 style="margin-bottom:4px;">Data Peminjaman Buku</h1>
        <p class="text-muted" style="margin:0;">Kelola permohonan pinjam, tenggat waktu, dan status peminjaman buku</p>
    </div>
    <div>
        <a href="{{ url('/admin/borrowings/print') }}?status={{ request('status') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}&q={{ request('q') }}" 
           target="_blank" 
           class="btn btn-primary" 
           style="display:inline-flex; align-items:center; gap:8px; font-weight:700; padding:10px 18px; background:#2563eb;">
            🖨️ Cetak Laporan Peminjaman
        </a>
    </div>
</div>

<!-- ============ TABS STATUS ============ -->
<div class="status-tabs">
    <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}" class="status-tab {{ !request('status') ? 'active' : '' }}">
        Semua ({{ $counts['all'] }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Pending']) }}" class="status-tab {{ request('status') === 'Pending' ? 'active' : '' }}">
        Pending ({{ $counts['Pending'] }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Borrowed']) }}" class="status-tab {{ request('status') === 'Borrowed' ? 'active' : '' }}">
        Sedang Dipinjam ({{ $counts['Borrowed'] }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Overdue']) }}" class="status-tab {{ request('status') === 'Overdue' ? 'active' : '' }}">
        Terlambat ({{ $counts['Overdue'] }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Return Requested']) }}" class="status-tab {{ request('status') === 'Return Requested' ? 'active' : '' }}">
        Minta Kembali ({{ $counts['Return Requested'] }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Returned']) }}" class="status-tab {{ request('status') === 'Returned' ? 'active' : '' }}">
        Sudah Kembali ({{ $counts['Returned'] }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['status' => 'Rejected']) }}" class="status-tab {{ request('status') === 'Rejected' ? 'active' : '' }}">
        Ditolak ({{ $counts['Rejected'] }})
    </a>
</div>

<!-- ============ FILTER TANGGAL & PENCARIAN ============ -->
<div class="panel" style="margin-bottom:20px; padding:16px 20px;">
    <form method="GET" action="/admin/borrowings">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:180px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div style="flex:1; min-width:180px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div style="flex:1.5; min-width:200px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Cari Nama Peminjam / Judul Buku</label>
                <input type="text" name="q" class="form-control" placeholder="Nama, email, atau judul buku..." value="{{ request('q') }}">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="font-weight:700;">🔍 Filter</button>
                @if(request('start_date') || request('end_date') || request('q'))
                    <a href="/admin/borrowings{{ request('status') ? '?status=' . request('status') : '' }}" class="btn btn-outline">Reset</a>
                @endif
            </div>
        </div>
    </form>
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
                        @if($b->user)
                            <div style="margin-top:6px;">
                                <a href="/admin/chats?user_id={{ $b->user_id }}" class="btn btn-outline btn-sm" style="padding:2px 8px; font-size:0.72rem; text-decoration:none; display:inline-flex; align-items:center; gap:4px; font-weight:600;">
                                    💬 Chat User
                                </a>
                            </div>
                        @endif
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
                                    <button class="btn btn-primary btn-sm" onclick="return confirm('Setujui peminjaman buku ini?')">ACC Pinjam</button>
                                </form>
                                <form action="/admin/borrowings/action" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="borrowing_id" value="{{ $b->id }}">
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman buku ini?')">Tolak</button>
                                </form>
                            </div>
                        @elseif($b->status === 'Return Requested')
                            @php
                                $hasUnpaid = $b->fine && $b->fine->status === 'Unpaid';
                            @endphp
                            @if($hasUnpaid)
                                <div style="display:inline-flex; flex-direction:column; align-items:flex-end; gap:4px;">
                                    <button type="button" class="btn btn-sm" disabled style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; font-weight:700; cursor:not-allowed; opacity:0.85; padding:6px 10px;">
                                        ⚠️ Denda Belum Lunas
                                    </button>
                                    <div style="font-size:0.72rem; color:#dc2626;">Harus bayar denda dulu</div>
                                </div>
                            @else
                                <form action="/admin/returns/action" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="borrowing_id" value="{{ $b->id }}">
                                    <button type="submit" class="btn btn-sm" style="background:#10b981; color:white; border:none; font-weight:700; padding:7px 14px; border-radius:6px; cursor:pointer;" onclick="return confirm('ACC penerimaan pengembalian buku ini dan kembalikan stok?')">
                                        ✓ ACC Terima Buku
                                    </button>
                                </form>
                            @endif
                        @elseif(in_array($b->status, ['Borrowed', 'Overdue']))
                            <span class="text-muted" style="font-size:0.8rem;">Menunggu Pengembalian User</span>
                        @elseif($b->status === 'Returned')
                            <span style="font-size:0.8rem; color:#166534; font-weight:700;">✓ Selesai Dikembalikan</span>
                        @elseif($b->status === 'Rejected')
                            <span class="text-muted" style="font-size:0.8rem;">Peminjaman Ditolak</span>
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
