<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Rainpedia</title>
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
            <h1 class="auth-title">Buat Akun Baru</h1>
            <p class="auth-subtitle">Daftar untuk mulai meminjam dan membeli buku.</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0; padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/register" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Password (min. 6 karakter)</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
            </form>

            <div class="auth-footer">
                Sudah punya akun? <a href="/login">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>