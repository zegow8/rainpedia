@extends('layouts.user')

@section('title', 'Checkout Pembayaran')

@section('content')
<style>
    @media (max-width: 768px) {
        .checkout-grid { grid-template-columns: 1fr !important; gap: 16px !important; }
        .checkout-row-2col { grid-template-columns: 1fr !important; gap: 12px !important; }
        .checkout-summary-box { position: static !important; }
    }
</style>

<div class="mb-4">
    <a href="/user/cart" style="font-weight:600; font-size:0.9rem;">&larr; Kembali ke Keranjang</a>
    <h1 style="margin-top:8px; margin-bottom:4px;">Checkout Pembayaran</h1>
    <p class="text-muted" style="margin:0;">Lengkapi data pengiriman dan pilih metode pembayaran transfer bank</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:20px;">
        <ul style="margin:0; padding-left:20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="/user/checkout" method="POST">
    @csrf

    <!-- PASS ITEM IDS YANG TERPILIH DARI KERANJANG -->
    @foreach($items as $it)
        <input type="hidden" name="items[]" value="{{ $it->id }}">
    @endforeach

    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:24px; align-items:start;" class="checkout-grid">
        
        <!-- KOLOM KIRI: DATA PENGIRIMAN & METODE BAYAR -->
        <div>
            <!-- DATA PENERIMA & ALAMAT -->
            <div class="panel" style="margin-bottom:20px;">
                <h3 class="panel-title mb-4">📍 Data Pengiriman</h3>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;" class="checkout-row-2col">
                    <div class="form-group">
                        <label class="form-label">
                            Nama Penerima <span style="color:var(--danger); font-weight:800;">* (Wajib)</span>
                        </label>
                        <input type="text" 
                               name="receiver_name" 
                               class="form-control" 
                               required 
                               placeholder="Nama lengkap penerima"
                               value="{{ old('receiver_name', auth()->user()->name) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Nomor Handphone / WhatsApp <span style="color:var(--danger); font-weight:800;">* (Wajib)</span>
                        </label>
                        <input type="text" 
                               name="receiver_phone" 
                               class="form-control" 
                               required 
                               placeholder="Contoh: 08123456789"
                               value="{{ old('receiver_phone', auth()->user()->phone) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Alamat Lengkap Pengiriman <span style="color:var(--danger); font-weight:800;">* (Wajib)</span>
                    </label>
                    <textarea name="shipping_address" 
                              class="form-control" 
                              required 
                              style="min-height:90px;"
                              placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten, dan kode pos...">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Catatan Tambahan untuk Kurir (Opsional)</label>
                    <input type="text" 
                           name="note" 
                           class="form-control" 
                           placeholder="Contoh: Titip ke tetangga bila rumah kosong / pagar hitam"
                           value="{{ old('note') }}">
                </div>
            </div>

            <!-- METODE PEMBAYARAN (HAPUS COD: Hanya Transfer BCA & Mandiri) -->
            <div class="panel">
                <h3 class="panel-title mb-2">
                    💳 Metode Pembayaran <span style="color:var(--danger); font-weight:800; font-size:0.85rem;">* (Wajib Dipilih)</span>
                </h3>
                <p class="text-muted" style="font-size:0.85rem; margin-bottom:16px;">Pilih salah satu metode transfer bank. Pesanan Anda akan dikonfirmasi terlebih dahulu oleh admin sebelum dikemas:</p>
                
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <label style="display:flex; align-items:center; gap:14px; padding:16px; border:1.5px solid var(--border); border-radius:8px; cursor:pointer; background:white; transition:border-color 0.2s;">
                        <input type="radio" 
                               name="payment_method" 
                               value="Transfer BCA" 
                               required 
                               style="width:18px; height:18px; cursor:pointer;"
                               {{ old('payment_method', 'Transfer BCA') === 'Transfer BCA' ? 'checked' : '' }}>
                        <div style="flex:1;">
                            <strong style="color:var(--text-main); font-size:0.95rem;">Transfer Bank BCA</strong><br>
                            <span class="text-muted" style="font-size:0.825rem;">No. Rekening: <strong>123456789</strong> a.n PT Rainpedia Indonesia</span>
                        </div>
                    </label>

                    <label style="display:flex; align-items:center; gap:14px; padding:16px; border:1.5px solid var(--border); border-radius:8px; cursor:pointer; background:white; transition:border-color 0.2s;">
                        <input type="radio" 
                               name="payment_method" 
                               value="Transfer Mandiri" 
                               required 
                               style="width:18px; height:18px; cursor:pointer;"
                               {{ old('payment_method') === 'Transfer Mandiri' ? 'checked' : '' }}>
                        <div style="flex:1;">
                            <strong style="color:var(--text-main); font-size:0.95rem;">Transfer Bank Mandiri</strong><br>
                            <span class="text-muted" style="font-size:0.825rem;">No. Rekening: <strong>987654321</strong> a.n PT Rainpedia Indonesia</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: RINGKASAN BUKU DIPILIH -->
        <div class="panel checkout-summary-box" style="position:sticky; top:90px;">
            <h3 class="panel-title mb-4">📚 Rincian Buku ({{ $items->count() }} Judul)</h3>
            
            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px; max-height:300px; overflow-y:auto; padding-right:4px;">
                @foreach($items as $it)
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px; height:50px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0; border:1px solid var(--border);">
                            @if($it->book && $it->book->cover)
                                <img src="{{ asset('uploads/covers/'.$it->book->cover) }}" alt="{{ $it->book->title }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1rem;">📖</div>
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:0.85rem; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $it->book->title }}
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                {{ $it->qty }}x @ Rp {{ number_format($it->book->price, 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="font-size:0.85rem; font-weight:700; white-space:nowrap;">
                            Rp {{ number_format($it->book->price * $it->qty, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <hr style="border:0; border-top:1px solid var(--border); margin:12px 0;">
            
            <div class="flex-between mb-2" style="font-size:0.85rem;">
                <span class="text-muted">Subtotal Barang</span>
                <span style="font-weight:600;">Rp {{ number_format($totalBarang, 0, ',', '.') }}</span>
            </div>

            <div class="flex-between mb-2" style="font-size:0.85rem;">
                <span class="text-muted">Ongkos Kirim Flat</span>
                <span style="font-weight:600;">Rp {{ number_format($ongkir, 0, ',', '.') }}</span>
            </div>

            <div class="flex-between" style="margin-top:14px; padding-top:12px; border-top:1.5px dashed var(--border);">
                <strong style="font-size:1rem;">Total Pembayaran</strong>
                <strong style="font-size:1.3rem; color:var(--primary);">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px; padding:14px; font-size:1rem; font-weight:700;">
                Kirim &amp; Selesaikan Pemesanan &rarr;
            </button>
        </div>

    </div>
</form>
@endsection
