<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $menus = Menu::with('options')
            ->where('status', 'tersedia')
            ->where('stok', '>', 0)
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get()
            ->groupBy('kategori');

        // Get table number from QR scan session
        $nomorMeja = session('nomor_meja');

        // Fetch all lauk options from database
        $lauks = \App\Models\Lauk::all();

        return view('customer.dashboard', compact('menus', 'nomorMeja', 'lauks'));
    }

    public function menus()
    {
        $menus = Menu::with('options')
            ->where('status', 'tersedia')
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get()
            ->groupBy('kategori');

        return view('customer.menus.index', compact('menus'));
    }

    public function createOrder()
    {
        $menus = Menu::with('options')
            ->where('status', 'tersedia')
            ->where('stok', '>', 0)
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get()
            ->groupBy('kategori');

        return view('customer.orders.create', compact('menus'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'nomor_meja' => 'nullable|string|max:50',
            'atas_nama' => 'required|string|max:100',
            'tipe_pesanan' => 'required|in:dine_in,take_away',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:0',
            'items.*.catatan_item' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $itemsData = collect($validated['items'])
                    ->filter(fn ($item) => !empty($item['jumlah']))
                    ->values();

                if ($itemsData->isEmpty()) {
                    throw new \RuntimeException('Minimal satu item harus dipilih.');
                }

                $order = Order::create([
                    'kode_pesanan' => $this->generateOrderCode(),
                    'user_id' => auth()->id(),
                    'nama_pelanggan' => auth()->user()->name,
                    'atas_nama' => $validated['atas_nama'],
                    'tipe_pesanan' => $validated['tipe_pesanan'],
                    'nomor_meja' => session('nomor_meja', $validated['nomor_meja'] ?? '-'),
                    'total_harga' => 0,
                    'status_pesanan' => 'menunggu',
                    'status_pembayaran' => 'belum_bayar',
                ]);

                $total = 0;
                foreach ($itemsData as $item) {
                    $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);

                    if ($menu->status !== 'tersedia' || $menu->stok < $item['jumlah']) {
                        throw new \RuntimeException('Stok menu tidak mencukupi untuk ' . $menu->nama_menu . '.');
                    }

                    $catatan = $item['catatan_item'] ?? '';
                    $catatanLines = $catatan ? explode(" \n ", $catatan) : [];

                    if (!empty($catatanLines)) {
                        foreach ($catatanLines as $line) {
                            $portionPrice = Order::calculatePortionPrice($menu, $line);
                            $total += $portionPrice;

                            OrderItem::create([
                                'order_id' => $order->id,
                                'menu_id' => $menu->id,
                                'nama_menu' => $menu->nama_menu,
                                'harga' => $portionPrice,
                                'jumlah' => 1,
                                'subtotal' => $portionPrice,
                                'catatan_item' => $line,
                            ]);
                        }
                    } else {
                        $subtotal = $menu->harga * $item['jumlah'];
                        $total += $subtotal;

                        OrderItem::create([
                            'order_id' => $order->id,
                            'menu_id' => $menu->id,
                            'nama_menu' => $menu->nama_menu,
                            'harga' => $menu->harga,
                            'jumlah' => $item['jumlah'],
                            'subtotal' => $subtotal,
                            'catatan_item' => null,
                        ]);
                    }

                    $menu->decrement('stok', $item['jumlah']);
                }

                $order->update(['total_harga' => $total]);
            });

            return redirect()->route('customer.orders.index')
                ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function myOrders()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load('items');

        return view('customer.orders.show', compact('order'));
    }

    public function payOrder(Order $order, Request $request)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->status_pembayaran === 'lunas') {
            return back()->with('error', 'Pesanan sudah dibayar.');
        }

        if ($order->status_pembayaran === 'menunggu_konfirmasi') {
            return back()->with('error', 'Pesanan Anda sedang menunggu konfirmasi pembayaran.');
        }

        if ($order->status_pesanan === 'dibatalkan') {
            return back()->with('error', 'Pesanan dibatalkan.');
        }

        $metode = $request->input('metode', 'qris');

        if ($metode === 'qris') {
            return redirect()->route('customer.orders.qris', $order);
        }

        // For cash, we redirect back with instructions
        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Silakan kunjungi meja Kasir untuk membayar dengan uang tunai.');
    }

    public function showQrisPayment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->status_pembayaran === 'lunas') {
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Pesanan sudah lunas dibayar.');
        }

        if ($order->status_pembayaran === 'menunggu_konfirmasi') {
            return redirect()->route('customer.orders.show', $order)
                ->with('info', 'Pembayaran Anda sedang menunggu konfirmasi kasir.');
        }

        return view('customer.orders.qris', compact('order'));
    }

    public function uploadQrisPayment(Order $order, Request $request)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->status_pembayaran === 'lunas') {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Pesanan sudah lunas.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = 'bukti_' . $order->kode_pesanan . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Ensure directory exists
            $destinationPath = public_path('uploads/bukti_pembayaran');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);

            // Update order
            $order->update([
                'bukti_pembayaran' => 'uploads/bukti_pembayaran/' . $filename,
                'status_pembayaran' => 'menunggu_konfirmasi',
            ]);

            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Bukti pembayaran berhasil diunggah. Silakan tunggu konfirmasi dari kasir.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }

    public function checkOrderStatus(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status_pembayaran' => $order->status_pembayaran,
            'status_pesanan' => $order->status_pesanan,
        ]);
    }

    public function simulateQrisPay(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($order) {
            if ($order->status_pembayaran !== 'lunas') {
                Payment::create([
                    'order_id' => $order->id,
                    'kasir_id' => null,
                    'kode_pembayaran' => $this->generatePaymentCode(),
                    'metode_pembayaran' => 'qris',
                    'total_bayar' => $order->total_harga,
                    'uang_diterima' => $order->total_harga,
                    'kembalian' => 0,
                    'status' => 'lunas',
                    'paid_at' => Carbon::now(),
                ]);
                $order->update([
                    'status_pembayaran' => 'lunas',
                    'status_pesanan' => 'selesai',
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    private function generateOrderCode(): string
    {
        $today = Carbon::now()->format('Ymd');
        $countToday = Order::whereDate('created_at', Carbon::today())->count() + 1;

        return 'ORD-' . $today . '-' . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
    }

    private function generatePaymentCode(): string
    {
        $today = Carbon::now()->format('Ymd');
        $countToday = Payment::whereDate('created_at', Carbon::today())->count() + 1;

        return 'PAY-' . $today . '-' . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
    }
}
