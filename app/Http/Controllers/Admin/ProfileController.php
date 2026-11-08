<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = User::findOrFail(auth()->id());
        return view('admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $request->validate(['name' => 'required']);
        
        $admin = User::findOrFail(auth()->id());
        $admin->update(['name' => $request->name]);
        
        return back()->with('success', 'Profil admin berhasil diperbarui.');
    }
}