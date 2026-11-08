@extends('layouts.admin')
@section('title', 'Edit Buku')
@section('content')
<h1 class="mb-4">Edit Buku</h1>

<div class="panel">
    <!-- WAJIB pakai enctype buat upload file -->
    <form action="/admin/books/update/{{ $book->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div>
                <div class="form-group">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="title" class="form-control" required value="{{ $book->title }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="author" class="form-control" required value="{{ $book->author }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="publisher" class="form-control" value="{{ $book->publisher }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="year" class="form-control" value="{{ $book->year }}">
                </div>
                <div class="form-group">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="{{ $book->isbn }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-control">
                        <option value="">- Pilih Kategori -</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $book->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control" required value="{{ $book->price }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Jual</label>
                    <input type="number" name="stock_sale" class="form-control" required value="{{ $book->stock_sale }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Pinjam</label>
                    <input type="number" name="stock_borrow" class="form-control" required value="{{ $book->stock_borrow }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Cover Baru (Biarkan kosong jika tidak diganti)</label>
                    <input type="file" name="cover" class="form-control" accept="image/*">
                    @if($book->cover)
                        <div style="margin-top:8px;">
                            <img src="{{ asset('uploads/covers/'.$book->cover) }}" style="height:100px; border-radius:4px; border:1px solid #ccc;">
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" style="min-height:140px;">{{ $book->description }}</textarea>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">Update Buku</button>
    </form>
</div>
@endsection