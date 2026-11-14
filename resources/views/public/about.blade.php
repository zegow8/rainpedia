@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<div class="about-hero">
    <h1>Tentang Rainpedia</h1>
    <p>Platform buku independen untuk semua orang.</p>
</div>

<div style="max-width: 860px; margin: 0 auto;">

    <div class="panel" style="margin-bottom:24px;">
        <h2 style="font-size:1.3rem;margin-bottom:12px;">Siapa Kami?</h2>
        <p>Rainpedia adalah platform buku independen yang didirikan dengan satu tujuan: membuat buku lebih mudah diakses oleh semua orang. Kami percaya bahwa membaca adalah jendela dunia, dan setiap orang berhak mendapatkan akses ke pengetahuan tanpa hambatan biaya.</p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:20px; margin-bottom:24px;">
        <div class="panel" style="margin-bottom:0;border-top:4px solid var(--primary);">
            <div style="font-size:2rem;margin-bottom:12px;">🛒</div>
            <h3 style="font-size:1.1rem;margin-bottom:8px;">Beli Buku</h3>
            <p style="font-size:0.95rem;color:var(--text-muted);margin:0;">Dapatkan buku fisik favorit Anda dengan pengiriman ke seluruh Indonesia. Koleksi lengkap dari berbagai genre dan penulis terbaik.</p>
        </div>
        <div class="panel" style="margin-bottom:0;border-top:4px solid var(--success);">
            <div style="font-size:2rem;margin-bottom:12px;">📗</div>
            <h3 style="font-size:1.1rem;margin-bottom:8px;">Pinjam Buku Gratis</h3>
            <p style="font-size:0.95rem;color:var(--text-muted);margin:0;">Layanan peminjaman 100% gratis dengan durasi maksimal 7 hari. Karena tidak semua orang harus membeli untuk bisa membaca.</p>
        </div>
    </div>

    <div class="panel" style="margin-bottom:24px;background:#f8fafc;">
        <h2 style="font-size:1.3rem;margin-bottom:20px;">📋 Aturan Peminjaman</h2>
        <div style="display:grid;gap:16px;">
            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:36px;height:36px;background:var(--primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">1</div>
                <div>
                    <strong>Maks 2 buku sekaligus</strong>
                    <p style="margin:4px 0 0;font-size:0.9rem;color:var(--text-muted);">Setiap pengguna dapat meminjam maksimal 2 buku secara bersamaan.</p>
                </div>
            </div>
            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:36px;height:36px;background:var(--primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">2</div>
                <div>
                    <strong>Durasi maks 7 hari</strong>
                    <p style="margin:4px 0 0;font-size:0.9rem;color:var(--text-muted);">Buku harus dikembalikan dalam 7 hari sejak peminjaman disetujui.</p>
                </div>
            </div>
            <div style="display:flex;gap:16px;align-items:flex-start;">
                <div style="width:36px;height:36px;background:var(--danger);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">!</div>
                <div>
                    <strong>Denda keterlambatan</strong>
                    <p style="margin:4px 0 0;font-size:0.9rem;color:var(--text-muted);">Keterlambatan pengembalian dikenakan denda Rp10.000 per hari.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="text-align:center;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white;border:none;">
        <h2 style="color:white;margin-bottom:12px;">Siap Mulai Membaca?</h2>
        <p style="color:rgba(255,255,255,0.85);margin-bottom:24px;">Daftar sekarang dan nikmati layanan peminjaman buku gratis.</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            @guest
                <a href="/register" class="btn" style="background:white;color:var(--primary);font-weight:700;">Daftar Sekarang</a>
                <a href="/catalog" class="btn" style="background:transparent;color:white;border-color:rgba(255,255,255,0.5);">Lihat Katalog</a>
            @else
                <a href="/catalog" class="btn" style="background:white;color:var(--primary);font-weight:700;">Jelajahi Katalog</a>
            @endguest
        </div>
    </div>

</div>
@endsection