@extends('layouts.user')

@section('title', 'Katalog Buku')

@section('content')
<style>
/* 5 Kolom Memanjang x 3 Baris = 15 buku per halaman */
.user-book-grid { 
    display: grid; 
    grid-template-columns: repeat(5, 1fr); 
    gap: 18px; 
    margin-top: 20px; 
}
@media (max-width: 1200px) { .user-book-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 900px)  { .user-book-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px)  { 
    .user-book-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } 
    .catalog-filter-bar > div { width: 100% !important; min-width: 100% !important; }
}
@media (max-width: 360px)  { .user-book-grid { grid-template-columns: 1fr; } }

.user-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.user-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
.user-card-cover {
    aspect-ratio: 2 / 3;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
    background: #f1f5f9;
    margin-bottom: 10px;
    border: 1px solid var(--border);
    display: block;
}
.user-card-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.25s ease;
}
.user-card:hover .user-card-cover img {
    transform: scale(1.03);
}

.pagination { display:flex; gap:6px; justify-content:center; margin-top:32px; align-items:center; }
.pagination .page-item { list-style:none; }
.pagination .page-link { display:inline-flex; align-items:center; justify-content:center; min-width:38px; height:38px; padding:0 10px; border-radius:8px; border:1.5px solid var(--border-dark); font-weight:600; font-size:0.875rem; color:var(--text-muted); text-decoration:none; background:white; }
.pagination .page-item.active .page-link { background:var(--primary); color:white; border-color:var(--primary); }
.pagination .page-item.disabled .page-link { opacity:0.4; pointer-events:none; }
</style>

<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Katalog Buku</h1>
        <p class="text-muted" style="margin:0;">Jelajahi koleksi buku kami. Pinjam 100% gratis atau beli untuk koleksi pribadimu.</p>
    </div>
</div>

<!-- ============ SEARCH & FILTER PANEL ============ -->
<div class="panel" style="margin-bottom:24px; padding:18px;">
    <form method="GET" action="/user/catalog">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;" class="catalog-filter-bar">
            <div style="flex:1; min-width:220px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Cari Buku</label>
                <input type="text" name="q" class="form-control" placeholder="Judul, penulis, ISBN..." value="{{ request('q') }}">
            </div>
            <div style="width:200px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Kategori</label>
                <select name="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width:180px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px; color:var(--text-muted);">Urutkan</label>
                <select name="sort" class="form-control">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Judul A-Z</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary">Cari</button>
                @if(request('q') || request('category') || request('sort'))
                    <a href="/user/catalog" class="btn btn-outline">Reset</a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- ============ HASIL & PAGINATION INFO ============ -->
<div class="flex-between mb-2">
    <div style="font-size:0.9rem; color:var(--text-muted);">
        Menampilkan <strong>{{ $books->count() }}</strong> dari <strong>{{ $books->total() }}</strong> buku (Halaman {{ $books->currentPage() }})
    </div>
</div>

<!-- ============ GRID BUKU (5 KOLOM X 3 BARIS = 15 BUKU) ============ -->
@if ($books->isEmpty())
    <div class="panel text-center" style="padding:56px 32px;">
        <div style="font-size:3rem; margin-bottom:14px;">📭</div>
        <h3>Buku Tidak Ditemukan</h3>
        <p class="text-muted">Tidak ada buku yang sesuai dengan kriteria pencarian Anda.</p>
        <a href="/user/catalog" class="btn btn-outline" style="margin-top:8px;">Tampilkan Semua Buku</a>
    </div>
@else
    <div class="user-book-grid">
        @foreach ($books as $b)
            <div class="user-card">
                <a href="/book/{{ $b->id }}" class="user-card-cover">
                    @if ($b->cover)
                        <img src="{{ asset('uploads/covers/' . $b->cover) }}" alt="{{ $b->title }}" loading="lazy">
                    @else
                        <div style="display:flex; align-items:center; justify-content:center; height:100%; color:var(--primary-dark); font-weight:700; text-align:center; padding:10px; font-size:0.85rem; background:#eff6ff;">
                            {{ $b->title }}
                        </div>
                    @endif

                    <!-- Badge Status Stok Pinjam -->
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

                <a href="/book/{{ $b->id }}" style="font-size:0.9rem; font-weight:700; color:var(--text-main); margin-bottom:2px; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; text-decoration:none;" title="{{ $b->title }}">
                    {{ $b->title }}
                </a>
                
                <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $b->author }}
                </div>

                <div style="margin-top:auto; padding-top:8px; border-top:1px solid var(--border);">
                    <div style="font-size:0.95rem; font-weight:800; color:var(--primary); margin-bottom:8px;">
                        Rp {{ number_format($b->price, 0, ',', '.') }}
                    </div>
                    
                    <div style="display:flex; gap:6px;">
                        <a href="/book/{{ $b->id }}" class="btn btn-outline btn-sm" style="flex:1; padding:6px 4px; font-size:0.75rem; text-align:center;">
                            🔍 Detail
                        </a>
                        @if($b->stock_sale > 0)
                            <form action="/user/cart/add" method="POST" style="flex:1; margin:0;">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $b->id }}">
                                <input type="hidden" name="from_catalog" value="1">
                                <button type="submit" class="btn btn-primary btn-sm" style="width:100%; padding:6px 4px; font-size:0.75rem;">
                                    + Keranjang
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm" disabled style="flex:1; padding:6px 4px; font-size:0.75rem; opacity:0.5; background:#e2e8f0; color:#64748b; border:1px solid #cbd5e1; cursor:not-allowed;">
                                Habis
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination (15 buku per page) -->
    <div class="pagination">
        {{ $books->links() }}
    </div>
@endif
@endsection
