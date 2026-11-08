@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('content')
<style>
    @media (max-width: 868px) {
        .admin-order-detail-grid { grid-template-columns: 1fr !important; gap: 16px !important; }
    }
</style>
<div class="mb-4">
    <a href="/admin/orders" style="font-weight:600; font-size:0.9rem;">&larr; Kembali ke Daftar Pembelian</a>
    <h1 style="margin-top:8px; margin-bottom:4px;">Pesanan: {{ $order->invoice_code }}</h1>
    <p class="text-muted" style="margin:0;">Tanggal Transaksi: {{ date('d M Y H:i', strtotime($order->created_at)) }}</p>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;" class="admin-order-detail-grid">
    <!-- DAFTAR ITEM DENGAN PREVIEW COVER BUKU -->
    <div>
        <div class="panel" style="padding:0; overflow:hidden; margin-bottom:24px;">
            <div style="padding:16px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-weight:700;">
                📚 Rincian Buku yang Dipesan
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:70px;">Cover</th>
                        <th>Buku</th>
                        <th>Harga Satuan</th>
                        <th>Qty</th>
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
                        <th colspan="4" style="text-align:right; font-size:1rem;">Total Pembayaran</th>
                        <th style="text-align:right; font-size:1.15rem; color:white;">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- INFORMASI PENGIRIMAN & PEMBELI -->
        <div class="panel">
            <h3 class="panel-title mb-4">📍 Informasi Pengiriman & Pembeli</h3>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Nama Pembeli</div>
                    <div style="font-size:1rem; font-weight:600; margin-top:2px;">{{ $order->user->name ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Email & No. HP</div>
                    <div style="font-size:0.9rem; margin-top:2px;">{{ $order->user->email ?? '-' }} | {{ $order->user->phone ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Metode Pembayaran</div>
                    <div style="font-size:0.95rem; font-weight:700; color:var(--primary); margin-top:2px;">{{ $order->payment_method ?? 'Transfer' }}</div>
                </div>
                <div style="grid-column: span 2;">
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--text-muted);">Alamat Pengiriman</div>
                    <div style="font-size:0.95rem; margin-top:2px; background:#f8fafc; padding:12px; border-radius:6px; border:1px solid var(--border);">
                        {{ $order->shipping_address ?? 'Tidak ada alamat tercatat' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UPDATE STATUS & RESI -->
    <div>
        <div class="panel">
            <h3 class="panel-title mb-4">⚙️ Kelola Status Pembayaran &amp; Pesanan</h3>
            
            <div style="margin-bottom:16px; padding:12px; background:#f8fafc; border-radius:8px; border:1px solid var(--border);">
                <div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Status Saat Ini</div>
                <div style="margin-top:4px;">
                    @if($order->status === 'Waiting Confirmation')
                        <span class="status-badge" style="background:#ffedd5; color:#c2410c; font-size:0.85rem; font-weight:800; border:1px solid #fdba74;">
                            ⏳ Menunggu Konfirmasi Admin
                        </span>
                    @elseif($order->status === 'Processing')
                        <span class="status-badge" style="background:#fef3c7; color:#92400e; font-size:0.85rem;">Dikemas (Sedang Disiapkan)</span>
                    @elseif($order->status === 'Shipped')
                        <span class="status-badge" style="background:#e0e7ff; color:#3730a3; font-size:0.85rem;">Dikirim (Shipped)</span>
                    @elseif($order->status === 'Completed')
                        <span class="status-badge" style="background:#dcfce7; color:#166534; font-size:0.85rem;">Selesai (Completed)</span>
                    @elseif($order->status === 'Cancelled')
                        <span class="status-badge" style="background:#f1f5f9; color:#475569; font-size:0.85rem;">Dibatalkan (Cancelled)</span>
                    @elseif($order->status === 'Rejected')
                        <span class="status-badge" style="background:#fee2e2; color:#991b1b; font-size:0.85rem;">Ditolak (Rejected)</span>
                    @endif
                </div>
            </div>

            <!-- JIKA STATUS WAITING CONFIRMATION: TOMBOL INSTAN ACC / TOLAK -->
            @if($order->status === 'Waiting Confirmation')
                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:14px; margin-bottom:18px;">
                    <strong style="color:#b45309; font-size:0.9rem;">⚠️ Perlu Konfirmasi Pembayaran:</strong>
                    <p style="font-size:0.8rem; color:#92400e; margin:4px 0 12px;">
                        User telah memilih pembayaran via <strong>{{ $order->payment_method }}</strong>. Silakan periksa mutasi rekening Anda sebelum meng-ACC pesanan ini.
                    </p>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <form action="/admin/orders/update/{{ $order->id }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="Processing">
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('ACC Pembayaran ini? Status akan berubah menjadi Dikemas.')">
                                ✅ ACC Pembayaran &amp; Kemas Buku
                            </button>
                        </form>
                        <form action="/admin/orders/update/{{ $order->id }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="Rejected">
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Tolak pembayaran pesanan ini? Stok buku akan otomatis dikembalikan.')">
                                ❌ Tolak Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <form action="/admin/orders/update/{{ $order->id }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Ubah Status Manual</label>
                    <select name="status" class="form-control" {{ in_array($order->status, ['Completed','Rejected','Cancelled']) ? 'disabled' : '' }}>
                        @if($order->status === 'Waiting Confirmation')
                            <option value="Waiting Confirmation" selected>Menunggu Konfirmasi Admin</option>
                            <option value="Processing">Processing (ACC &amp; Dikemas)</option>
                            <option value="Rejected">Rejected (Tolak Pembayaran)</option>
                        @elseif($order->status === 'Processing')
                            <option value="Processing" selected>Processing (Dikemas)</option>
                            <option value="Shipped">Shipped (Dikirim)</option>
                            <option value="Rejected">Rejected (Tolak)</option>
                        @elseif($order->status === 'Shipped')
                            <option value="Shipped" selected>Shipped (Dikirim)</option>
                            <option value="Completed">Completed (Selesai)</option>
                        @else
                            <option value="{{ $order->status }}" selected>{{ $order->status }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Resi Pengiriman</label>
                    <input type="text" name="tracking_number" class="form-control" placeholder="Contoh: JNE123456789" value="{{ $order->tracking_number }}" {{ in_array($order->status, ['Completed','Rejected','Cancelled']) ? 'readonly' : '' }}>
                    <small class="text-muted">Isi nomor resi saat status diubah menjadi Dikirim (Shipped).</small>
                </div>

                @if(!in_array($order->status, ['Completed','Rejected','Cancelled']))
                    <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                @else
                    <div class="alert alert-info" style="font-size:0.85rem; margin:0;">
                        Pesanan dengan status final ({{ $order->status }}) tidak dapat diubah lagi.
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
