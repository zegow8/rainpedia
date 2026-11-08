@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-eyebrow">📚 Toko &amp; Perpustakaan Digital</div>
        <h1 class="hero-title">Temukan Dunia Baru<br>Lewat Membaca.</h1>
        <p class="hero-desc">Beli buku favoritmu atau pinjam secara <strong>gratis</strong>. Rainpedia adalah tempatnya bagi para pembaca sejati untuk menjelajahi ribuan cerita dan ilmu.</p>
        <div class="hero-actions">
            <a href="/catalog" class="btn btn-primary btn-lg">Jelajahi Katalog</a>
            @guest
                <a href="/register" class="btn btn-outline btn-lg">Daftar Gratis</a>
            @endguest
        </div>
        <div class="hero-features">
            <div class="hero-feature-item">✅ Peminjaman Gratis</div>
            <div class="hero-feature-item">✅ Koleksi Lengkap</div>
            <div class="hero-feature-item">✅ Daftar Mudah</div>
        </div>
    </div>
    <div class="hero-image-wrapper">
        <img src="{{ asset('assets/images/perpusrain.jpg') }}" alt="Perpustakaan Rainpedia" onerror="this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&q=80'">
    </div>
</section>

<!-- STATISTIK -->
<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number">{{ number_format($totalBooks) }}+</div>
            <div class="stat-label">Judul Buku</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($totalUsers) }}+</div>
            <div class="stat-label">Pengguna Aktif</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-number">{{ number_format($totalBorrow) }}+</div>
            <div class="stat-label">Buku Dipinjamkan</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-number">100%</div>
            <div class="stat-label">Pinjam Gratis</div>
        </div>
    </div>
</section>

<!-- BUKU TERBARU -->
<section style="padding: 56px 0 24px;">
    <div class="flex-between mb-4">
        <div>
            <h2 style="margin-bottom:4px;">Buku Terbaru</h2>
            <p class="text-muted" style="font-size:0.9rem;margin:0;">Koleksi buku yang baru ditambahkan</p>
        </div>
        <a href="/catalog" style="font-weight:600;white-space:nowrap;">Lihat Semua &rarr;</a>
    </div>

    <div class="book-grid">
        @foreach ($latestBooks as $b)
            <div class="book-card">
                <a href="/book/{{ $b->id }}" class="book-cover">
                    @if ($b->cover)
                        <img src="{{ asset('uploads/covers/' . $b->cover) }}" alt="{{ $b->title }}">
                    @else
                        <div class="book-cover-placeholder">{{ $b->title }}</div>
                    @endif
                    @if ($b->stock_borrow > 0)
                        <span class="book-borrow-badge">Gratis Pinjam</span>
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
</section>

<!-- PINJAM GRATIS BANNER (Yang tadi ilang) -->
@if (!$borrowableBooks->isEmpty())
<section style="padding: 32px 0 56px;">
    <div class="free-borrow-banner">
        <div class="free-borrow-info">
            <div class="free-borrow-eyebrow">🎉 Layanan Gratis</div>
            <h2 style="color:white;margin-bottom:8px;">Pinjam Buku Gratis Sekarang</h2>
            <p style="color:rgba(255,255,255,0.85);margin-bottom:0;">Tidak perlu bayar apapun. Pinjam buku hingga 7 hari, cukup kembalikan tepat waktu.</p>
        </div>
        <div class="free-borrow-books">
            @foreach ($borrowableBooks as $b)
                <a href="/book/{{ $b->id }}" class="free-borrow-book-thumb">
                    @if ($b->cover)
                        <img src="{{ asset('uploads/covers/' . $b->cover) }}" alt="{{ $b->title }}">
                    @else
                        <div class="free-borrow-placeholder">{{ $b->title }}</div>
                    @endif
                </a>
            @endforeach
        </div>
        <div>
            <a href="{{ auth()->check() ? '/catalog' : '/register' }}" class="btn" style="background:white;color:var(--primary);font-weight:700;">
                {{ auth()->check() ? 'Lihat Buku Tersedia' : 'Daftar & Pinjam Gratis' }}
            </a>
        </div>
    </div>
</section>
@endif

<!-- CARA KERJA -->
<section style="padding: 32px 0 72px;">
    <div class="text-center" style="margin-bottom:40px;">
        <h2>Cara Kerja Rainpedia</h2>
        <p class="text-muted">Mudah, cepat, dan gratis untuk peminjaman</p>
    </div>
    <div class="how-grid">
        <div class="how-step">
            <div class="how-step-num">1</div>
            <h3 class="how-step-title">Daftar Akun</h3>
            <p class="how-step-desc">Buat akun gratis dengan email kamu. Proses hanya butuh 1 menit.</p>
        </div>
        <div class="how-arrow">&#8594;</div>
        <div class="how-step">
            <div class="how-step-num">2</div>
            <h3 class="how-step-title">Pilih Buku</h3>
            <p class="how-step-desc">Jelajahi ribuan koleksi buku dari berbagai genre dan kategori.</p>
        </div>
        <div class="how-arrow">&#8594;</div>
        <div class="how-step">
            <div class="how-step-num">3</div>
            <h3 class="how-step-title">Pinjam atau Beli</h3>
            <p class="how-step-desc">Ajukan peminjaman gratis atau beli buku fisik untuk dimiliki.</p>
        </div>
        <div class="how-arrow">&#8594;</div>
        <div class="how-step">
            <div class="how-step-num">4</div>
            <h3 class="how-step-title">Nikmati!</h3>
            <p class="how-step-desc">Baca dan nikmati buku pilihanmu. Kembalikan tepat waktu untuk pinjaman.</p>
        </div>
    </div>
</section>
@endsection