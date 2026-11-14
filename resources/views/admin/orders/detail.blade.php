@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<style>
    .order-detail-header h1 {
        font-size: clamp(1.2rem, 4.5vw, 1.75rem);
        font-weight: 800;
        color: #1e293b;
        word-break: break-all;
        line-height: 1.3;
        margin: 6px 0 4px 0;
    }

    @media (max-width: 868px) {
        .admin-order-detail-grid { grid-template-columns: 1fr !important; gap: 16px !important; }
    }

    /* Tampilan Desktop vs Mobile untuk Rincian Buku */
    .order-table-desktop {
        display: block;
        width: 100%;
        overflow-x: auto;
    }
    .order-cards-mobile {
        display: none;
    }

    @media (max-width: 640px) {
        .order-table-desktop {
            display: none !important;
        }
        .order-cards-mobile {
            display: flex !important;
            flex-direction: column;
        }
    }
</style>

<div class="mb-4 order-detail-header">
    <a href="/admin/orders" style="font-weight:600; font-size:0.88rem; text-decoration:none; color:var(--primary); display:inline-flex; align-items:center; gap:6px; margin-bottom:4px;">
        &larr; Kembali ke Daftar Pembelian
    </a>
    <h1>Pesanan: {{ $order->invoice_code }}</h1>
    <p class="text-muted" style="margin:0; font-size:0.85rem;">Tanggal Transaksi: {{ date('d M Y H:i', strtotime($order->created_at)) }} WIB</p>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;" class="admin-order-detail-grid">
    <!-- DAFTAR ITEM DENGAN PREVIEW COVER BUKU -->
    <div>
        <div class="panel" style="padding:0; overflow:hidden; margin-bottom:20px;">
            <div style="padding:14px 18px; background:#f8fafc; border-bottom:1px solid var(--border); font-weight:700; font-size:0.95rem;">
                📚 Rincian Buku yang Dipesan
            </div>

            <!-- 1. TAMPILAN DESKTOP (TABEL) -->
            <div class="order-table-desktop">
                <table class="table" style="margin:0;">
                    <thead>
                        <tr>
                            <th style="width:70px;">Cover</th>
                            <th>Buku</th>
                            <th>Harga Satuan</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $d)
                        <tr>
                            <td>
                                <div style="width:50px; height:70px; border-radius:6px; overflow:hidden; background:#eee; border:1px solid var(--border); box-shadow:0 1px 3px rgba(0,0,0,0.08);">
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
                            <td style="white-space:nowrap;">Rp {{ number_format($d->price_snapshot, 0, ',', '.') }}</td>
                            <td style="text-align:center; font-weight:700;">{{ $d->qty }} pcs</td>
                            <td style="text-align:right; font-weight:700; color:var(--primary); white-space:nowrap;">
                                Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafc;">
                            <th colspan="4" style="text-align:right; font-size:0.95rem; color:#1e293b;">Total Pembayaran :</th>
                            <th style="text-align:right; font-size:1.15rem; color:var(--primary); font-weight:800; white-space:nowrap;">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- 2. TAMPILAN MOBILE (KARTU PRODUK) -->
            <div class="order-cards-mobile">
                @foreach($order->details as $d)
                    <div style="display:flex; gap:12px; padding:14px; border-bottom:1px solid var(--border); align-items:flex-start;">
                        <div style="width:55px; height:78px; border-radius:6px; overflow:hidden; background:#f1f5f9; flex-shrink:0; border:1px solid var(--border);">
                            @if($d->book && $d->book->cover)
                                <img src="{{ asset('uploads/covers/' . $d->book->cover) }}" alt="{{ $d->title_snapshot }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.4rem;">📖</div>
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:700; font-size:0.92rem; color:var(--text-main); line-height:1.35; margin-bottom:2px;">
                                {{ $d->title_snapshot }}
                            </div>
                            @if($d->book)
                                <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:6px;">
                                    Penulis: {{ $d->book->author }}
                                </div>
                            @endif
                            <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:6px; margin-top:6px;">
                                <div style="font-size:0.8rem; color:var(--text-muted);">
                                    Rp {{ number_format($d->price_snapshot, 0, ',', '.') }} &times; <strong>{{ $d->qty }} pcs</strong>
                                </div>
                                <div style="font-size:0.95rem; font-weight:800; color:var(--primary);">
                                    Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Rekap Pembayaran Mobile -->
                <div style="padding:14px 16px; background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-size:0.9rem; font-weight:700; color:var(--text-main);">Total Pembayaran:</div>
                    <div style="font-size:1.15rem; font-weight:800; color:var(--primary);">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </div>
                </div>
            </div>

        </div>

        <!-- INFORMASI PENGIRIMAN & PEMBELI -->
        <div class="panel">
            <h3 class="panel-title mb-4" style="font-size:1rem;">📍 Informasi Pengiriman & Pembeli</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Nama Pembeli</div>
                    <div style="font-size:0.95rem; font-weight:700; margin-top:2px;">{{ $order->user->name ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Email & No. HP</div>
                    <div style="font-size:0.88rem; margin-top:2px; word-break:break-all;">{{ $order->user->email ?? '-' }} | {{ $order->user->phone ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Metode Pembayaran</div>
                    <div style="font-size:0.92rem; font-weight:700; color:var(--primary); margin-top:2px;">{{ $order->payment_method ?? 'Transfer' }}</div>
                </div>
                <div style="grid-column: 1 / -1;">
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Alamat Pengiriman</div>
                    <div style="font-size:0.9rem; margin-top:2px; background:#f8fafc; padding:12px; border-radius:6px; border:1px solid var(--border); line-height:1.5; word-break:break-word;">
                        {{ $order->shipping_address ?? 'Tidak ada alamat tercatat' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UPDATE STATUS & RESI -->
    <div>
        <div class="panel">
            <h3 class="panel-title mb-4" style="font-size:1rem;">⚙️ Kelola Status Pembayaran &amp; Pesanan</h3>
            
            <div style="margin-bottom:16px; padding:12px; background:#f8fafc; border-radius:8px; border:1px solid var(--border);">
                <div style="font-size:0.72rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Status Saat Ini</div>
                <div style="margin-top:4px;">
                    @if($order->status === 'Waiting Confirmation')
                        <span class="status-badge" style="background:#ffedd5; color:#c2410c; font-size:0.85rem; font-weight:800; border:1px solid #fdba74;">
                            ⏳ Menunggu Konfirmasi Admin
                        </span>
                    @elseif($order->status === 'Processing')
                        <span class="status-badge" style="background:#fef3c7; color:#92400e;">Dikemas (Disiapkan)</span>
                    @elseif($order->status === 'Shipped')
                        <span class="status-badge" style="background:#e0e7ff; color:#3730a3;">Sedang Dikirim</span>
                    @elseif($order->status === 'Completed')
                        <span class="status-badge" style="background:#dcfce7; color:#166534;">Selesai Diterima</span>
                    @elseif($order->status === 'Cancelled')
                        <span class="status-badge" style="background:#f1f5f9; color:#475569;">Dibatalkan Pembeli</span>
                    @elseif($order->status === 'Rejected')
                        <span class="status-badge" style="background:#fee2e2; color:#991b1b;">Ditolak Admin</span>
                    @endif
                </div>
            </div>

            <!-- Form Update Status -->
            <form action="/admin/orders/update/{{ $order->id }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label" style="font-size:0.85rem; font-weight:700;">Ubah Status Pesanan</label>
                    <select name="status" class="form-control" style="font-size:0.9rem;" {{ in_array($order->status, ['Completed', 'Rejected', 'Cancelled']) ? 'disabled' : '' }}>
                        <option value="Waiting Confirmation" {{ $order->status === 'Waiting Confirmation' ? 'selected' : '' }}>Menunggu Konfirmasi Admin</option>
                        <option value="Processing" {{ $order->status === 'Processing' ? 'selected' : '' }}>Dikemas (ACC Pembayaran)</option>
                        <option value="Shipped" {{ $order->status === 'Shipped' ? 'selected' : '' }}>Dikirim (Input Resi)</option>
                        <option value="Completed" {{ $order->status === 'Completed' ? 'selected' : '' }}>Selesai (Pesanan Diterima)</option>
                        <option value="Rejected" {{ $order->status === 'Rejected' ? 'selected' : '' }}>Tolak Pembayaran / Pesanan</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label" style="font-size:0.85rem; font-weight:700;">Nomor Resi Pengiriman</label>
                    <input type="text" name="tracking_number" class="form-control" style="font-family:monospace; font-weight:700;" value="{{ $order->tracking_number }}" placeholder="Contoh: RPN26110798234" {{ in_array($order->status, ['Completed', 'Rejected', 'Cancelled']) ? 'disabled' : '' }}>
                    <small class="text-muted" style="display:block; margin-top:4px; font-size:0.75rem;">Masukkan nomor resi saat status diubah menjadi Dikirim.</small>
                </div>

                @if(!in_array($order->status, ['Completed', 'Rejected', 'Cancelled']))
                    <button type="submit" class="btn btn-primary btn-block" style="font-weight:700; height:42px;">
                        Simpan Perubahan Status
                    </button>
                @else
                    <div style="font-size:0.82rem; color:var(--text-muted); text-align:center; padding:10px; background:#f1f5f9; border-radius:6px;">
                        🔒 Status pesanan sudah final dan tidak dapat diubah lagi.
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
