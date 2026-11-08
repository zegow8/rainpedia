<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rainpedia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-inner">
            <a href="/" class="navbar-brand">
                <img src="{{ asset('assets/images/rainpedia.png') }}" alt="Rainpedia" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <span style="display:none;">RAINPEDIA</span>
            </a>
            <div class="navbar-actions">
                <a href="/" class="btn btn-outline btn-sm">Beranda</a>
            </div>
        </div>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="{{ asset('assets/images/rainpedia.png') }}" alt="Rainpedia" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <span style="display:none; font-size: 1.5rem; font-weight: 800; color: var(--primary);">RAINPEDIA</span>
            </div>
            <h1 class="auth-title">Masuk ke Akun</h1>
            <p class="auth-subtitle">Silakan login untuk mulai meminjam dan membeli buku.</p>

            @if(session('error'))
                <div class="alert alert-danger">⚠️ {{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="nama@email.com">
                    @error('email') <small style="color:var(--danger);">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    @error('password') <small style="color:var(--danger);">{{ $message }}</small> @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="auth-footer">
                Belum punya akun? <a href="/register">Daftar di sini</a>
            </div>
            <div class="auth-footer" style="margin-top: 8px; font-size: 0.8rem; color: var(--text-muted);">
                Admin: admin@rainpedia.com / admin123<br>
                User: user@rainpedia.com / user123
            </div>
        </div>
    </div>
</body>
</html>