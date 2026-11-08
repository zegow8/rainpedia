@extends('layouts.user')

@section('title', 'Detail Transaksi')

@section('content')
<style>
    @media (max-width: 868px) {
        .user-order-detail-grid { grid-template-columns: 1fr !important; gap: 16px !important; }
    }
</style>
<div class="mb-4">
    <a href="/user/transactions" style="font-weight:600; font-size:0.9rem;">&larr; Kembali ke Riwayat Pembelian</a>
    <h1 style="margin-top:8px; margin-bottom:4px;">Detail Pesanan: {{ $order->invoice_code }}</h1>
    <p class="text-muted" style="margin:0;">Tanggal Pemesanan: {{ date('d M Y H:i', strtotime($order->created_at)) }} WIB</p>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;" class="user-order-detail-grid">
    <!-- RINCIAN BUKU YANG DIBELI DENGAN COVER -->
    <div>
        <div class="panel" style="padding:0; overflow:hidden; margin-bottom:24px;">
            <div style="padding:16px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-weight:700;">
                📚 Daftar Buku yang Dipesan
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:70px;">Cover</th>
                        <th>Buku</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $d)
                    <tr>
                        <td>
                            <div style="width:50px; height:70px; border-radius:6px; overflow:hidden; background:#eee; border:1px solid var(--border);">
                                @if($d->book && $d->book->cover)
                                    <img src="{{ asset('uploads/covers/' . $d->book->cover) }}" alt="{{ $d->title_snapshot }}" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.5rem;">📖</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong style="font-size:0.95rem;">{{ $d->title_snapshot }}</strong>
                            @if($d->book)
                                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Penulis: {{ $d->book->author }}</div>
                            @endif
                        </td>
                        <td>Rp {{ number_format($d->price_snapshot, 0, ',', '.') }}</td>
                        <td><span style="font-weight:700;">{{ $d->qty }}</span> pcs</td>
                        <td style="text-align:right; font-weight:700; color:var(--primary);">
                            Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:right; font-size:1rem;">Total Tagihan</th>
                        <th style="text-align:right; font-size:1.15rem; color:white;">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ALAMAT PENGIRIMAN -->
        <div class="panel">
            <h3 class="panel-title mb-2">📍 Alamat Pengiriman</h3>
            <p style="margin:0; font-size:0.95rem; color:var(--text-main); background:#f8fafc; padding:14px; border-radius:8px; border:1px solid var(--border); line-height:1.6;">
                {{ $order->shipping_address ?? 'Alamat tidak tercantum' }}
            </p>
        </div>
    </div>

    <!-- STATUS & TRACKING -->
    <div>
        <div class="panel">
            <h3 class="panel-title mb-4">📦 Status Pesanan</h3>
            
            <div style="margin-bottom:16px;">
                <div style="font-size:0.8rem; text-transform:uppercase; font-weight:700; color:var(--text-muted); margin-bottom:4px;">Status Saat Ini</div>
                <div>
                    @if($order->status === 'Waiting Confirmation')
                        <span class="status-badge" style="background:#ffedd5; color:#c2410c; font-size:0.85rem; font-weight:800; border:1px solid #fdba74;">
                            ⏳ Menunggu Konfirmasi Admin
                        </span>
                        <p style="font-size:0.8rem; color:var(--text-muted); margin-top:6px;">
                            Pembayaran Anda via <strong>{{ $order->payment_method }}</strong> sedang diperiksa oleh admin. Buku akan segera dikemas setelah pembayaran di-ACC.
                        </p>
                    @elseif($order->status === 'Processing')
                        <span class="status-badge" style="background:#fef3c7; color:#92400e; font-size:0.85rem;">Dikemas (Sedang Disiapkan)</span>
                    @elseif($order->status === 'Shipped')
                        <span class="status-badge" style="background:#e0e7ff; color:#3730a3; font-size:0.85rem;">Sedang Dikirim</span>
                    @elseif($order->status === 'Completed')
                        <span class="status-badge" style="background:#dcfce7; color:#166534; font-size:0.85rem;">Selesai Diterima</span>
                    @elseif($order->status === 'Cancelled')
                        <span class="status-badge" style="background:#f1f5f9; color:#475569; font-size:0.85rem;">Pesanan Dibatalkan</span>
                    @elseif($order->status === 'Rejected')
                        <span class="status-badge" style="background:#fee2e2; color:#991b1b; font-size:0.85rem;">Pesanan Ditolak</span>
                    @endif
                </div>
            </div>

            @if($order->tracking_number && in_array($order->status, ['Shipped', 'Completed']))
                <div style="margin-bottom:16px; background:#eff6ff; padding:12px; border-radius:8px; border:1px solid #bfdbfe;">
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--primary-dark);">Nomor Resi Pengiriman</div>
                    <div style="font-family:monospace; font-size:1.1rem; font-weight:800; color:var(--primary); margin-top:4px;">
                        {{ $order->tracking_number }}
                    </div>
                </div>
            @endif

            <div style="margin-bottom:16px;">
                <div style="font-size:0.8rem; text-transform:uppercase; font-weight:700; color:var(--text-muted); margin-bottom:4px;">Metode Pembayaran</div>
                <div style="font-weight:700; color:var(--primary);">{{ $order->payment_method ?? 'Transfer Bank' }}</div>
            </div>

            @if(in_array($order->status, ['Waiting Confirmation', 'Processing']))
                <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border);">
                    <form action="/user/transactions/cancel" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit" class="btn btn-outline btn-block" style="color:var(--danger); border-color:var(--danger);" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
