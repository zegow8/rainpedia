<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Kesalahan Server - Rainpedia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            background-color: var(--bg-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            font-family: 'Inter', sans-serif;
        }
        .error-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 48px 40px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }
        .error-code {
            font-size: 5rem;
            font-weight: 800;
            color: #ef4444;
            line-height: 1;
            margin-bottom: 12px;
            letter-spacing: -0.04em;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 12px;
        }
        .error-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        @media (max-width: 480px) {
            .error-card { padding: 32px 18px !important; }
            .error-code { font-size: 3.5rem !important; }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div style="font-size: 3.5rem; margin-bottom: 8px;">🛠️⚠️</div>
        <div class="error-code">500</div>
        <div class="error-title">TERJADI KESALAHAN SERVER</div>
        <p class="error-desc">
            Sistem sedang mengalami kendala teknis internal. Tim kami telah diberitahu untuk segera memperbaikinya.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="/" class="btn btn-primary" style="font-weight:700;">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
