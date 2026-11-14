@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<style>
    @media (max-width: 900px) {
        .admin-dashboard-grid { grid-template-columns: 1fr !important; }
        .admin-status-cards { grid-template-columns: repeat(3, 1fr) !important; gap: 8px !important; }
    }
    @media (max-width: 480px) {
        .admin-status-cards { grid-template-columns: repeat(2, 1fr) !important; }
    }
</style>
<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Dashboard Admin</h1>
        <p class="text-muted" style="margin:0;">Ringkasan sistem Rainpedia hari ini ({{ date('d M Y') }})</p>
    </div>
    <div>
        <a href="/admin/books/create" class="btn btn-primary btn-sm">+ Tambah Buku Baru</a>
    </div>
</div>

<!-- ============ RINGKASAN UTAMA ============ -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="panel" style="margin:0; padding:18px; border-left:4px solid var(--primary);">
        <div style="font-size:0.8rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Total Buku</div>
        <div style="font-size:1.8rem; font-weight:800; color:var(--text-main); margin-top:4px;">{{ $totalBooks }}</div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Jual: <strong>{{ $totalStockSale }}</strong> | Pinjam: <strong>{{ $totalStockBorrow }}</strong></div>
    </div>
    <div class="panel" style="margin:0; padding:18px; border-left:4px solid #8b5cf6;">
        <div style="font-size:0.8rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Total User</div>
        <div style="font-size:1.8rem; font-weight:800; color:var(--text-main); margin-top:4px;">{{ $totalUsers }}</div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Akun pembaca terdaftar</div>
    </div>
    <div class="panel" style="margin:0; padding:18px; border-left:4px solid #f59e0b;">
        <div style="font-size:0.8rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Total Pesanan</div>
        <div style="font-size:1.8rem; font-weight:800; color:var(--text-main); margin-top:4px;">{{ $totalOrders }}</div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Semua transaksi penjualan</div>
    </div>
    <div class="panel" style="margin:0; padding:18px; border-left:4px solid #06b6d4;">
        <div style="font-size:0.8rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Total Peminjaman</div>
        <div style="font-size:1.8rem; font-weight:800; color:var(--text-main); margin-top:4px;">{{ $totalBorrows }}</div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Semua riwayat peminjaman</div>
    </div>
    <div class="panel" style="margin:0; padding:18px; border-left:4px solid var(--success); background:#f0fdf4;">
        <div style="font-size:0.8rem; color:var(--success); font-weight:700; text-transform:uppercase;">Pendapatan Bulan Ini</div>
        <div style="font-size:1.5rem; font-weight:800; color:var(--success); margin-top:4px;">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Total Semua: Rp {{ number_format($revenueAllTime, 0, ',', '.') }}</div>
    </div>
</div>

<!-- ============ STATUS PEMBELIAN & STATUS PEMINJAMAN ============ -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;" class="admin-dashboard-grid">
    <!-- STATUS PEMBELIAN -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">📦 Status Pembelian</h3>
            <a href="/admin/orders" class="btn btn-outline btn-sm">Lihat Semua Order</a>
        </div>
        <div style="display:grid; grid-template-columns: repeat(6, 1fr); gap:8px; text-align:center;" class="admin-status-cards">
            <a href="/admin/orders?status=Waiting Confirmation" style="background:#ffedd5; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#c2410c;">{{ $ordWaiting }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#ea580c; margin-top:2px;">Menunggu ACC</div>
            </a>
            <a href="/admin/orders?status=Processing" style="background:#fef3c7; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#92400e;">{{ $ordProcessing }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#b45309; margin-top:2px;">Dikemas</div>
            </a>
            <a href="/admin/orders?status=Shipped" style="background:#e0e7ff; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#3730a3;">{{ $ordShipped }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#4338ca; margin-top:2px;">Dikirim</div>
            </a>
            <a href="/admin/orders?status=Completed" style="background:#dcfce7; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#166534;">{{ $ordCompleted }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#15803d; margin-top:2px;">Selesai</div>
            </a>
            <a href="/admin/orders?status=Cancelled" style="background:#f1f5f9; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#475569;">{{ $ordCancelled }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#64748b; margin-top:2px;">Batal</div>
            </a>
            <a href="/admin/orders?status=Rejected" style="background:#fee2e2; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#991b1b;">{{ $ordRejected }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#b91c1c; margin-top:2px;">Ditolak</div>
            </a>
        </div>
    </div>

    <!-- STATUS PEMINJAMAN -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">📚 Status Peminjaman</h3>
            <a href="/admin/borrowings" class="btn btn-outline btn-sm">Lihat Semua Pinjam</a>
        </div>
        <div style="display:grid; grid-template-columns: repeat(6, 1fr); gap:8px; text-align:center;" class="admin-status-cards">
            <a href="/admin/borrowings?status=Pending" style="background:#fef3c7; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#92400e;">{{ $bwPending }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#b45309; margin-top:2px;">Pending</div>
            </a>
            <a href="/admin/borrowings?status=Borrowed" style="background:#dbeafe; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#1e40af;">{{ $bwBorrowed }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#2563eb; margin-top:2px;">Borrowed</div>
            </a>
            <a href="/admin/borrowings?status=Overdue" style="background:#fee2e2; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#991b1b;">{{ $bwOverdue }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#b91c1c; margin-top:2px;">Overdue</div>
            </a>
            <a href="/admin/returns?status=Pending" style="background:#ffedd5; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#9a3412;">{{ $bwReturnRequested }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#c2410c; margin-top:2px;">Ret. Req</div>
            </a>
            <a href="/admin/borrowings?status=Returned" style="background:#dcfce7; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#166534;">{{ $bwReturned }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#15803d; margin-top:2px;">Returned</div>
            </a>
            <a href="/admin/borrowings?status=Rejected" style="background:#f1f5f9; padding:12px 4px; border-radius:8px; text-decoration:none; display:block;">
                <div style="font-size:1.3rem; font-weight:800; color:#475569;">{{ $bwRejected }}</div>
                <div style="font-size:0.7rem; font-weight:700; color:#64748b; margin-top:2px;">Rejected</div>
            </a>
        </div>
    </div>
</div>

<!-- ============ LISTS ROW 1: Pinjam Pending & Kembali Pending (DENGAN COVER) ============ -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;" class="admin-dashboard-grid">
    <!-- PINJAM PENDING -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">⏳ Pengajuan Pinjam Pending ({{ $bwPending }})</h3>
            <a href="/admin/borrowings?status=Pending" style="font-size:0.85rem; font-weight:600;">Proses Semua &rarr;</a>
        </div>
        @if($listBorrowPending->isEmpty())
            <p class="text-muted" style="margin:0; font-size:0.9rem;">Tidak ada pengajuan pinjam yang tertunda.</p>
        @else
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($listBorrowPending as $bp)
                    <div style="display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid var(--border);">
                        <div style="width:40px; height:56px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0;">
                            @if($bp->book && $bp->book->cover)
                                <img src="{{ asset('uploads/covers/' . $bp->book->cover) }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.2rem;">📖</div>
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:700; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $bp->book->title ?? 'Buku Dihapus' }}
                            </div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">
                                Peminjam: <strong>{{ $bp->user->name }}</strong> • {{ $bp->duration_days }} Hari
                            </div>
                        </div>
                        <a href="/admin/borrowings?status=Pending" class="btn btn-outline btn-sm" style="flex-shrink:0;">Review</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- KEMBALI PENDING -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">📥 Permintaan Kembali Pending ({{ $retPending }})</h3>
            <a href="/admin/returns" style="font-size:0.85rem; font-weight:600;">Proses Semua &rarr;</a>
        </div>
        @if($listReturnPending->isEmpty())
            <p class="text-muted" style="margin:0; font-size:0.9rem;">Tidak ada permintaan pengembalian tertunda.</p>
        @else
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($listReturnPending as $rp)
                    <div style="display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid var(--border);">
                        <div style="width:40px; height:56px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0;">
                            @if($rp->borrowing && $rp->borrowing->book && $rp->borrowing->book->cover)
                                <img src="{{ asset('uploads/covers/' . $rp->borrowing->book->cover) }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.2rem;">📖</div>
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:700; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $rp->borrowing->book->title ?? 'Buku Dihapus' }}
                            </div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">
                                Dari: <strong>{{ $rp->borrowing->user->name ?? '-' }}</strong>
                            </div>
                        </div>
                        <a href="/admin/returns" class="btn btn-outline btn-sm" style="flex-shrink:0;">ACC</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- ============ LISTS ROW 2: Pesanan Terbaru & Daftar User Terbaru ============ -->
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;" class="admin-dashboard-grid">
    <!-- PESANAN TERBARU -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">🛒 Pesanan Terbaru</h3>
            <a href="/admin/orders" style="font-size:0.85rem; font-weight:600;">Lihat Semua &rarr;</a>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($listRecentOrders as $o)
                <div style="display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid var(--border);">
                    <!-- Thumbnail buku pertama -->
                    <div style="width:40px; height:56px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0;">
                        @php $firstItem = $o->details->first(); @endphp
                        @if($firstItem && $firstItem->book && $firstItem->book->cover)
                            <img src="{{ asset('uploads/covers/' . $firstItem->book->cover) }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1.2rem;">📦</div>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:4px;">
                            <span style="font-weight:700; font-size:0.85rem; word-break:break-all;">{{ $o->invoice_code }}</span>
                            <span class="status-badge" style="background:#e0e7ff; color:#3730a3; font-size:0.7rem; white-space:nowrap;">{{ $o->status }}</span>
                        </div>
                        <div style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;">
                            {{ $o->user->name ?? 'User' }} • <strong>Rp {{ number_format($o->total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <a href="/admin/orders/{{ $o->id }}" class="btn btn-outline btn-sm" style="flex-shrink:0;">Detail</a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- USER TERBARU -->
    <div class="panel" style="margin:0;">
        <div class="panel-header flex-between">
            <h3 class="panel-title">👥 User Terbaru</h3>
            <a href="/admin/users" style="font-size:0.85rem; font-weight:600;">Lihat Semua &rarr;</a>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($listRecentUsers as $u)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-weight:700; font-size:0.9rem;">{{ $u->name }}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $u->email }} • Terdaftar: {{ date('d M Y', strtotime($u->created_at)) }}</div>
                    </div>
                    <div style="text-align:right;">
                        @if($u->activeBorrowings->count() > 0)
                            <span class="status-badge" style="background:#dbeafe; color:#1e40af;">Pinjam {{ $u->activeBorrowings->count() }} Buku</span>
                        @else
                            <span class="status-badge" style="background:#f1f5f9; color:#64748b;">Tidak Ada Pinjaman</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ============ LISTS ROW 3: Stok Menipis (DENGAN COVER) ============ -->
<div class="panel">
    <div class="panel-header flex-between">
        <h3 class="panel-title">⚠️ Stok Buku Menipis (Segera Restock)</h3>
        <a href="/admin/books" style="font-size:0.85rem; font-weight:600;">Kelola Stok &rarr;</a>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:60px;">Cover</th>
                    <th>Judul Buku</th>
                    <th>Harga</th>
                    <th>Stok Jual</th>
                    <th>Stok Pinjam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listLowStock as $b)
                <tr>
                    <td>
                        <div style="width:40px; height:56px; border-radius:4px; overflow:hidden; background:#eee;">
                            @if($b->cover)
                                <img src="{{ asset('uploads/covers/' . $b->cover) }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; align-items:center; justify-content:center; height:100%;">📚</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <strong>{{ $b->title }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $b->author }}</div>
                    </td>
                    <td>Rp {{ number_format($b->price, 0, ',', '.') }}</td>
                    <td>
                        <span style="font-weight:700; color: {{ $b->stock_sale < 5 ? 'var(--danger)' : 'inherit' }};">
                            {{ $b->stock_sale }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700; color: {{ $b->stock_borrow < 2 ? 'var(--danger)' : 'inherit' }};">
                            {{ $b->stock_borrow }}
                        </span>
                    </td>
                    <td>
                        <a href="/admin/books/edit/{{ $b->id }}" class="btn btn-outline btn-sm">Edit Stok</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
