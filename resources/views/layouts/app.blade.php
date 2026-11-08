<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - Rainpedia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        html, body { max-width: 100vw; overflow-x: hidden; }
    </style>
</head>
<body>
    <!-- NAVBAR PUBLIC -->
    <nav class="navbar">
        <div class="container navbar-inner">
            <a href="/" class="navbar-brand">
                <img src="{{ asset('assets/images/rainpedia.png') }}" alt="Rainpedia" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <span style="display:none;">RAINPEDIA</span>
            </a>
            <ul class="navbar-nav">
                <li><a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="/catalog" class="{{ request()->is('catalog') ? 'active' : '' }}">Katalog Buku</a></li>
                <li><a href="/about" class="{{ request()->is('about') ? 'active' : '' }}">Tentang Kami</a></li>
            </ul>
            <div class="navbar-actions">
                @auth
                    <a href="{{ auth()->user()->role === 'admin' ? '/admin/dashboard' : '/user/dashboard' }}" class="btn btn-outline btn-sm">Dashboard</a>
                @else
                    <a href="/login" class="btn btn-outline btn-sm">Login</a>
                    <a href="/register" class="btn btn-primary btn-sm">Daftar</a>
                @endauth
                <button class="navbar-hamburger" id="navbar-hamburger" aria-label="Menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="navbar-mobile-menu" id="navbar-mobile-menu">
            <a href="/">Beranda</a>
            <a href="/catalog">Katalog Buku</a>
            <a href="/about">Tentang Kami</a>
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? '/admin/dashboard' : '/user/dashboard' }}">Dashboard</a>
            @else
                <a href="/login">Login</a>
                <a href="/register" style="color:var(--primary);">Daftar Gratis</a>
            @endauth
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <!-- FOOTER PUBLIC -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top" style="grid-template-columns: 1.5fr 1fr 1fr 1.5fr;">
                <div class="footer-col">
                    <div class="footer-brand">
                        <img src="{{ asset('assets/images/rainpedia.png') }}" alt="Rainpedia" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                        <span style="display:none;">RAINPEDIA</span>
                    </div>
                    <p class="footer-tagline">Toko &amp; Perpustakaan Buku Modern.<br>Beli atau pinjam buku gratis dengan mudah.</p>
                </div>
                <div class="footer-col">
                    <h4 class="footer-col-title">Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="/">Beranda</a></li>
                        <li><a href="/catalog">Katalog Buku</a></li>
                        <li><a href="/about">Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 class="footer-col-title">Akun</h4>
                    <ul class="footer-links">
                        @auth
                            <li><a href="{{ auth()->user()->role === 'admin' ? '/admin/dashboard' : '/user/dashboard' }}">Dashboard</a></li>
                        @else
                            <li><a href="/login">Login</a></li>
                            <li><a href="/register">Daftar Gratis</a></li>
                        @endauth
                    </ul>
                </div>
                <!-- INFORMASI DEVELOPER -->
                <div class="footer-col" style="background:rgba(255,255,255,0.04); padding:16px 20px; border-radius:10px; border:1px solid rgba(255,255,255,0.08);">
                    <h4 class="footer-col-title" style="color:#60a5fa; margin-bottom:10px;">💬 Hubungi Developer</h4>
                    <p style="font-size:0.8rem; color:rgba(255,255,255,0.7); margin-bottom:12px; line-height:1.5;">
                        Ada masalah atau saran fitur tambahan? Hubungi developer langsung:
                    </p>
                    <ul class="footer-links" style="font-size:0.85rem; gap:8px;">
                        <li>
                            <a href="https://wa.me/6281547184307" target="_blank" style="display:flex; align-items:center; gap:8px;">
                                <span>📱 WhatsApp:</span> <strong>081547184307</strong>
                            </a>
                        </li>
                        <li>
                            <a href="https://instagram.com/eddlyaa__" target="_blank" style="display:flex; align-items:center; gap:8px;">
                                <span>📸 Instagram:</span> <strong>@eddlyaa__</strong>
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/zegow8" target="_blank" style="display:flex; align-items:center; gap:8px;">
                                <span>💻 GitHub:</span> <strong>@zegow8</strong>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} Rainpedia. All rights reserved.</span>
                <span>Dibangun dengan Laravel &amp; MySQL. Developer: <strong>@eddlyaa__ / @zegow8</strong></span>
            </div>
        </div>
    </footer>
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
