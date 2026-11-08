@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div style="margin-bottom: 24px;">
    <a href="/catalog" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-muted);font-weight:600;font-size:0.9rem;">
        &#8592; Kembali ke Katalog
    </a>
</div>

<div class="book-detail-wrap">
    <!-- Cover -->
    <div class="book-detail-cover-col">
        <div class="book-detail-cover">
            @if ($book->cover)
                <img src="{{ asset('uploads/covers/' . $book->cover) }}" alt="{{ $book->title }}">
            @else
                <div class="book-cover-placeholder" style="height:100%;font-size:1.2rem;">{{ $book->title }}</div>
            @endif
        </div>
        @if ($book->category)
            <div style="margin-top:12px;text-align:center;">
                <span class="book-badge">{{ $book->category->name }}</span>
            </div>
        @endif
    </div>

    <!-- Info -->
    <div class="book-detail-info">
        <h1 style="font-size:2rem;margin-bottom:6px;">{{ $book->title }}</h1>
        <p style="font-size:1.1rem;color:var(--text-muted);margin-bottom:20px;">oleh <strong>{{ $book->author }}</strong></p>

        <div class="book-meta-grid">
            @if ($book->publisher)
            <div class="book-meta-item">
                <span class="book-meta-label">Penerbit</span>
                <span class="book-meta-val">{{ $book->publisher }}</span>
            </div>
            @endif
            @if ($book->year)
            <div class="book-meta-item">
                <span class="book-meta-label">Tahun</span>
                <span class="book-meta-val">{{ $book->year }}</span>
            </div>
            @endif
            @if ($book->isbn)
            <div class="book-meta-item">
                <span class="book-meta-label">ISBN</span>
                <span class="book-meta-val">{{ $book->isbn }}</span>
            </div>
            @endif
            @if ($book->category)
            <div class="book-meta-item">
                <span class="book-meta-label">Kategori</span>
                <span class="book-meta-val">{{ $book->category->name }}</span>
            </div>
            @endif
        </div>

        @if ($book->description)
        <div style="margin-bottom:28px;">
            <h3 style="font-size:1rem;margin-bottom:8px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Deskripsi</h3>
            <p style="color:var(--text-main);line-height:1.75;">{!! nl2br(e($book->description)) !!}</p>
        </div>
        @endif

        <!-- Harga & Aksi -->
        <div class="book-action-box">
            <div class="book-action-pricing">
                <div class="book-action-price-block">
                    <div class="book-action-label">Harga Beli</div>
                    <div class="book-action-price">Rp {{ number_format($book->price, 0, ',', '.') }}</div>
                    <div class="stock-pill {{ $book->stock_sale > 0 ? 'stock-ok' : 'stock-empty' }}">
                        {{ $book->stock_sale > 0 ? 'Stok: ' . $book->stock_sale : 'Stok Habis' }}
                    </div>
                </div>
                <div class="book-action-divider"></div>
                <div class="book-action-price-block">
                    <div class="book-action-label">Pinjam</div>
                    <div class="book-action-price" style="color:var(--success);">GRATIS</div>
                    <div class="stock-pill {{ $book->stock_borrow > 0 ? 'stock-ok' : 'stock-empty' }}">
                        {{ $book->stock_borrow > 0 ? 'Stok: ' . $book->stock_borrow : 'Stok Habis' }}
                    </div>
                </div>
            </div>

            <!-- AKSI DETAIL BUKU (MASUKKAN KERANJANG - BELI SEKARANG - PINJAM SEKARANG) -->
            <div class="book-action-buttons" style="display:flex; flex-direction:column; gap:10px;">
                @auth
                    @if(auth()->user()->role === 'user')
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <!-- 1. Masukkan Keranjang -->
                            <form action="/user/cart/add" method="POST" style="flex:1; min-width:180px; margin:0;">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <button type="submit" class="btn btn-outline btn-block" style="font-weight:700; height:46px;" {{ $book->stock_sale <= 0 ? 'disabled' : '' }}>
                                    🛒 Masukkan Keranjang
                                </button>
                            </form>

                            <!-- 2. Beli Sekarang (Langsung ke Checkout) -->
                            <form action="/user/cart/add" method="POST" style="flex:1; min-width:180px; margin:0;">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" class="btn btn-primary btn-block" style="font-weight:700; height:46px; background:#2563eb;" {{ $book->stock_sale <= 0 ? 'disabled' : '' }}>
                                    ⚡ Beli Sekarang
                                </button>
                            </form>
                        </div>

                        <!-- 3. Pinjam Sekarang (Gratis) -->
                        <div>
                            <a href="/user/borrow/{{ $book->id }}" 
                               class="btn btn-block" 
                               style="height:46px; display:flex; align-items:center; justify-content:center; gap:8px; font-weight:700; text-decoration:none; background:#10b981; border:1px solid #10b981; color:white; border-radius:var(--radius-md); transition:all 0.2s; {{ $book->stock_borrow <= 0 ? 'pointer-events:none; opacity:0.5; background:#94a3b8; border-color:#94a3b8;' : '' }}">
                                📖 Pinjam Sekarang (100% Gratis)
                            </a>
                        </div>
                    @else
                        <div style="background:#eff6ff; border:1px solid #bfdbfe; padding:12px; border-radius:8px; text-align:center; font-size:0.88rem; color:#1e40af;">
                            Anda sedang login sebagai <strong>Administrator</strong>. Silakan beralih ke akun user untuk berbelanja atau meminjam.
                        </div>
                    @endif
                @else
                    <a href="/login" class="btn btn-primary btn-block" style="height:48px; display:flex; align-items:center; justify-content:center; font-weight:700;">
                        Login untuk Beli / Pinjam Buku Ini
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Buku Rekomendasi -->
@if (!$relatedBooks->isEmpty())
<div style="margin-top:56px;">
    <div class="flex-between mb-4">
        <h2>Buku Lainnya dari Kategori Ini</h2>
        <a href="/catalog?category={{ $book->category_id }}" style="font-weight:600;">Lihat Semua &rarr;</a>
    </div>
    <div class="book-grid">
        @foreach ($relatedBooks as $r)
            <div class="book-card">
                <a href="/book/{{ $r->id }}" class="book-cover">
                    @if ($r->cover)
                        <img src="{{ asset('uploads/covers/' . $r->cover) }}" alt="{{ $r->title }}">
                    @else
                        <div class="book-cover-placeholder">{{ $r->title }}</div>
                    @endif
                    @if ($r->stock_borrow > 0)
                        <span class="book-borrow-badge">Gratis Pinjam</span>
                    @endif
                </a>
                <a href="/book/{{ $r->id }}" class="book-title">{{ $r->title }}</a>
                <div class="book-author">{{ $r->author }}</div>
                <div class="book-price">Rp {{ number_format($r->price, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
