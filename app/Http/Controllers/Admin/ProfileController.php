<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = User::findOrFail(auth()->id());
        return view('admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20'
        ]);
        
        $admin = User::findOrFail(auth()->id());
        $admin->update([
            'name' => $request->name,
            'phone' => $request->phone
        ]);
        
        return back()->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        $admin = User::findOrFail(auth()->id());

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', 'Password saat ini yang Anda masukkan salah.');
        }

        $admin->update(['password' => Hash::make($request->new_password)]);
        return back()->with('success', 'Password admin berhasil diperbarui.');
    }
}
