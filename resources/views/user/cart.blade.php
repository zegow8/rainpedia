@extends('layouts.user')

@section('title', 'Keranjang Belanja')

@section('content')
<style>
    @media (max-width: 868px) {
        .cart-wrapper-flex { flex-direction: column !important; }
        .cart-summary-col { width: 100% !important; max-width: 100% !important; }
        .cart-summary-panel { position: static !important; }
        .cart-item-card { flex-wrap: wrap; }
    }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Keranjang Belanja</h1>
        <p class="text-muted" style="margin:0;">Pilih buku yang ingin Anda checkout dan selesaikan pembayaran</p>
    </div>
    <a href="/user/catalog" class="btn btn-outline btn-sm">+ Tambah Buku Lain</a>
</div>

@if($items->isEmpty())
    <div class="panel text-center" style="padding:56px 32px;">
        <div style="font-size:3rem; margin-bottom:12px;">🛒</div>
        <h3>Keranjang Belanja Kosong</h3>
        <p class="text-muted">Belum ada buku yang Anda masukkan ke keranjang.</p>
        <a href="/user/catalog" class="btn btn-primary mt-4">Mulai Belanja Sekarang</a>
    </div>
@else
<form id="cart-form" action="/user/checkout" method="GET">
    <div style="display:flex; gap:28px; align-items:flex-start; flex-wrap:wrap;" class="cart-wrapper-flex">
        
        <!-- KOLOM DAFTAR ITEM KERANJANG -->
        <div style="flex:1; min-width:0; width:100%;">
            
            <!-- HEADER PILIH SEMUA -->
            <div style="background:white; border:1px solid var(--border); border-radius:8px; padding:12px 16px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; font-size:0.9rem; margin:0;">
                    <input type="checkbox" id="select-all-checkbox" style="width:18px; height:18px; cursor:pointer;" checked>
                    <span>Pilih Semua Buku Tersedia (<span id="available-count">{{ $inStockItems->count() }}</span>)</span>
                </label>
                <span class="text-muted" style="font-size:0.8rem;">Hanya buku dengan stok tersedia yang dapat dipilih</span>
            </div>

            <!-- LIST ITEM KERANJANG -->
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach($items as $it)
                    @php
                        $book = $it->book;
                        $isOutOfStock = !$book || $book->stock_sale <= 0;
                        $isInsufficientStock = $book && $book->stock_sale > 0 && $it->qty > $book->stock_sale;
                    @endphp

                    <div class="cart-item-card" style="background:{{ $isOutOfStock ? '#fef2f2' : 'white' }}; border:1.5px solid {{ $isOutOfStock ? '#fecaca' : 'var(--border)' }}; border-radius:10px; padding:16px; display:flex; gap:14px; align-items:center; box-shadow:0 1px 3px rgba(0,0,0,0.04); {{ $isOutOfStock ? 'opacity:0.8;' : '' }}">
                        
                        <!-- Checkbox Pilihan -->
                        <div style="display:flex; align-items:center;">
                            @if(!$isOutOfStock)
                                <input type="checkbox" 
                                       name="items[]" 
                                       value="{{ $it->id }}" 
                                       class="item-checkbox" 
                                       data-price="{{ $book->price }}" 
                                       data-qty="{{ $it->qty }}" 
                                       data-subtotal="{{ $book->price * $it->qty }}"
                                       style="width:20px; height:20px; cursor:pointer;" 
                                       checked>
                            @else
                                <input type="checkbox" disabled style="width:20px; height:20px; cursor:not-allowed; opacity:0.3;" title="Stok habis, tidak dapat dipilih">
                            @endif
                        </div>

                        <!-- Cover Buku -->
                        <div style="width:60px; height:84px; background:#eee; border-radius:6px; overflow:hidden; flex-shrink:0; border:1px solid var(--border);">
                            @if($book && $book->cover)
                                <img src="{{ asset('uploads/covers/' . $book->cover) }}" alt="{{ $book->title }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.5rem;">📖</div>
                            @endif
                        </div>

                        <!-- Info Buku & Status Stok -->
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px;">
                                <div>
                                    <strong style="font-size:0.95rem; line-height:1.3; color:var(--text-main); display:block;">
                                        {{ $book->title ?? 'Buku Dihapus' }}
                                    </strong>
                                    <small class="text-muted">{{ $book->author ?? '-' }}</small>
                                </div>

                                <!-- Badge Status Stok -->
                                @if($isOutOfStock)
                                    <span class="status-badge" style="background:#fee2e2; color:#991b1b; font-size:0.75rem; padding:4px 8px; font-weight:800; border:1px solid #f87171;">
                                        ❌ STOK KOSONG
                                    </span>
                                @elseif($isInsufficientStock)
                                    <span class="status-badge" style="background:#fef3c7; color:#92400e; font-size:0.75rem; padding:4px 8px;">
                                        ⚠️ Sisa Stok: {{ $book->stock_sale }}
                                    </span>
                                @else
                                    <span class="status-badge" style="background:#dcfce7; color:#166534; font-size:0.75rem;">
                                        Stok: {{ $book->stock_sale }}
                                    </span>
                                @endif
                            </div>

                            <div style="margin-top:8px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                                <div style="font-weight:800; color:var(--primary); font-size:1rem;">
                                    Rp {{ number_format($book->price ?? 0, 0, ',', '.') }}
                                </div>

                                @if($isOutOfStock)
                                    <span style="font-size:0.8rem; color:#991b1b; font-weight:600;">
                                        Buku ini telah habis dibeli pengguna lain.
                                    </span>
                                @elseif($isInsufficientStock)
                                    <span style="font-size:0.8rem; color:#b45309; font-weight:600;">
                                        Harap turunkan jumlah pembelian agar sesuai stok.
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Update Qty & Hapus -->
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                            @if(!$isOutOfStock)
                                <div style="display:flex; align-items:center; gap:4px;">
                                    <input type="number" 
                                           id="qty-input-{{ $it->id }}" 
                                           value="{{ $it->qty }}" 
                                           min="1" 
                                           max="{{ $book->stock_sale }}" 
                                           class="form-control" 
                                           style="width:65px; padding:4px 8px; font-size:0.85rem; text-align:center;">
                                    <button type="button" 
                                            class="btn btn-outline btn-sm" 
                                            style="padding:5px 8px; font-size:0.75rem;" 
                                            onclick="updateCartItem({{ $it->id }})">
                                        Ubah
                                    </button>
                                </div>
                            @endif

                            <button type="button" 
                                    class="btn btn-danger btn-sm" 
                                    style="padding:5px 10px; font-size:0.75rem;" 
                                    onclick="removeCartItem({{ $it->id }})">
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($outOfStockItems->count() > 0)
                <div style="margin-top:14px; padding:10px 14px; background:#fef2f2; border:1px dashed #fca5a5; border-radius:8px; font-size:0.825rem; color:#991b1b;">
                    ℹ️ <strong>Catatan:</strong> Terdapat {{ $outOfStockItems->count() }} buku yang stoknya telah habis di posisi paling bawah. Buku dengan stok habis tidak dapat dicheckout dan dapat Anda hapus dari keranjang.
                </div>
            @endif

        </div>

        <!-- KOLOM RINGKASAN PEMBAYARAN -->
        <div style="width:340px;" class="cart-summary-col">
            <div class="panel cart-summary-panel" style="position:sticky; top:90px;">
                <h3 class="panel-title mb-4">Ringkasan Belanja</h3>

                <div class="flex-between mb-2" style="font-size:0.9rem;">
                    <span class="text-muted">Total Buku Dipilih:</span>
                    <strong id="selected-items-count">0 item</strong>
                </div>

                <div class="flex-between mb-4" style="font-size:0.9rem;">
                    <span class="text-muted">Subtotal Harga:</span>
                    <strong id="selected-subtotal" style="font-size:1.1rem; color:var(--primary);">Rp 0</strong>
                </div>

                <div style="background:#f8fafc; border:1px solid var(--border); border-radius:6px; padding:10px 12px; margin-bottom:16px; font-size:0.8rem; color:var(--text-muted);">
                    🚚 Ongkos kirim flat Rp 10.000 akan ditambahkan pada halaman pembayaran.
                </div>

                <button type="submit" id="btn-checkout" class="btn btn-primary btn-block" style="padding:12px; font-size:0.95rem;">
                    Lanjut ke Pembayaran &rarr;
                </button>
                <div id="checkout-warning" style="display:none; color:var(--danger); font-size:0.78rem; font-weight:600; text-align:center; margin-top:8px;">
                    ⚠️ Pilih minimal 1 buku yang tersedia untuk melanjutkan.
                </div>
            </div>
        </div>

    </div>
</form>

<!-- FORM TERSEMBUNYI UNTUK UPDATE & REMOVE ITEM -->
<form id="action-update-form" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="qty" id="hidden-qty">
</form>

<form id="action-remove-form" method="POST" style="display:none;">
    @csrf
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const countEl = document.getElementById('selected-items-count');
    const subtotalEl = document.getElementById('selected-subtotal');
    const btnCheckout = document.getElementById('btn-checkout');
    const checkoutWarning = document.getElementById('checkout-warning');
    const cartForm = document.getElementById('cart-form');

    function calculateTotal() {
        let count = 0;
        let total = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                count += parseInt(cb.getAttribute('data-qty') || 1);
                total += parseFloat(cb.getAttribute('data-subtotal') || 0);
            }
        });

        countEl.textContent = count + ' item';
        subtotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');

        if (count === 0) {
            btnCheckout.disabled = true;
            btnCheckout.style.opacity = '0.5';
            btnCheckout.style.cursor = 'not-allowed';
            checkoutWarning.style.display = 'block';
        } else {
            btnCheckout.disabled = false;
            btnCheckout.style.opacity = '1';
            btnCheckout.style.cursor = 'pointer';
            checkoutWarning.style.display = 'none';
        }

        // Sinkronisasi status 'select all'
        const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = itemCheckboxes.length > 0 && allChecked;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            calculateTotal();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', calculateTotal);
    });

    cartForm.addEventListener('submit', function(e) {
        const checkedCount = Array.from(itemCheckboxes).filter(cb => cb.checked).length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 buku yang tersedia untuk dicheckout.');
        }
    });

    // Inisialisasi hitungan saat load
    calculateTotal();
});

function updateCartItem(id) {
    const qtyInput = document.getElementById('qty-input-' + id);
    const qty = qtyInput.value;
    if (qty < 1) {
        alert('Jumlah minimal adalah 1.');
        return;
    }
    const form = document.getElementById('action-update-form');
    form.action = '/user/cart/update/' + id;
    document.getElementById('hidden-qty').value = qty;
    form.submit();
}

function removeCartItem(id) {
    if (confirm('Hapus buku ini dari keranjang?')) {
        const form = document.getElementById('action-remove-form');
        form.action = '/user/cart/remove/' + id;
        form.submit();
    }
}
</script>
@endif
@endsection
