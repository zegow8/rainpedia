<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Order::with('details.book')->where('user_id', $userId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $counts = [
            'all' => Order::where('user_id', $userId)->count(),
            'Waiting Confirmation' => Order::where('user_id', $userId)->where('status', 'Waiting Confirmation')->count(),
            'Processing' => Order::where('user_id', $userId)->where('status', 'Processing')->count(),
            'Shipped' => Order::where('user_id', $userId)->where('status', 'Shipped')->count(),
            'Completed' => Order::where('user_id', $userId)->where('status', 'Completed')->count(),
            'Cancelled' => Order::where('user_id', $userId)->where('status', 'Cancelled')->count(),
            'Rejected' => Order::where('user_id', $userId)->where('status', 'Rejected')->count(),
        ];

        return view('user.transactions', compact('orders', 'counts'));
    }

    public function show($id)
    {
        $order = Order::with(['details.book'])->where('user_id', Auth::id())->findOrFail($id);
        return view('user.transaction_detail', compact('order'));
    }

    public function cancel(Request $request)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($request->order_id);
        
        // Hanya bisa dibatalkan jika masih Waiting Confirmation atau Processing
        if (!in_array($order->status, ['Waiting Confirmation', 'Processing'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah dalam proses pengiriman atau selesai.');
        }

        DB::beginTransaction();
        try {
            $order->update(['status' => 'Cancelled', 'cancel_reason' => 'Dibatalkan oleh pembeli']);

            foreach ($order->details as $detail) {
                Book::where('id', $detail->book_id)->increment('stock_sale', $detail->qty);
            }

            DB::commit();
            return redirect('/user/transactions')->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
