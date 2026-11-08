@extends('layouts.user')

@section('title', 'Riwayat Transaksi')

@section('content')
<style>
.status-tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
.status-tab { padding:8px 16px; border-radius:100px; font-size:0.85rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border-dark); color:var(--text-muted); background:var(--white); transition:all 0.2s; }
.status-tab:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.status-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Riwayat Pembelian Buku</h1>
        <p class="text-muted" style="margin:0;">Daftar seluruh pesanan pembelian buku yang pernah Anda lakukan</p>
    </div>
    <a href="/user/catalog" class="btn btn-primary btn-sm">+ Beli Buku Baru</a>
</div>

<!-- ============ STATUS TABS ============ -->
<div class="status-tabs">
    <a href="/user/transactions" class="status-tab {{ !request('status') ? 'active' : '' }}">
        Semua ({{ $counts['all'] }})
    </a>
    <a href="/user/transactions?status=Waiting Confirmation" class="status-tab {{ request('status') === 'Waiting Confirmation' ? 'active' : '' }}" style="{{ $counts['Waiting Confirmation'] > 0 ? 'border-color:#f59e0b; color:#b45309; font-weight:700;' : '' }}">
        ⏳ Menunggu ACC ({{ $counts['Waiting Confirmation'] }})
    </a>
    <a href="/user/transactions?status=Processing" class="status-tab {{ request('status') === 'Processing' ? 'active' : '' }}">
        Dikemas ({{ $counts['Processing'] }})
    </a>
    <a href="/user/transactions?status=Shipped" class="status-tab {{ request('status') === 'Shipped' ? 'active' : '' }}">
        Dikirim ({{ $counts['Shipped'] }})
    </a>
    <a href="/user/transactions?status=Completed" class="status-tab {{ request('status') === 'Completed' ? 'active' : '' }}">
        Selesai ({{ $counts['Completed'] }})
    </a>
    <a href="/user/transactions?status=Cancelled" class="status-tab {{ request('status') === 'Cancelled' ? 'active' : '' }}">
        Dibatalkan ({{ $counts['Cancelled'] }})
    </a>
    <a href="/user/transactions?status=Rejected" class="status-tab {{ request('status') === 'Rejected' ? 'active' : '' }}">
        Ditolak ({{ $counts['Rejected'] }})
    </a>
</div>

@if($orders->isEmpty())
    <div class="panel text-center" style="padding:56px 32px;">
        <div style="font-size:3rem; margin-bottom:12px;">🛒</div>
        <h3>Belum Ada Transaksi Pembelian</h3>
        <p class="text-muted">Tidak ada data pesanan pada filter status ini.</p>
        <a href="/user/catalog" class="btn btn-primary mt-4">Lihat Katalog Buku</a>
    </div>
@else
    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach($orders as $o)
            <div style="background:white; border:1px solid var(--border); border-radius:10px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <!-- Header Order -->
                <div class="flex-between" style="border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:14px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <code style="background:var(--primary-light); padding:4px 10px; border-radius:6px; font-weight:700; color:var(--primary); font-size:0.9rem;">{{ $o->invoice_code }}</code>
                        <span style="font-size:0.85rem; color:var(--text-muted);">{{ date('d M Y H:i', strtotime($o->created_at)) }}</span>
                    </div>
                    <div>
                        @if($o->status === 'Waiting Confirmation')
                            <span class="status-badge" style="background:#ffedd5; color:#c2410c; font-weight:800; border:1px solid #fdba74;">
                                ⏳ Menunggu Konfirmasi Admin
                            </span>
                        @elseif($o->status === 'Processing')
                            <span class="status-badge" style="background:#fef3c7; color:#92400e;">Dikemas</span>
                        @elseif($o->status === 'Shipped')
                            <span class="status-badge" style="background:#e0e7ff; color:#3730a3;">Sedang Dikirim</span>
                        @elseif($o->status === 'Completed')
                            <span class="status-badge" style="background:#dcfce7; color:#166534;">Selesai</span>
                        @elseif($o->status === 'Cancelled')
                            <span class="status-badge" style="background:#f1f5f9; color:#475569;">Dibatalkan</span>
                        @elseif($o->status === 'Rejected')
                            <span class="status-badge" style="background:#fee2e2; color:#991b1b;">Ditolak</span>
                        @endif
                    </div>
                </div>

                <!-- Info Buku-Buku di dalam Pesanan (Judul & Cover) -->
                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px;">
                    @foreach($o->details as $d)
                        <div style="display:flex; align-items:center; gap:14px;">
                            <div style="width:45px; height:62px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0; border:1px solid var(--border);">
                                @if($d->book && $d->book->cover)
                                    <img src="{{ asset('uploads/covers/' . $d->book->cover) }}" alt="{{ $d->title_snapshot }}" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.2rem;">📖</div>
                                @endif
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:700; font-size:0.95rem; color:var(--text-main);">
                                    {{ $d->title_snapshot }}
                                </div>
                                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">
                                    {{ $d->qty }} barang x Rp {{ number_format($d->price_snapshot, 0, ',', '.') }}
                                </div>
                            </div>
                            <div style="font-weight:700; color:var(--primary); font-size:0.95rem;">
                                Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Card: Total & Aksi Detail -->
                <div class="flex-between" style="border-top:1px solid var(--border); padding-top:14px;">
                    <div>
                        <span style="font-size:0.85rem; color:var(--text-muted);">Total Belanja:</span>
                        <strong style="font-size:1.15rem; color:var(--primary); margin-left:6px;">Rp {{ number_format($o->total, 0, ',', '.') }}</strong>
                        @if($o->tracking_number && in_array($o->status, ['Shipped', 'Completed']))
                            <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">
                                No. Resi: <code>{{ $o->tracking_number }}</code>
                            </div>
                        @endif
                    </div>
                    <div style="display:flex; gap:8px;">
                        @if(in_array($o->status, ['Waiting Confirmation', 'Processing']))
                            <form action="/user/transactions/cancel" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $o->id }}">
                                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger);" onclick="return confirm('Batalkan pesanan ini?')">Batalkan</button>
                            </form>
                        @endif
                        <a href="/user/transactions/{{ $o->id }}" class="btn btn-primary btn-sm">Lihat Detail Lengkap</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
