<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Book;
use App\Models\Notification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'details.book'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        // Hitung count untuk tab
        $counts = [
            'all' => Order::count(),
            'Waiting Confirmation' => Order::where('status', 'Waiting Confirmation')->count(),
            'Processing' => Order::where('status', 'Processing')->count(),
            'Shipped' => Order::where('status', 'Shipped')->count(),
            'Completed' => Order::where('status', 'Completed')->count(),
            'Cancelled' => Order::where('status', 'Cancelled')->count(),
            'Rejected' => Order::where('status', 'Rejected')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'details.book'])->findOrFail($id);
        return view('admin.orders.detail', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::with('details')->findOrFail($id);
        $newStatus = $request->status;

        // Validasi alur status
        if ($order->status === 'Waiting Confirmation' && !in_array($newStatus, ['Waiting Confirmation', 'Processing', 'Rejected'])) {
            return back()->with('error', 'Status tidak valid. Dari Menunggu Konfirmasi, status hanya bisa diubah ke Dikemas (ACC) atau Ditolak.');
        }
        if ($order->status === 'Processing' && !in_array($newStatus, ['Processing', 'Shipped', 'Rejected'])) {
            return back()->with('error', 'Status tidak valid.');
        }
        if ($order->status === 'Shipped' && !in_array($newStatus, ['Shipped', 'Completed'])) {
            return back()->with('error', 'Status tidak valid.');
        }
        if (in_array($order->status, ['Completed', 'Rejected', 'Cancelled'])) {
            return back()->with('error', 'Status pesanan sudah final dan tidak dapat diubah lagi.');
        }

        // Jika pesanan ditolak, kembalikan stok buku
        if ($newStatus === 'Rejected' && $order->status !== 'Rejected') {
            foreach ($order->details as $d) {
                Book::where('id', $d->book_id)->increment('stock_sale', $d->qty);
            }
        }

        $order->update([
            'status' => $newStatus, 
            'tracking_number' => $request->tracking_number
        ]);

        // Kirim Notifikasi ke User
        if ($newStatus === 'Processing') {
            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Pembayaran Dikonfirmasi ✅',
                'message' => "Pembayaran pesanan #{$order->invoice_code} telah dikonfirmasi oleh admin. Pesanan Anda kini sedang dikemas.",
                'link' => '/user/transactions/' . $order->id
            ]);
        } elseif ($newStatus === 'Shipped') {
            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Pesanan Dikirim 🚚',
                'message' => "Pesanan #{$order->invoice_code} sedang dikirim dengan no. resi: " . ($request->tracking_number ?? '-'),
                'link' => '/user/transactions/' . $order->id
            ]);
        } elseif ($newStatus === 'Rejected') {
            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Pesanan Ditolak ❌',
                'message' => "Mohon maaf, pembayaran atau pesanan #{$order->invoice_code} ditolak oleh admin.",
                'link' => '/user/transactions/' . $order->id
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
