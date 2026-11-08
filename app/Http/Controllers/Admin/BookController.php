<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['category', 'activeBorrowings.user']);

        // Search by Nama / Judul / Penulis
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%")
                  ->orWhere('isbn', 'like', "%{$keyword}%");
            });
        }

        // Filter by Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Harga Min & Max
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by Stok
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'sale_available') {
                $query->where('stock_sale', '>', 0);
            } elseif ($request->stock_status === 'sale_empty') {
                $query->where('stock_sale', '<=', 0);
            } elseif ($request->stock_status === 'borrow_available') {
                $query->where('stock_borrow', '>', 0);
            } elseif ($request->stock_status === 'borrow_empty') {
                $query->where('stock_borrow', '<=', 0);
            }
        }

        $books = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255', 
            'author' => 'required|string|max:255', 
            'price' => 'required|numeric|min:0',
            'stock_sale' => 'required|integer|min:0', 
            'stock_borrow' => 'required|integer|min:0',
            'cover' => 'nullable|file|mimes:jpeg,png,jpg,webp,jfif,avif,gif|max:10240'
        ], [
            'cover.mimes' => 'Format file cover harus berupa gambar (JPG, PNG, WEBP, JFIF, AVIF).',
            'cover.max' => 'Ukuran file cover maksimal adalah 10 MB.',
        ]);

        // CEK DUPLIKASI JUDUL BUKU (Case-Insensitive & Trimmed)
        $cleanTitle = trim($request->title);
        $duplicate = Book::whereRaw('LOWER(TRIM(title)) = ?', [strtolower($cleanTitle)])->first();
        if ($duplicate) {
            return back()->withInput()->with('error', "Peringatan: Buku dengan judul '{$cleanTitle}' sudah terdaftar di sistem (ID Buku: #{$duplicate->id}). Anda tidak dapat menambahkan buku dengan judul yang sama.");
        }

        $data = $request->except(['cover']);
        
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $uploadDir = public_path('uploads/covers');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Bersihkan nama file dari karakter berbahaya (#, spasi, simbol, dll)
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $rawName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $rawName);
            $filename = time() . '_' . substr($cleanName, 0, 50) . '.' . strtolower($ext);

            $file->move($uploadDir, $filename);
            $data['cover'] = $filename;
        }

        Book::create($data);
        return redirect('/admin/books')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255', 
            'author' => 'required|string|max:255', 
            'price' => 'required|numeric|min:0',
            'stock_sale' => 'required|integer|min:0', 
            'stock_borrow' => 'required|integer|min:0',
            'cover' => 'nullable|file|mimes:jpeg,png,jpg,webp,jfif,avif,gif|max:10240'
        ], [
            'cover.mimes' => 'Format file cover harus berupa gambar (JPG, PNG, WEBP, JFIF, AVIF).',
            'cover.max' => 'Ukuran file cover maksimal adalah 10 MB.',
        ]);

        // CEK DUPLIKASI JUDUL BUKU KETIKA UPDATE (Kecuali buku ini sendiri)
        $cleanTitle = trim($request->title);
        $duplicate = Book::whereRaw('LOWER(TRIM(title)) = ?', [strtolower($cleanTitle)])
            ->where('id', '!=', $id)
            ->first();
        if ($duplicate) {
            return back()->withInput()->with('error', "Peringatan: Buku dengan judul '{$cleanTitle}' sudah ada pada buku lain (ID Buku: #{$duplicate->id}). Judul buku tidak boleh sama.");
        }

        $data = $request->except(['cover']);
        
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $uploadDir = public_path('uploads/covers');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Bersihkan nama file dari karakter liar seperti #, spasi, simbol
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $rawName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $rawName);
            $filename = time() . '_' . substr($cleanName, 0, 50) . '.' . strtolower($ext);

            // Hapus cover lama jika ada file lama
            if ($book->cover && file_exists($uploadDir . '/' . $book->cover)) {
                @unlink($uploadDir . '/' . $book->cover);
            }

            $file->move($uploadDir, $filename);
            $data['cover'] = $filename;
        }

        $book->update($data);
        return redirect('/admin/books')->with('success', 'Buku berhasil diupdate.');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        if ($book->cover && file_exists(public_path('uploads/covers/' . $book->cover))) {
            @unlink(public_path('uploads/covers/' . $book->cover));
        }
        $book->delete();
        return redirect('/admin/books')->with('success', 'Buku berhasil dihapus.');
    }
}
