<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:categories']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
        Category::create(['name' => $request->name, 'slug' => $slug]);
        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        $bookCount = \App\Models\Book::where('category_id', $id)->count();
        if ($bookCount > 0) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$bookCount} buku.");
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}