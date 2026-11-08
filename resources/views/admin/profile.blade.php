@extends('layouts.admin')
@section('title', 'Profil Admin')
@section('content')
<h1 class="mb-4">Profil Admin</h1>
<div style="max-width:600px;">
    <div class="panel">
        <form action="/admin/profile" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" required value="{{ $admin->name }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email (Tidak dapat diubah)</label>
                <input type="email" class="form-control" disabled value="{{ $admin->email }}">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection