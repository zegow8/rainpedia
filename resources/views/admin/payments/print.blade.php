<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan & Denda - Rainpedia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 30px;
            color: #1e293b;
            background: #fff;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 800;
            color: #1e40af;
            letter-spacing: 0.05em;
            margin: 0 0 4px 0;
        }
        .brand-sub {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }
        .doc-title {
            text-align: right;
        }
        .doc-title h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .meta-item {
            font-size: 11px;
        }
        .meta-item strong {
            display: block;
            color: #475569;
            text-transform: uppercase;
            font-size: 10px;
            margin-bottom: 2px;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            background: #fafafa;
        }
        .summary-card span {
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }
        .summary-card h3 {
            margin: 4px 0 0 0;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
        }
        .badge-order { background: #e0e7ff; color: #3730a3; }
        .badge-fine { background: #fef3c7; color: #92400e; }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sig-box {
            text-align: center;
            width: 200px;
        }
        .sig-space {
            height: 60px;
        }
        .action-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border: 1px solid #cbd5e1;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-print { background: #2563eb; color: white; }
        .btn-close { background: #e2e8f0; color: #334155; }
        @media print {
            body { padding: 0; }
            .action-bar { display: none !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <div class="header">
        <div>
            <h1 class="brand-title">RAINPEDIA PERPUSTAKAAN & BOOKSTORE</h1>
            <p class="brand-sub">Sistem Manajemen Toko Buku & Perpustakaan Digital Terintegrasi</p>
            <p class="brand-sub">Jl. Literasi Cendekia No. 42, Kota Pelajar | support@rainpedia.com</p>
        </div>
        <div class="doc-title">
            <h2>Laporan Keuangan</h2>
            <div style="font-size:11px; color:#64748b;">No. Dokumen: RP-FIN-{{ date('Ymd-His') }}</div>
        </div>
    </div>

    <!-- META LAPORAN -->
    <div class="meta-box">
        <div class="meta-item">
            <strong>Periode Laporan</strong>
            {{ $period }}
        </div>
        <div class="meta-item">
            <strong>Sumber Dana</strong>
            {{ $source === 'order' ? 'Hanya Pembelian Buku' : ($source === 'fine' ? 'Hanya Denda Peminjaman' : 'Semua Sumber (Pembelian & Denda)') }}
        </div>
        <div class="meta-item">
            <strong>Waktu Dicetak</strong>
            {{ date('d F Y, H:i') }} WIB
        </div>
        <div class="meta-item">
            <strong>Dicetak Oleh</strong>
            {{ auth()->user()->name ?? 'Administrator' }}
        </div>
    </div>

    <!-- RINGKASAN FINANSIAL -->
    <div class="summary-cards">
        <div class="summary-card">
            <span>Total Penjualan Buku</span>
            <h3 style="color:#2563eb;">Rp {{ number_format($totalOrders, 0, ',', '.') }}</h3>
        </div>
        <div class="summary-card">
            <span>Total Denda Keterlambatan</span>
            <h3 style="color:#d97706;">Rp {{ number_format($totalFines, 0, ',', '.') }}</h3>
        </div>
        <div class="summary-card" style="background:#f0fdf4; border-color:#bbf7d0;">
            <span style="color:#166534;">Grand Total Pemasukan</span>
            <h3 style="color:#15803d;">Rp {{ number_format($total, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- TABEL RINCIAN TRANSAKSI -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No</th>
                <th style="width: 110px;">Tanggal</th>
                <th style="width: 120px;">No. Referensi</th>
                <th>Sumber</th>
                <th>Keterangan & Pelanggan</th>
                <th class="text-right" style="width: 110px;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ date('d/m/Y H:i', strtotime($tx->date)) }}</td>
                <td><strong>{{ $tx->ref_code }}</strong></td>
                <td>
                    @if($tx->badge === 'order')
                        <span class="badge badge-order">Pembelian</span>
                    @else
                        <span class="badge badge-fine">Denda</span>
                    @endif
                </td>
                <td>{{ $tx->desc }}</td>
                <td class="text-right" style="font-weight:700;">
                    {{ number_format($tx->amount, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #94a3b8;">
                    Tidak ada transaksi dalam periode yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background:#f8fafc; font-weight:800;">
                <td colspan="5" class="text-right" style="font-size:11px;">TOTAL PEMASUKAN BERSIH :</td>
                <td class="text-right" style="color:#15803d; font-size:12px;">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- TANDA TANGAN PENGESAHAN -->
    <div class="signature-section">
        <div class="sig-box">
            <div>Mengetahui,</div>
            <div style="font-weight:700;">Kepala Perpustakaan Rainpedia</div>
            <div class="sig-space"></div>
            <div style="font-weight:700; text-decoration:underline;">Drs. H. Mulyadi, M.Pd.</div>
            <div style="font-size:10px; color:#64748b;">NIP. 19780512 200312 1 004</div>
        </div>

        <div class="sig-box">
            <div>Dicetak dan Diverifikasi,</div>
            <div style="font-weight:700;">Petugas Administrasi / Kasir</div>
            <div class="sig-space"></div>
            <div style="font-weight:700; text-decoration:underline;">{{ auth()->user()->name ?? 'Administrator' }}</div>
            <div style="font-size:10px; color:#64748b;">Petugas Rainpedia</div>
        </div>
    </div>

    <!-- ACTION FLOATING BUTTON (Hanya tampil di layar) -->
    <div class="action-bar">
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-close">✕ Tutup</button>
    </div>

    <script>
        // Otomatis munculkan print dialog saat halaman selesai termuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 600);
        };
    </script>
</body>
</html>
