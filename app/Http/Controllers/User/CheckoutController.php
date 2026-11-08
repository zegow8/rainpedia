<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Ambil item ID yang dipilih dari checkbox keranjang atau dari tombol 'Beli Sekarang'
        $selectedIds = $request->input('items');

        // Jika tidak ada item yang dipilih, kembalikan ke keranjang untuk memilih buku
        if (empty($selectedIds)) {
            return redirect('/user/cart')->with('error', 'Silakan pilih buku di keranjang Anda yang ingin dicheckout.');
        }

        if (!is_array($selectedIds)) {
            $selectedIds = [$selectedIds];
        }

        $query = CartDetail::with('book')->whereHas('cart', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereIn('id', $selectedIds);

        $items = $query->get();

        if ($items->isEmpty()) {
            return redirect('/user/cart')->with('error', 'Silakan pilih minimal satu buku yang tersedia untuk dicheckout.');
        }

        // VALIDASI REAL-TIME: Pastikan tidak ada buku yang stoknya habis atau kurang
        foreach ($items as $it) {
            if (!$it->book || $it->book->stock_sale <= 0) {
                return redirect('/user/cart')->with('error', "Maaf, stok buku '{$it->book->title}' telah habis terjual. Silakan hapus atau pilih buku lain.");
            }
            if ($it->qty > $it->book->stock_sale) {
                return redirect('/user/cart')->with('error', "Jumlah buku '{$it->book->title}' melebihi stok yang tersisa ({$it->book->stock_sale} pcs). Silakan sesuaikan jumlah di keranjang.");
            }
        }

        $totalBarang = $items->sum(function($item) {
            return $item->book->price * $item->qty;
        });
        $ongkir = 10000;
        $grandTotal = $totalBarang + $ongkir;

        return view('user.checkout', compact('items', 'totalBarang', 'ongkir', 'grandTotal'));
    }

    public function process(Request $request)
    {
        // VALIDASI WAJIB SEMUA FIELD (HAPUS COD: Hanya Transfer Bank BCA dan Mandiri)
        $request->validate([
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|min:5',
            'payment_method' => 'required|string|in:Transfer BCA,Transfer Mandiri',
            'items' => 'required|array|min:1',
            'items.*' => 'integer'
        ], [
            'receiver_name.required' => 'Nama penerima wajib diisi.',
            'receiver_phone.required' => 'Nomor HP penerima wajib diisi.',
            'shipping_address.required' => 'Alamat pengiriman lengkap wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran yang dipilih tidak valid. Pilih Transfer BCA atau Transfer Mandiri.',
            'items.required' => 'Pilih minimal satu buku untuk dibeli.',
        ]);

        $userId = Auth::id();

        // Ambil item terpilih yang dimiliki user
        $cartItems = CartDetail::with('book')
            ->whereIn('id', $request->items)
            ->whereHas('cart', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->get();

        if ($cartItems->isEmpty()) {
            return redirect('/user/cart')->with('error', 'Tidak ada buku valid yang dipilih untuk checkout.');
        }

        DB::beginTransaction();
        try {
            $subtotalBarang = 0;

            // KUNCI STOK & CEK KETERSEDIAAN TERBARU (Mencegah Race Condition User A vs User B)
            foreach ($cartItems as $it) {
                $freshBook = Book::lockForUpdate()->find($it->book_id);

                if (!$freshBook || $freshBook->stock_sale <= 0) {
                    throw new \Exception("Maaf, buku '{$freshBook->title}' baru saja habis dibeli oleh pengguna lain. Silakan periksa keranjang Anda.");
                }

                if ($it->qty > $freshBook->stock_sale) {
                    throw new \Exception("Maaf, stok buku '{$freshBook->title}' tersisa {$freshBook->stock_sale} pcs, tidak mencukupi permintaan Anda.");
                }

                $subtotalBarang += $freshBook->price * $it->qty;
            }

            $grandTotal = $subtotalBarang + 10000;

            $invoice = 'RP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $tracking = 'RPN' . date('ymd') . strtoupper(bin2hex(random_bytes(4)));

            // Buat Order: STATUS AWAL = 'Waiting Confirmation' (Menunggu ACC Konfirmasi Admin)
            $order = Order::create([
                'user_id' => $userId,
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone,
                'invoice_code' => $invoice,
                'total' => $grandTotal,
                'shipping_address' => $request->shipping_address,
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'status' => 'Waiting Confirmation', // Menunggu Konfirmasi Admin
                'tracking_number' => $tracking
            ]);

            // Buat OrderDetail & Potong Stok Buku
            foreach ($cartItems as $it) {
                $freshBook = Book::lockForUpdate()->find($it->book_id);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'book_id' => $freshBook->id,
                    'title_snapshot' => $freshBook->title,
                    'price_snapshot' => $freshBook->price,
                    'qty' => $it->qty,
                    'subtotal' => $freshBook->price * $it->qty
                ]);

                // Kurangi stok penjualan
                $freshBook->decrement('stock_sale', $it->qty);
            }

            // Catat Pembayaran
            Payment::create([
                'user_id' => $userId,
                'type' => 'order',
                'order_id' => $order->id,
                'amount' => $grandTotal,
                'method' => $request->payment_method
            ]);

            // HANYA HAPUS ITEM YANG DICHECKOUT DARI KERANJANG
            CartDetail::whereIn('id', $cartItems->pluck('id'))->delete();

            // Kirim Notifikasi ke User
            \App\Models\Notification::create([
                'user_id' => $userId,
                'title' => 'Menunggu Konfirmasi Admin ⏳',
                'message' => "Pesanan #$invoice telah dibuat dan sedang menunggu konfirmasi pembayaran dari admin sebelum dikemas.",
                'link' => '/user/transactions/' . $order->id
            ]);

            DB::commit();
            return redirect('/user/transactions')->with('success', "Pesanan #$invoice berhasil dibuat! Pembayaran Anda sedang menunggu konfirmasi dari admin.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/user/cart')->with('error', $e->getMessage());
        }
    }
}
