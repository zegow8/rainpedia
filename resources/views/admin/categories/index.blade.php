@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')
<h1 class="mb-4">Kategori Buku</h1>
<div style="display:grid; grid-template-columns:1fr 2fr; gap:20px;">
    <div class="panel">
        <h3>Tambah Kategori</h3>
        <form action="/admin/categories" method="POST">
            @csrf
            <div class="form-group"><input type="text" name="name" class="form-control" placeholder="Nama Kategori" required></div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
    <div class="panel" style="padding:0;">
        <table class="table">
            <thead><tr><th>Nama</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($categories as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>
                        <form action="/admin/categories/delete/{{ $c->id }}" method="POST">@csrf <button class="btn btn-danger btn-sm">Hapus</button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection