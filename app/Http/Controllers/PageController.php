<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Borrowing;

class PageController extends Controller
{
    /**
     * Halaman Landing Page (Beranda)
     */
    public function index()
    {
        $latestBooks = Book::where('is_active', 1)->orderBy('id', 'desc')->take(8)->get();
        
        $totalBooks = Book::where('is_active', 1)->count();
        $totalUsers = User::where('role', 'user')->count();
        $totalBorrow = Borrowing::where('status', 'Returned')->count();
        
        $borrowableBooks = Book::where('is_active', 1)->where('stock_borrow', '>', 0)->inRandomOrder()->take(4)->get();

        return view('public.index', compact('latestBooks', 'totalBooks', 'totalUsers', 'totalBorrow', 'borrowableBooks'));
    }

    /**
     * Halaman Katalog Buku Public
     */
    public function catalog(Request $request)
    {
        $query = Book::with('category')->where('is_active', 1);

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%")
                  ->orWhere('isbn', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $books = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('public.catalog', compact('books', 'categories'));
    }

    /**
     * Halaman Katalog Buku Khusus User Area (5x3 = 15 buku per halaman)
     */
    public function userCatalog(Request $request)
    {
        $query = Book::with('category')->where('is_active', 1);

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%")
                  ->orWhere('isbn', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 15 buku per halaman (5 memanjang x 3 kebawah)
        $books = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('user.catalog', compact('books', 'categories'));
    }

    /**
     * Halaman Detail Buku
     */
    public function bookDetail($id)
    {
        $book = Book::with('category')->where('is_active', 1)->findOrFail($id);

        $relatedBooks = [];
        if ($book->category_id) {
            $relatedBooks = Book::where('category_id', $book->category_id)
                                ->where('id', '!=', $id)
                                ->where('is_active', 1)
                                ->inRandomOrder()
                                ->take(4)
                                ->get();
        }

        return view('public.book', compact('book', 'relatedBooks'));
    }

    /**
     * Halaman Tentang Kami
     */
    public function about()
    {
        return view('public.about');
    }
}
