<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->with(['activeBorrowings.book'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleActive(Request $request)
    {
        $user = User::where('role', 'user')->findOrFail($request->id);
        $user->update(['is_active' => $request->is_active]);
        
        $status = $request->is_active == 1 ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun user {$user->name} berhasil {$status}.");
    }
}
