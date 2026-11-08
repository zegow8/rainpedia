@extends('layouts.admin')
@section('title', 'Pendapatan & Denda')
@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Pendapatan & Denda</h1>
        <p class="text-muted" style="margin:0;">Laporan pemasukan dari transaksi pembelian buku dan pembayaran denda</p>
    </div>
    <div style="text-align:right;">
        <div style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">Total Pemasukan:</div>
        <div style="font-size:1.8rem; font-weight:800; color:var(--success);">Rp {{ number_format($total, 0, ',', '.') }}</div>
    </div>
</div>

<!-- ============ FILTER BY SUMBER ============ -->
<div class="status-tabs">
    <a href="/admin/payments" class="status-tab {{ $source === 'all' ? 'active' : '' }}">
        Semua Sumber (Rp {{ number_format($totalOrders + $totalFines, 0, ',', '.') }})
    </a>
    <a href="/admin/payments?source=order" class="status-tab {{ $source === 'order' ? 'active' : '' }}">
        📦 Pembelian Buku (Rp {{ number_format($totalOrders, 0, ',', '.') }})
    </a>
    <a href="/admin/payments?source=fine" class="status-tab {{ $source === 'fine' ? 'active' : '' }}">
        💰 Denda Peminjaman (Rp {{ number_format($totalFines, 0, ',', '.') }})
    </a>
</div>

<div class="panel" style="padding:0; overflow:hidden;">
    <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-size:0.85rem; color:var(--text-muted);">
        Menampilkan <strong>{{ count($transactions) }}</strong> catatan transaksi pendapatan
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
                        Belum ada data pendapatan pada sumber ini.
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
