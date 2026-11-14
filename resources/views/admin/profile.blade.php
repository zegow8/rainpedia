@extends('layouts.admin')
@section('title', 'Profil Admin')
@section('content')
<h1 class="mb-4">Profil Administrator</h1>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:24px; max-width:900px;">
    <!-- Form Biodata Admin -->
    <div class="panel">
        <h3 style="margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border);">Informasi Akun</h3>
        <form action="/admin/profile" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $admin->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email (Akun Login)</label>
                <input type="email" class="form-control" disabled value="{{ $admin->email }}" style="background:#f1f5f9; cursor:not-allowed;">
                <small class="text-muted" style="display:block; margin-top:4px;">Email login ditetapkan oleh sistem dan tidak dapat diubah.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $admin->phone) }}" placeholder="08xxxxxxxxxx">
            </div>
            <button type="submit" class="btn btn-primary" style="font-weight:700;">Simpan Perubahan Profil</button>
        </form>
    </div>

    <!-- Form Ganti Password -->
    <div class="panel">
        <h3 style="margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border);">Ganti Password</h3>
        <form action="/admin/profile/password" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control" required placeholder="Masukkan password lama">
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" required placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" class="form-control" required placeholder="Ulangi password baru">
            </div>
            <button type="submit" class="btn btn-primary" style="font-weight:700; background:#0f172a;">Perbarui Password</button>
        </form>
    </div>
</div>
@endsection
