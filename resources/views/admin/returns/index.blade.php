@extends('layouts.admin')
@section('title', 'Pengembalian Buku')
@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Pengembalian Buku</h1>
        <p class="text-muted" style="margin:0;">Konfirmasi pengembalian buku dan cek durasi peminjaman pengguna</p>
    </div>
</div>

<!-- ============ TABS STATUS ============ -->
<div class="status-tabs">
    <a href="/admin/returns" class="status-tab {{ !request('status') ? 'active' : '' }}">
        Semua ({{ $counts['all'] }})
    </a>
    <a href="/admin/returns?status=Pending" class="status-tab {{ request('status') === 'Pending' ? 'active' : '' }}">
        Menunggu ACC ({{ $counts['Pending'] }})
    </a>
    <a href="/admin/returns?status=Approved" class="status-tab {{ request('status') === 'Approved' ? 'active' : '' }}">
        Selesai Diterima ({{ $counts['Approved'] }})
    </a>
</div>

<div class="panel" style="padding:0; overflow:hidden;">
    <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-size:0.85rem; color:var(--text-muted);">
        Menampilkan <strong>{{ count($returns) }}</strong> pengembalian
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:70px;">Cover</th>
                    <th>Buku yang Dikembalikan</th>
                    <th>Peminjam (User)</th>
                    <th>Riwayat Waktu (Mulai - Tenggat - Kembali)</th>
                    <th>Status & Denda Keterlambatan</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $r)
                @php
                    $bw = $r->borrowing;
                    $book = $bw ? $bw->book : null;
                    $user = $bw ? $bw->user : null;
                @endphp
                <tr>
                    <!-- Preview Cover Buku -->
                    <td style="vertical-align:top;">
                        <div style="width:50px; height:70px; border-radius:6px; overflow:hidden; background:#eee; border:1px solid var(--border); box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                            @if($book && $book->cover)
                                <img src="{{ asset('uploads/covers/' . $book->cover) }}" alt="{{ $book->title }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.5rem;">📖</div>
                            @endif
                        </div>
                    </td>

                    <!-- Buku -->
                    <td style="vertical-align:top;">
                        <strong style="font-size:0.95rem;">{{ $book->title ?? 'Buku Dihapus' }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Penulis: {{ $book->author ?? '-' }}</div>
                    </td>

                    <!-- User -->
                    <td style="vertical-align:top;">
                        <strong>{{ $user->name ?? 'User Terhapus' }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $user->email ?? '-' }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">HP: {{ $user->phone ?? '-' }}</div>
                    </td>

                    <!-- Riwayat Waktu Peminjaman: Dr Kapan Sampe Kapan -->
                    <td style="vertical-align:top; min-width:260px;">
                        <div style="background:#f8fafc; border:1px solid var(--border); border-radius:6px; padding:8px 12px; font-size:0.82rem;">
                            <div>
                                📅 Mulai Pinjam: 
                                <strong>{{ $bw && $bw->start_date ? date('d M Y', strtotime($bw->start_date)) : '-' }}</strong>
                            </div>
                            <div style="margin-top:2px;">
                                ⏰ Batas Jatuh Tempo: 
                                <strong>{{ $bw && $bw->due_date ? date('d M Y', strtotime($bw->due_date)) : '-' }}</strong>
                            </div>
                            <div style="margin-top:2px; color:var(--primary); font-weight:600;">
                                📦 Tanggal Ajukan Kembali: 
                                <strong>{{ $r->requested_at ? date('d M Y H:i', strtotime($r->requested_at)) : '-' }}</strong>
                            </div>
                            @if($r->reviewed_at)
                                <div style="margin-top:2px; font-size:0.75rem; color:var(--text-muted);">
                                    Disetujui/Ditinjau: {{ date('d M Y H:i', strtotime($r->reviewed_at)) }}
                                </div>
                            @endif
                        </div>
                    </td>

                    <!-- Status & Denda -->
                    <td style="vertical-align:top;">
                        @if($r->status === 'Pending')
                            <span class="status-badge" style="background:#fef3c7; color:#92400e;">Menunggu ACC</span>
                        @elseif($r->status === 'Approved')
                            <span class="status-badge" style="background:#dcfce7; color:#166534;">Telah Diterima (ACC)</span>
                        @else
                            <span class="status-badge">{{ $r->status }}</span>
                        @endif

                        @if($bw && $bw->late_days > 0)
                            <div style="margin-top:6px; font-size:0.78rem; padding:4px 8px; border-radius:4px; background:#fee2e2; color:#991b1b; font-weight:700;">
                                ⚠️ Terlambat: {{ $bw->late_days }} hari<br>
                                Denda: Rp {{ number_format($bw->fine_amount, 0, ',', '.') }}
                            </div>
                        @else
                            <div style="margin-top:4px; font-size:0.75rem; color:var(--success); font-weight:600;">
                                ✅ Tepat Waktu (Bebas Denda)
                            </div>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td style="vertical-align:top; text-align:right;">
                        @if($r->status === 'Pending')
                            @php
                                $hasUnpaidFine = $bw && $bw->fine && $bw->fine->status === 'Unpaid';
                            @endphp
                            @if($hasUnpaidFine)
                                <div style="display:inline-flex; flex-direction:column; align-items:flex-end; gap:4px;">
                                    <button type="button" class="btn btn-sm" disabled style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; font-weight:700; cursor:not-allowed; opacity:0.85; padding:6px 10px;">
                                        ⚠️ Denda Belum Lunas
                                    </button>
                                    <div style="font-size:0.72rem; color:#dc2626;">Harus bayar denda dulu</div>
                                </div>
                            @else
                                <form action="/admin/returns/action" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="return_id" value="{{ $r->id }}">
                                    <button class="btn btn-sm" style="background:#10b981; color:white; border:none; font-weight:700; padding:7px 14px; border-radius:6px; cursor:pointer;" onclick="return confirm('ACC penerimaan pengembalian buku ini dan kembalikan stok pinjam?')">
                                        ✓ ACC Terima Buku
                                    </button>
                                </form>
                            @endif
                        @else
                            <span class="text-muted" style="font-size:0.8rem;">Buku Telah Diterima</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Belum ada data pengembalian dengan status ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination 10 per halaman -->
    <div style="margin-top: 16px;">
        {{ $returns->links() }}
    </div>
</div>
@endsection
