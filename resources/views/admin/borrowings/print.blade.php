<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman Buku - Rainpedia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 30px;
            color: #1e293b;
            background: #fff;
            font-size: 11px;
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
            grid-template-columns: repeat(4, 1fr);
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
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 10.5px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
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
        .badge-borrowed { background: #dbeafe; color: #1e40af; }
        .badge-returned { background: #dcfce7; color: #15803d; }
        .badge-overdue { background: #fee2e2; color: #b91c1c; }
        .badge-pending { background: #fef3c7; color: #b45309; }
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
            <h1 class="brand-title">RAINPEDIA PERPUSTAKAAN DIGITAL</h1>
            <p class="brand-sub">Sistem Informasi Sirkulasi Peminjaman & Pengembalian Buku</p>
            <p class="brand-sub">Jl. Literasi Cendekia No. 42, Kota Pelajar | support@rainpedia.com</p>
        </div>
        <div class="doc-title">
            <h2>Laporan Peminjaman</h2>
            <div style="font-size:11px; color:#64748b;">No. Dokumen: RP-BRW-{{ date('Ymd-His') }}</div>
        </div>
    </div>

    <!-- META LAPORAN -->
    <div class="meta-box">
        <div class="meta-item">
            <strong>Periode Laporan</strong>
            {{ $period }}
        </div>
        <div class="meta-item">
            <strong>Filter Status</strong>
            {{ $request->filled('status') ? $request->status : 'Semua Status' }}
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

    <!-- RINGKASAN DATA -->
    <div class="summary-cards">
        <div class="summary-card">
            <span>Total Catatan</span>
            <h3>{{ $borrows->count() }} Data</h3>
        </div>
        <div class="summary-card">
            <span>Sedang Dipinjam</span>
            <h3 style="color:#2563eb;">{{ $borrows->where('status', 'Borrowed')->count() }} Buku</h3>
        </div>
        <div class="summary-card">
            <span>Terlambat (Overdue)</span>
            <h3 style="color:#dc2626;">{{ $borrows->where('status', 'Overdue')->count() }} Buku</h3>
        </div>
        <div class="summary-card" style="background:#f0fdf4; border-color:#bbf7d0;">
            <span style="color:#166534;">Sudah Dikembalikan</span>
            <h3 style="color:#15803d;">{{ $borrows->where('status', 'Returned')->count() }} Buku</h3>
        </div>
    </div>

    <!-- TABEL DATA -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 140px;">Peminjam / Siswa</th>
                <th>Judul Buku</th>
                <th style="width: 80px;">Tgl Pinjam</th>
                <th style="width: 80px;">Tenggat</th>
                <th style="width: 90px;">Status</th>
                <th style="width: 80px;" class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $index => $b)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $b->user->name ?? 'User' }}</strong>
                    <div style="font-size:9.5px; color:#64748b;">{{ $b->user->email ?? '-' }}</div>
                </td>
                <td>
                    <strong>{{ $b->book->title ?? 'Buku Dihapus' }}</strong>
                    <div style="font-size:9.5px; color:#64748b;">Penulis: {{ $b->book->author ?? '-' }}</div>
                </td>
                <td>{{ $b->start_date ? date('d/m/Y', strtotime($b->start_date)) : '-' }}</td>
                <td>{{ $b->due_date ? date('d/m/Y', strtotime($b->due_date)) : '-' }}</td>
                <td>
                    @if($b->status === 'Borrowed')
                        <span class="badge badge-borrowed">Sedang Dipinjam</span>
                    @elseif($b->status === 'Returned')
                        <span class="badge badge-returned">Dikembalikan</span>
                    @elseif($b->status === 'Overdue')
                        <span class="badge badge-overdue">Terlambat</span>
                    @elseif($b->status === 'Return Requested')
                        <span class="badge badge-pending">Menunggu ACC</span>
                    @elseif($b->status === 'Pending')
                        <span class="badge badge-pending">Pengajuan</span>
                    @else
                        <span class="badge">{{ $b->status }}</span>
                    @endif
                </td>
                <td class="text-right">
                    @if($b->fine)
                        <span style="color:{{ $b->fine->status === 'Paid' ? '#15803d' : '#dc2626' }}; font-weight:700;">
                            Rp {{ number_format($b->fine->amount, 0, ',', '.') }}
                            ({{ $b->fine->status }})
                        </span>
                    @else
                        <span style="color:#94a3b8;">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">
                    Tidak ada data peminjaman yang sesuai dengan filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
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
            <div style="font-weight:700;">Petugas Sirkulasi Buku</div>
            <div class="sig-space"></div>
            <div style="font-weight:700; text-decoration:underline;">{{ auth()->user()->name ?? 'Administrator' }}</div>
            <div style="font-size:10px; color:#64748b;">Petugas Perpustakaan</div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="action-bar">
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-close">✕ Tutup</button>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 600);
        };
    </script>
</body>
</html>
