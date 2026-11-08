@extends('layouts.user')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div style="max-width:640px;margin:0 auto;">
    <h1 class="mb-4">Pinjam Buku</h1>

    <div class="panel" style="display:flex;gap:20px;align-items:flex-start;margin-bottom:20px;">
        <div style="width:80px;height:112px;background:#eee;border-radius:4px;overflow:hidden;flex-shrink:0;">
            @if($book->cover)
                <img src="{{ asset('uploads/covers/'.$book->cover) }}" style="width:100%;height:100%;object-fit:cover;">
            @endif
        </div>
        <div>
            <h2 style="font-size:1.15rem;margin-bottom:4px;">{{ $book->title }}</h2>
            <p style="color:var(--text-muted);font-size:0.9rem;">oleh {{ $book->author }}</p>
            <div style="margin-top:10px;font-size:0.85rem;">
                <span class="stock-pill stock-ok">Stok Pinjam: {{ $book->stock_borrow }}</span>
            </div>
        </div>
    </div>

    <div class="panel">
        @if($activeCount >= 2)
            <div class="alert alert-danger">Anda sudah mencapai batas maksimal 2 peminjaman aktif.</div>
        @elseif($hasFine)
            <div class="alert alert-danger">Anda memiliki denda yang belum dibayar.</div>
        @elseif($hasThisBook)
            <div class="alert alert-info">Anda sedang meminjam buku ini.</div>
        @elseif($book->stock_borrow <= 0)
            <div class="alert alert-danger">Stok pinjam buku ini habis.</div>
        @else
            <form action="/user/borrowings/process" method="POST">
                @csrf
                <input type="hidden" name="book_id" value="{{ $book->id }}">
                <div class="form-group">
                    <label class="form-label">Durasi Peminjaman (1-7 Hari)</label>
                    <select name="duration_days" class="form-control" required>
                        @for($i=1; $i<=7; $i++)
                            <option value="{{ $i }}">{{ $i }} Hari</option>
                        @endfor
                    </select>
                </div>
                <div style="background:var(--primary-light);padding:16px;border-radius:4px;margin-bottom:20px;font-size:0.9rem;">
                    <strong>Ketentuan:</strong> Gratis. Denda Rp10.000/hari jika terlambat.
                </div>
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:20px;">
                    <input type="checkbox" name="agree" id="agree" required>
                    <label for="agree" style="font-size:0.9rem;">Saya menyetujui ketentuan.</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Kirim Pengajuan</button>
            </form>
        @endif
    </div>
</div>
@endsection