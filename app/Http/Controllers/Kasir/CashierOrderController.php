<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;

class CashierOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('kasir.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'payment');

        return view('kasir.orders.show', compact('order'));
    }

    public function complete(Order $order)
    {
        if ($order->status_pesanan !== 'siap_saji') {
            return back()->with('error', 'Hanya pesanan siap saji yang bisa diselesaikan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $order->update([
                'status_pesanan' => 'selesai',
            ]);
        });

        return redirect()->route('kasir.orders.index')
            ->with('success', 'Pesanan berhasil diselesaikan.');
    }
}
