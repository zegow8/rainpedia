@extends('layouts.admin')
@section('title', 'Data User')
@section('content')
<div class="flex-between mb-4">
    <div>
        <h1 style="margin-bottom:4px;">Data Pengguna (User)</h1>
        <p class="text-muted" style="margin:0;">Daftar akun pembaca, status keaktifan, dan buku yang sedang aktif dipinjam</p>
    </div>
</div>

<div class="panel" style="padding:0; overflow:hidden;">
    <div style="padding:14px 20px; background:#f8fafc; border-bottom:1px solid var(--border); font-size:0.85rem; color:var(--text-muted);">
        Total: <strong>{{ count($users) }}</strong> pengguna terdaftar
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama & Kontak</th>
                    <th>Alamat</th>
                    <th>Buku yang Lagi Aktif Dipinjam</th>
                    <th>Status Akun</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td style="vertical-align:top;">
                        <strong style="font-size:0.95rem;">{{ $u->name }}</strong>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Email: {{ $u->email }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">HP: {{ $u->phone ?? '-' }}</div>
                        <div style="font-size:0.7rem; color:#94a3b8; margin-top:4px;">Gabung: {{ date('d M Y', strtotime($u->created_at)) }}</div>
                    </td>

                    <td style="vertical-align:top; max-width:180px;">
                        <div style="font-size:0.85rem; color:var(--text-muted);">
                            {{ $u->address ?? '-' }}
                        </div>
                    </td>

                    <!-- BUKU YANG LAGI AKTIF DIPINJAM USER + COVER -->
                    <td style="vertical-align:top; min-width:260px;">
                        @if($u->activeBorrowings && $u->activeBorrowings->count() > 0)
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                @foreach($u->activeBorrowings as $ab)
                                    <div style="display:flex; align-items:center; gap:10px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:6px 10px;">
                                        <div style="width:34px; height:46px; border-radius:4px; overflow:hidden; background:#eee; flex-shrink:0; border:1px solid var(--border);">
                                            @if($ab->book && $ab->book->cover)
                                                <img src="{{ asset('uploads/covers/' . $ab->book->cover) }}" alt="{{ $ab->book->title }}" style="width:100%; height:100%; object-fit:cover;">
                                            @else
                                                <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:1rem;">📖</div>
                                            @endif
                                        </div>
                                        <div style="flex:1; min-width:0;">
                                            <div style="font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                {{ $ab->book->title ?? 'Buku Dihapus' }}
                                            </div>
                                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                                                Tenggat: <strong style="color: {{ $ab->status === 'Overdue' ? 'var(--danger)' : 'var(--primary)' }};">{{ $ab->due_date ? date('d M Y', strtotime($ab->due_date)) : '-' }}</strong>
                                            </div>
                                            <div style="margin-top:2px;">
                                                <span class="status-badge {{ $ab->status === 'Overdue' ? 'badge-overdue' : 'badge-borrowed' }}" style="font-size:0.65rem; padding:1px 6px;">
                                                    {{ $ab->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span style="font-size:0.8rem; color:#94a3b8; font-style:italic;">
                                Sedang tidak meminjam buku
                            </span>
                        @endif
                    </td>

                    <td style="vertical-align:top;">
                        @if($u->is_active)
                            <span class="status-badge" style="background:#dcfce7; color:#166534; font-weight:700;">Aktif</span>
                        @else
                            <span class="status-badge" style="background:#fee2e2; color:#991b1b; font-weight:700;">Nonaktif</span>
                        @endif
                    </td>

                    <td style="vertical-align:top; text-align:right;">
                        <form action="/admin/users/toggle" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $u->id }}">
                            <input type="hidden" name="is_active" value="{{ $u->is_active ? 0 : 1 }}">
                            <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Ubah status aktif akun ini?')">
                                {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Belum ada user yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination 10 per halaman -->
    <div style="margin-top: 16px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
