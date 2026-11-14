@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')
<style>
    @media (max-width: 768px) {
        .admin-category-grid { grid-template-columns: 1fr !important; }
    }
</style>
<h1 class="mb-4">Kategori Buku</h1>
<div style="display:grid; grid-template-columns:1fr 2fr; gap:20px;" class="admin-category-grid">
    <div class="panel">
        <h3>Tambah Kategori</h3>
        <form action="/admin/categories" method="POST">
            @csrf
            <div class="form-group"><input type="text" name="name" class="form-control" placeholder="Nama Kategori" required></div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
    <div class="panel" style="padding:0; overflow:hidden;">
        <table class="table" style="margin:0;">
            <thead><tr><th>Nama</th><th style="text-align:right;">Aksi</th></tr></thead>
            <tbody>
                @foreach($categories as $c)
                <tr>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td style="text-align:right;">
                        <form action="/admin/categories/delete/{{ $c->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" style="display:inline;">
                            @csrf 
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
