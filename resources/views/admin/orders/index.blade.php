@extends('layouts.admin')
@section('title', 'Data Pembelian')
@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Data Pembelian</h1>
        <p class="text-muted" style="margin:0;">Kelola semua pesanan pembelian buku, konfirmasi pembayaran, dan pengiriman</p>
    </div>
</div>

<!-- ============ TABS STATUS ============ -->
<div class="status-tabs">
    <a href="/admin/orders" class="status-tab {{ !request('status') ? 'active' : '' }}">
        Semua ({{ $counts['all'] }})
    </a>
    <a href="/admin/orders?status=Waiting Confirmation" class="status-tab {{ request('status') === 'Waiting Confirmation' ? 'active' : '' }}" style="{{ $counts['Waiting Confirmation'] > 0 ? 'border-color:#f59e0b; color:#b45309; font-weight:700;' : '' }}">
        ⏳ Menunggu ACC ({{ $counts['Waiting Confirmation'] }})
    </a>
    <a href="/admin/orders?status=Processing" class="status-tab {{ request('status') === 'Processing' ? 'active' : '' }}">
        Dikemas ({{ $counts['Processing'] }})
    </a>
    <a href="/admin/orders?status=Shipped" class="status-tab {{ request('status') === 'Shipped' ? 'active' : '' }}">
        Dikirim ({{ $counts['Shipped'] }})
    </a>
    <a href="/admin/orders?status=Completed" class="status-tab {{ request('status') === 'Completed' ? 'active' : '' }}">
        Selesai ({{ $counts['Completed'] }})
    </a>
    <a href="/admin/orders?status=Cancelled" class="status-tab {{ request('status') === 'Cancelled' ? 'active' : '' }}">
        Dibatalkan ({{ $counts['Cancelled'] }})
    </a>
    <a href="/admin/orders?status=Rejected" class="status-tab {{ request('status') === 'Rejected' ? 'active' : '' }}">
        Ditolak ({{ $counts['Rejected'] }})
    </a>
</div>

<!-- ============ TABEL DATA PEMBELIAN ============ -->
<div class="panel" style="padding:0; overflow:hidden;">
    <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-size:0.85rem; color:var(--text-muted);">
        Menampilkan <strong>{{ count($orders) }}</strong> pesanan
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice & Tanggal</th>
                    <th>User / Pembeli</th>
                    <th>Item Buku & Cover</th>
                    <th>Total Bayar</th>
                    <th>Status Pembayaran</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td style="vertical-align:top;">
                        <strong style="color:var(--primary); font-size:0.95rem;">{{ $o->invoice_code }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">
                            {{ date('d M Y H:i', strtotime($o->created_at)) }}
                        </div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">
                            Metode: <strong>{{ $o->payment_method ?? 'Transfer' }}</strong>
                        </div>
                    </td>

                    <td style="vertical-align:top;">
                        <strong>{{ $o->user->name ?? 'User Terhapus' }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $o->user->email ?? '-' }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">HP: {{ $o->user->phone ?? '-' }}</div>
                    </td>

                    <!-- Preview Cover & Judul Buku di Order -->
                    <td style="vertical-align:top; min-width:280px;">
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            @foreach($o->details as $d)
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:36px; height:50px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0; border:1px solid var(--border);">
                                        @if($d->book && $d->book->cover)
                                            <img src="{{ asset('uploads/covers/' . $d->book->cover) }}" alt="{{ $d->title_snapshot }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1rem;">📖</div>
                                        @endif
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:0.85rem; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            {{ $d->title_snapshot }}
                                        </div>
                                        <div style="font-size:0.75rem; color:var(--text-muted);">
                                            {{ $d->qty }}x @ Rp {{ number_format($d->price_snapshot, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>

                    <td style="vertical-align:top; font-weight:800; color:var(--primary); font-size:1rem;">
                        Rp {{ number_format($o->total, 0, ',', '.') }}
                    </td>

                    <td style="vertical-align:top;">
                        @if($o->status === 'Waiting Confirmation')
                            <span class="status-badge" style="background:#ffedd5; color:#c2410c; font-weight:800; border:1px solid #fdba74;">
                                ⏳ Menunggu ACC Bayar
                            </span>
                        @elseif($o->status === 'Processing')
                            <span class="status-badge" style="background:#fef3c7; color:#92400e;">Dikemas</span>
                        @elseif($o->status === 'Shipped')
                            <span class="status-badge" style="background:#e0e7ff; color:#3730a3;">Dikirim</span>
                        @elseif($o->status === 'Completed')
                            <span class="status-badge" style="background:#dcfce7; color:#166534;">Selesai</span>
                        @elseif($o->status === 'Cancelled')
                            <span class="status-badge" style="background:#f1f5f9; color:#475569;">Dibatalkan</span>
                        @elseif($o->status === 'Rejected')
                            <span class="status-badge" style="background:#fee2e2; color:#991b1b;">Ditolak</span>
                        @else
                            <span class="status-badge">{{ $o->status }}</span>
                        @endif

                        @if($o->tracking_number && in_array($o->status, ['Shipped', 'Completed']))
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Resi: <code>{{ $o->tracking_number }}</code></div>
                        @endif
                    </td>

                    <td style="vertical-align:top; text-align:right;">
                        @if($o->status === 'Waiting Confirmation')
                            <a href="/admin/orders/{{ $o->id }}" class="btn btn-primary btn-sm" style="background:#ea580c; border-color:#ea580c;">ACC Pembayaran</a>
                        @else
                            <a href="/admin/orders/{{ $o->id }}" class="btn btn-outline btn-sm">Lihat Detail</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Belum ada data pesanan pada kategori status ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination 10 per halaman -->
    <div style="margin-top: 16px;">
        {{ $orders->links() }}
    </div>
</div>
@endsection
