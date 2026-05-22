<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create(Order $order)
    {
        $order->load('items', 'payment');

        if ($order->status_pesanan === 'dibatalkan') {
            return redirect()->route('kasir.orders.show', $order)
                ->with('error', 'Pesanan dibatalkan tidak bisa dibayar.');
        }

        if ($order->status_pembayaran === 'lunas') {
            return redirect()->route('kasir.orders.show', $order)
                ->with('error', 'Pesanan sudah lunas.');
        }

        return view('kasir.payments.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        if ($order->status_pesanan === 'dibatalkan') {
            return redirect()->route('kasir.orders.show', $order)
                ->with('error', 'Pesanan dibatalkan tidak bisa dibayar.');
        }

        if ($order->status_pembayaran === 'lunas') {
            return redirect()->route('kasir.orders.show', $order)
                ->with('error', 'Pesanan sudah lunas.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|in:tunai,qris,transfer,ewallet',
            'uang_diterima' => 'nullable|integer|min:0',
            'nomor_meja' => 'required|string|max:50',
        ]);

        if ($validated['metode_pembayaran'] === 'tunai' && empty($validated['uang_diterima'])) {
            return back()->with('error', 'Uang diterima wajib diisi untuk pembayaran tunai.');
        }

        $total = $order->total_harga;
        $uangDiterima = $validated['metode_pembayaran'] === 'tunai'
            ? (int) $validated['uang_diterima']
            : null;

        if ($validated['metode_pembayaran'] === 'tunai' && $uangDiterima < $total) {
            return back()->with('error', 'Uang diterima harus lebih besar atau sama dengan total harga.');
        }

        $kembalian = $validated['metode_pembayaran'] === 'tunai'
            ? $uangDiterima - $total
            : 0;

        DB::transaction(function () use ($order, $validated, $total, $uangDiterima, $kembalian) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'kasir_id' => auth()->id(),
                'kode_pembayaran' => $this->generatePaymentCode(),
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'total_bayar' => $total,
                'uang_diterima' => $uangDiterima,
                'kembalian' => $kembalian,
                'status' => 'lunas',
                'paid_at' => Carbon::now(),
            ]);

            $order->update([
                'nomor_meja' => $validated['nomor_meja'],
                'status_pembayaran' => 'lunas',
                'status_pesanan' => 'selesai',
            ]);
        });

        return redirect()->route('kasir.orders.show', $order)
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function receipt(Payment $payment)
    {
        $payment->load('order.items', 'kasir');

        return view('kasir.payments.receipt', compact('payment'));
    }

    private function generatePaymentCode(): string
    {
        $today = Carbon::now()->format('Ymd');
        $countToday = Payment::whereDate('created_at', Carbon::today())->count() + 1;

        return 'PAY-' . $today . '-' . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
    }
}
