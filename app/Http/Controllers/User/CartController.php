<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCart()
    {
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }

    public function index()
    {
        $cart = $this->getCart();
        $allItems = CartDetail::with('book')->where('cart_id', $cart->id)->get();

        // Pisahkan item: yang ready stok ditaro di atas, yang stok habis ditaro paling bawah
        $inStockItems = $allItems->filter(function($it) {
            return $it->book && $it->book->stock_sale > 0;
        })->values();

        $outOfStockItems = $allItems->filter(function($it) {
            return !$it->book || $it->book->stock_sale <= 0;
        })->values();

        // Gabungkan: In-stock duluan, out-of-stock paling bawah
        $items = $inStockItems->concat($outOfStockItems);

        $total = $inStockItems->sum(function($item) {
            return $item->book->price * $item->qty;
        });

        return view('user.cart', compact('items', 'inStockItems', 'outOfStockItems', 'total'));
    }

    public function add(Request $request)
    {
        $book = Book::findOrFail($request->book_id);
        if ($book->stock_sale <= 0) {
            return back()->with('error', 'Maaf, stok penjualan buku ini sudah habis.');
        }

        $cart = $this->getCart();
        $detail = CartDetail::where('cart_id', $cart->id)->where('book_id', $book->id)->first();

        if ($detail) {
            // Jika Beli Sekarang dan buku sudah ada di keranjang, tidak perlu menambah qty lagi
            if (!$request->has('buy_now') && $request->get('buy_now') != '1') {
                if (($detail->qty + 1) > $book->stock_sale) {
                    return back()->with('error', "Jumlah di keranjang sudah mencapai batas stok yang tersedia ({$book->stock_sale} pcs).");
                }
                $detail->increment('qty');
            }
        } else {
            $detail = CartDetail::create([
                'cart_id' => $cart->id,
                'book_id' => $book->id,
                'qty' => 1
            ]);
        }

        // Beli Sekarang -> Langsung arahkan ke halaman checkout KHUSUS BUKU INI SAJA
        if ($request->has('buy_now') || $request->get('buy_now') == '1') {
            return redirect('/user/checkout?items[]=' . $detail->id)->with('success', "'{$book->title}' siap di-checkout. Silakan lengkapi detail pengiriman.");
        }

        // Jika ditambahkan langsung dari card katalog, kembali ke katalog dengan pesan sukses
        if ($request->has('from_catalog')) {
            return back()->with('success', "🛒 '{$book->title}' berhasil dimasukkan ke keranjang belanja!");
        }

        return redirect('/user/cart')->with('success', "'{$book->title}' berhasil ditambahkan ke keranjang.");
    }

    public function update(Request $request, $id)
    {
        $detail = CartDetail::whereHas('cart', function($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $qty = (int)$request->qty;
        if ($qty < 1) {
            $detail->delete();
            return redirect('/user/cart')->with('success', 'Buku dihapus dari keranjang.');
        }

        if ($qty > $detail->book->stock_sale) {
            return back()->with('error', "Jumlah melebihi stok yang tersedia (tersisa {$detail->book->stock_sale} pcs).");
        }

        $detail->update(['qty' => $qty]);
        return redirect('/user/cart')->with('success', 'Jumlah buku berhasil diperbarui.');
    }

    public function remove($id)
    {
        $detail = CartDetail::whereHas('cart', function($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);
        
        $detail->delete();
        return redirect('/user/cart')->with('success', 'Buku dihapus dari keranjang.');
    }
}
