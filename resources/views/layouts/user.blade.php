<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - User Area - Rainpedia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Layout User Area */
        html, body { max-width: 100vw; overflow-x: hidden; }
        body { display: flex; flex-direction: column; min-height: 100vh; background-color: var(--bg-main); }
        
        /* NAVBAR DIUBAH JADI TERANG */
        .navbar-user { background: var(--white); border-bottom: 2px solid var(--primary); padding: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .navbar-user-top { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; }
        
        .navbar-user .brand { font-size: 1.25rem; font-weight: 800; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .navbar-user .brand img { height: 36px; width: auto; }
        
        .navbar-user-icons { display: flex; gap: 20px; align-items: center; }
        .navbar-user-icons a { color: var(--text-main); font-size: 0.9rem; font-weight: 600; text-decoration: none; transition: color 0.2s; }
        .navbar-user-icons a:hover, .navbar-user-icons a.active { color: var(--primary); }
        .navbar-user-icons .logout-link { color: var(--danger) !important; }
        
        .navbar-user-bottom { border-top: 1px solid var(--border); display: flex; gap: 24px; padding: 0; overflow-x: auto; white-space: nowrap; }
        .navbar-user-bottom a { color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-decoration: none; padding: 14px 0; border-bottom: 3px solid transparent; transition: all 0.2s; }
        .navbar-user-bottom a:hover { color: var(--primary); background: var(--bg-main); }
        .navbar-user-bottom a.active { color: var(--primary); border-bottom-color: var(--primary); }
        
        .badge-notif { background: var(--danger); color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 10px; font-weight: 700; margin-left: 4px; vertical-align: middle; }
        
        .page-wrap { max-width: 1200px; width: 100%; margin: 0 auto; padding: 32px 24px; flex: 1; }
        @media (max-width: 768px) {
            .navbar-user-top { flex-direction: column; gap: 12px; align-items: flex-start; }
            .navbar-user-icons { width: 100%; justify-content: space-between; overflow-x: auto; white-space: nowrap; gap: 10px; padding-bottom: 4px; -webkit-overflow-scrolling: touch; font-size: 0.8rem; }
            .navbar-user-bottom { gap: 14px; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; }
            .navbar-user-bottom a { font-size: 0.85rem; padding: 10px 0; }
            .page-wrap { padding: 16px 12px; }
        }
    </style>
</head>
<body>

    <nav class="navbar-user">
        <div class="container">
            <!-- Baris Atas -->
            <div class="navbar-user-top">
                <a href="/user/dashboard" class="brand">
                    <img src="{{ asset('assets/images/rainpedia.png') }}" alt="Rainpedia" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                    <span style="display:none;">RAINPEDIA</span>
                </a>
                <div class="navbar-user-icons">
                    <!-- LINK KATALOG LO GAK GUE SENTUH -->
                    <a href="/user/catalog" class="{{ request()->is('user/catalog*') ? 'active' : '' }}">📚 Katalog Buku</a>
                    <a href="/user/cart" class="{{ request()->is('user/cart*') ? 'active' : '' }}">🛒 Keranjang</a>
                    <a href="/user/profile" class="{{ request()->is('user/profile*') ? 'active' : '' }}">👤 {{ auth()->user()->name }}</a>
                    <form action="/logout" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="logout-link" style="background:none;border:none;cursor:pointer;font-weight:600;font-size:0.9rem;font-family:inherit;">Logout</button>
                    </form>
                </div>
            </div>
            <!-- Baris Bawah (Tab) -->
            <div class="navbar-user-bottom">
                <a href="/user/dashboard" class="{{ request()->is('user/dashboard') ? 'active' : '' }}">Beranda</a>
                <!-- LINK KATALOG LO GAK GUE SENTUH -->
                <a href="/user/catalog" class="{{ request()->is('user/catalog*') ? 'active' : '' }}">Katalog Buku</a>
                <a href="/user/borrowings" class="{{ request()->is('user/borrowings*') ? 'active' : '' }}">Peminjaman Saya</a>
                <a href="/user/transactions" class="{{ request()->is('user/transactions*') ? 'active' : '' }}">Riwayat Pembelian</a>
                <a href="/user/chat" class="{{ request()->is('user/chat*') ? 'active' : '' }}">
                    💬 Tanya Admin
                    @php
                        $unreadChatCount = \App\Models\Chat::where('user_id', auth()->id())->where('sender', 'admin')->where('is_read', 0)->count();
                    @endphp
                    @if($unreadChatCount > 0)
                        <span class="badge-notif" style="background:#2563eb;">{{ $unreadChatCount }}</span>
                    @endif
                </a>
                <a href="/user/profile" class="{{ request()->is('user/profile') ? 'active' : '' }}">Pengaturan Profil</a>
                <a href="/user/notifications" class="{{ request()->is('user/notifications*') ? 'active' : '' }}">
                    Notifikasi
                    @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', 0)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge-notif">{{ $unreadCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </nav>

    <div class="page-wrap">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <!-- ============ FOOTER USER AREA (INFO DEVELOPER) ============ -->
    <footer style="background:#0F172A; color:white; border-top:1px solid #1e293b; padding:32px 0 20px; margin-top:auto;">
        <div class="container">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:24px; padding-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.1);">
                <div>
                    <div style="font-weight:800; font-size:1.1rem; color:white; margin-bottom:8px;">RAINPEDIA</div>
                    <p style="font-size:0.85rem; color:rgba(255,255,255,0.6); line-height:1.5; margin:0;">
                        Platform membaca, meminjam buku gratis, dan belanja buku favoritmu.
                    </p>
                </div>
                <div>
                    <div style="font-weight:700; font-size:0.85rem; text-transform:uppercase; color:#94a3b8; margin-bottom:8px;">Bantuan & Aturan</div>
                    <ul style="list-style:none; padding:0; margin:0; font-size:0.85rem; color:rgba(255,255,255,0.7); display:flex; flex-direction:column; gap:4px;">
                        <li>📚 Maksimal 2 buku pinjaman aktif</li>
                        <li>📅 Durasi pinjam maksimal 7 hari</li>
                        <li>💰 Denda keterlambatan Rp 10.000/hari</li>
                    </ul>
                </div>
                <!-- Developer Info -->
                <div style="background:rgba(255,255,255,0.05); padding:14px 18px; border-radius:8px; border:1px solid rgba(255,255,255,0.1);">
                    <div style="font-weight:700; font-size:0.85rem; text-transform:uppercase; color:#60a5fa; margin-bottom:6px;">
                        💬 Hubungi Developer
                    </div>
                    <div style="font-size:0.8rem; color:rgba(255,255,255,0.7); margin-bottom:8px;">
                        Ada masalah atau saran fitur tambahan?
                    </div>
                    <div style="display:flex; flex-direction:column; gap:6px; font-size:0.825rem;">
                        <a href="https://wa.me/6281547184307" target="_blank" style="color:#d1d5db; text-decoration:none;">
                            📱 WhatsApp: <strong style="color:white;">081547184307</strong>
                        </a>
                        <a href="https://instagram.com/eddlyaa__" target="_blank" style="color:#d1d5db; text-decoration:none;">
                            📸 Instagram: <strong style="color:white;">@eddlyaa__</strong>
                        </a>
                        <a href="https://github.com/zegow8" target="_blank" style="color:#d1d5db; text-decoration:none;">
                            💻 GitHub: <strong style="color:white;">@zegow8</strong>
                        </a>
                    </div>
                </div>
            </div>
            <div style="padding-top:16px; font-size:0.75rem; color:rgba(255,255,255,0.4); text-align:center;">
                &copy; {{ date('Y') }} Rainpedia. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>