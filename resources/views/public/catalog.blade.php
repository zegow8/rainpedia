@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<style>
.book-grid-5col { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-top: 20px; }
@media (max-width: 1100px) { .book-grid-5col { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 768px)  { .book-grid-5col { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 480px)  { .book-grid-5col { grid-template-columns: repeat(2, 1fr); } }

.pagination { display:flex; gap:6px; justify-content:center; margin-top:32px; align-items:center; }
.page-btn { display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:8px; border:1.5px solid var(--border-dark); font-weight:600; font-size:0.875rem; color:var(--text-muted); text-decoration:none; transition:all 0.2s; }
.page-btn:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.page-btn.active { background:var(--primary); color:white; border-color:var(--primary); }
.page-btn.disabled { opacity:0.4; pointer-events:none; }
.page-btn-wide { width:auto; padding:0 14px; }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Katalog Buku</h1>
        <p class="text-muted" style="font-size:0.9rem;margin:0;">Temukan buku favoritmu dari koleksi kami</p>
    </div>
</div>

<!-- Search & Filter -->
<div class="panel" style="margin-bottom:24px;">
    <form method="GET" action="/catalog">
        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:220px;">
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:var(--text-muted);">Cari Buku</label>
                <input type="text" name="q" class="form-control" placeholder="Judul, penulis, atau ISBN..." value="{{ request('q') }}">
            </div>
            <div style="width:200px;">
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:var(--text-muted);">Kategori</label>
                <select name="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width:180px;">
                <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:6px;color:var(--text-muted);">Urutkan</label>
                <select name="sort" class="form-control">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Judul A-Z</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:0;">
                <button type="submit" class="btn btn-primary">Cari</button>
                @if(request('q') || request('category'))
                    <a href="/catalog" class="btn btn-outline">Reset</a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Hasil -->
<div class="flex-between mb-4" style="align-items:center;">
    <div style="font-size:0.9rem;color:var(--text-muted);">
        Menampilkan <strong>{{ $books->total() }}</strong> hasil
        @if(request('q')) untuk "<strong>{{ request('q') }}</strong>" @endif
    </div>
</div>

<!-- Grid Buku -->
@if ($books->isEmpty())
    <div class="panel text-center" style="padding:56px 32px;">
        <div style="font-size:3rem;margin-bottom:16px;">📭</div>
        <h3>Buku Tidak Ditemukan</h3>
        <p class="text-muted">Tidak ada buku yang cocok dengan pencarian Anda.</p>
        <a href="/catalog" class="btn btn-outline" style="margin-top:8px;">Lihat Semua Buku</a>
    </div>
@else
    <div class="book-grid-5col">
        @foreach ($books as $b)
            <div class="book-card">
                <a href="/book/{{ $b->id }}" class="book-cover">
                    @if ($b->cover)
                        <img src="{{ asset('uploads/covers/' . $b->cover) }}" alt="{{ $b->title }}" loading="lazy">
                    @else
                        <div class="book-cover-placeholder">{{ $b->title }}</div>
                    @endif
                    @if ($b->stock_borrow > 0)
                        <span class="book-borrow-badge">Gratis Pinjam</span>
                    @elseif ($b->stock_sale <= 0)
                        <span class="book-borrow-badge" style="background:#94a3b8;">Stok Habis</span>
                    @endif
                </a>
                @if ($b->category)
                    <div style="margin-bottom:4px;">
                        <span class="book-badge">{{ $b->category->name }}</span>
                    </div>
                @endif
                <a href="/book/{{ $b->id }}" class="book-title">{{ $b->title }}</a>
                <div class="book-author">{{ $b->author }}</div>
                <div class="book-price">Rp {{ number_format($b->price, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $books->links() }}
    </div>
@endif
@endsection