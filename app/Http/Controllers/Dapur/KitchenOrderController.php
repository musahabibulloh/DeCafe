<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class KitchenOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->whereIn('status_pesanan', ['menunggu', 'diterima_dapur', 'diproses'])
            ->latest()
            ->paginate(10);

        return view('dapur.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('dapur.orders.show', compact('order'));
    }

    public function accept(Order $order)
    {
        return $this->updateStatus($order, 'menunggu', 'diterima_dapur', 'Pesanan diterima dapur.');
    }

    public function process(Order $order)
    {
        return $this->updateStatus($order, 'diterima_dapur', 'diproses', 'Pesanan sedang diproses.');
    }

    public function ready(Order $order)
    {
        return $this->updateStatus($order, 'diproses', 'siap_saji', 'Pesanan siap disajikan.');
    }

    private function updateStatus(Order $order, string $expected, string $next, string $message)
    {
        if (in_array($order->status_pesanan, ['dibatalkan', 'selesai'], true)) {
            return redirect()->route('dapur.orders.index')
                ->with('error', 'Pesanan tidak dapat diproses.');
        }

        if ($order->status_pesanan !== $expected) {
            return redirect()->route('dapur.orders.show', $order)
                ->with('error', 'Status pesanan tidak sesuai urutan.');
        }

        DB::transaction(function () use ($order, $next) {
            $order->update(['status_pesanan' => $next]);
        });

        return redirect()->route('dapur.orders.show', $order)
            ->with('success', $message);
    }
}
