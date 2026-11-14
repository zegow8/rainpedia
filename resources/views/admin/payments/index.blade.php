@extends('layouts.admin')
@section('title', 'Pendapatan & Denda')
@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4" style="flex-wrap:wrap; gap:16px;">
    <div>
        <h1 style="margin-bottom:4px;">Pendapatan & Denda</h1>
        <p class="text-muted" style="margin:0;">Laporan pemasukan dari transaksi pembelian buku dan pembayaran denda</p>
    </div>
    <div style="display:flex; align-items:center; gap:20px;">
        <div style="text-align:right;">
            <div style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">Total Pemasukan:</div>
            <div style="font-size:1.8rem; font-weight:800; color:var(--success);">Rp {{ number_format($total, 0, ',', '.') }}</div>
        </div>
        <a href="{{ url('/admin/payments/print') }}?source={{ $source }}&start_date={{ $startDate }}&end_date={{ $endDate }}&q={{ $keyword }}" 
           target="_blank" 
           class="btn btn-primary" 
           style="display:inline-flex; align-items:center; gap:8px; font-weight:700; padding:10px 18px; background:#2563eb;">
            🖨️ Cetak Laporan
        </a>
    </div>
</div>

<!-- ============ FILTER TABS SUMBER ============ -->
<div class="status-tabs">
    <a href="{{ request()->fullUrlWithQuery(['source' => 'all']) }}" class="status-tab {{ $source === 'all' ? 'active' : '' }}">
        Semua Sumber (Rp {{ number_format($totalOrders + $totalFines, 0, ',', '.') }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['source' => 'order']) }}" class="status-tab {{ $source === 'order' ? 'active' : '' }}">
        📦 Pembelian Buku (Rp {{ number_format($totalOrders, 0, ',', '.') }})
    </a>
    <a href="{{ request()->fullUrlWithQuery(['source' => 'fine']) }}" class="status-tab {{ $source === 'fine' ? 'active' : '' }}">
        💰 Denda Peminjaman (Rp {{ number_format($totalFines, 0, ',', '.') }})
    </a>
</div>

<!-- ============ FILTER TANGGAL & PENCARIAN ============ -->
<div class="panel" style="margin-bottom:20px; padding:16px 20px;">
    <form method="GET" action="/admin/payments">
        <input type="hidden" name="source" value="{{ $source }}">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:180px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div style="flex:1; min-width:180px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div style="flex:1.5; min-width:200px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Cari Invoice / Nama / Judul</label>
                <input type="text" name="q" class="form-control" placeholder="No. invoice atau nama..." value="{{ $keyword }}">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="font-weight:700;">🔍 Filter</button>
                @if($startDate || $endDate || $keyword)
                    <a href="/admin/payments?source={{ $source }}" class="btn btn-outline">Reset</a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="panel" style="padding:0; overflow:hidden;">
    <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-size:0.85rem; color:var(--text-muted); display:flex; justify-content:space-between; align-items:center;">
        <div>Menampilkan <strong>{{ count($transactions) }}</strong> catatan transaksi pendapatan</div>
        @if($startDate || $endDate)
            <div style="font-size:0.8rem; font-weight:600; color:var(--primary);">
                📅 Filter: {{ $startDate ?: 'Awal' }} s/d {{ $endDate ?: 'Sekarang' }}
            </div>
        @endif
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Sumber Pemasukan</th>
                    <th>Keterangan (Klik untuk Detail)</th>
                    <th style="text-align:right;">Nominal Masuk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td style="white-space:nowrap;">
                        <strong>{{ date('d M Y', strtotime($tx->date)) }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ date('H:i', strtotime($tx->date)) }} WIB</div>
                    </td>
                    <td>
                        @if($tx->badge === 'order')
                            <span class="status-badge" style="background:#e0e7ff; color:#3730a3;">📦 Pembelian Buku</span>
                        @else
                            <span class="status-badge" style="background:#fef3c7; color:#92400e;">💰 Denda Peminjaman</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ $tx->link }}" style="font-weight:700; color:var(--primary); text-decoration:none;" title="Lihat Transaksi">
                            {{ $tx->desc }} &rarr;
                        </a>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Klik untuk membuka transaksi terkait</div>
                    </td>
                    <td style="text-align:right; color:var(--success); font-weight:800; font-size:1rem; white-space:nowrap;">
                        + Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Belum ada data pendapatan pada filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination 10 per halaman -->
    <div style="margin-top: 16px;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
