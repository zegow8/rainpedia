@extends('layouts.user')

@section('title', 'Profil')

@section('content')
<h1 class="mb-4">Pengaturan Akun</h1>

<div style="max-width:720px;">
    <div class="panel" style="margin-bottom:20px;">
        <h2 class="panel-title mb-4">👤 Informasi Profil</h2>
        <form action="/user/profile" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email (Tidak dapat diubah)</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>
            <div class="form-group">
                <label class="form-label">No HP</label>
                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control">{{ $user->address }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <div class="panel">
        <h2 class="panel-title mb-4">🔒 Ganti Password</h2>
        <form action="/user/profile/password" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary">Ubah Password</button>
        </form>
    </div>
</div>
@endsection