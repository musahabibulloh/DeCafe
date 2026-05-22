<?php

namespace App\Http\Controllers\Pelayan;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->latest()
            ->paginate(10);

        return view('pelayan.orders.index', compact('orders'));
    }

    public function create()
    {
        $menus = Menu::where('status', 'tersedia')
            ->where('stok', '>', 0)
            ->orderBy('nama_menu')
            ->get();

        return view('pelayan.orders.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_meja' => 'required|string|max:50',
            'nama_pelanggan' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                $itemsData = $this->filterItems($validated['items']);
                if ($itemsData->isEmpty()) {
                    throw new \RuntimeException('Minimal satu item harus diisi.');
                }

                $order = Order::create([
                    'kode_pesanan' => $this->generateOrderCode(),
                    'user_id' => $request->user()->id,
                    'nama_pelanggan' => $validated['nama_pelanggan'] ?? null,
                    'nomor_meja' => $validated['nomor_meja'],
                    'total_harga' => 0,
                    'status_pesanan' => 'menunggu',
                    'status_pembayaran' => 'belum_bayar',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                $total = 0;
                foreach ($itemsData as $item) {
                    $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);

                    if ($menu->status !== 'tersedia' || $menu->stok < $item['jumlah']) {
                        throw new \RuntimeException('Stok menu tidak mencukupi untuk ' . $menu->nama_menu . '.');
                    }

                    $subtotal = $menu->harga * $item['jumlah'];
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'nama_menu' => $menu->nama_menu,
                        'harga' => $menu->harga,
                        'jumlah' => $item['jumlah'],
                        'subtotal' => $subtotal,
                        'catatan_item' => $item['catatan_item'] ?? null,
                    ]);

                    $menu->decrement('stok', $item['jumlah']);
                }

                $order->update(['total_harga' => $total]);
            });

            return redirect()->route('pelayan.orders.index')
                ->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load('items');

        return view('pelayan.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status_pesanan !== 'menunggu') {
            return redirect()->route('pelayan.orders.index')
                ->with('error', 'Pesanan hanya bisa diubah saat status menunggu.');
        }

        $menus = Menu::orderBy('nama_menu')->get();
        $itemsMap = $order->items->keyBy('menu_id');

        return view('pelayan.orders.edit', compact('order', 'menus', 'itemsMap'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status_pesanan !== 'menunggu') {
            return redirect()->route('pelayan.orders.index')
                ->with('error', 'Pesanan hanya bisa diubah saat status menunggu.');
        }

        $validated = $request->validate([
            'nomor_meja' => 'required|string|max:50',
            'nama_pelanggan' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated, $order) {
                foreach ($order->items as $item) {
                    Menu::where('id', $item->menu_id)->increment('stok', $item->jumlah);
                }

                $order->items()->delete();

                $itemsData = $this->filterItems($validated['items']);
                if ($itemsData->isEmpty()) {
                    throw new \RuntimeException('Minimal satu item harus diisi.');
                }

                $total = 0;
                foreach ($itemsData as $item) {
                    $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);

                    if ($menu->status !== 'tersedia' || $menu->stok < $item['jumlah']) {
                        throw new \RuntimeException('Stok menu tidak mencukupi untuk ' . $menu->nama_menu . '.');
                    }

                    $subtotal = $menu->harga * $item['jumlah'];
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'nama_menu' => $menu->nama_menu,
                        'harga' => $menu->harga,
                        'jumlah' => $item['jumlah'],
                        'subtotal' => $subtotal,
                        'catatan_item' => $item['catatan_item'] ?? null,
                    ]);

                    $menu->decrement('stok', $item['jumlah']);
                }

                $order->update([
                    'nama_pelanggan' => $validated['nama_pelanggan'] ?? null,
                    'nomor_meja' => $validated['nomor_meja'],
                    'catatan' => $validated['catatan'] ?? null,
                    'total_harga' => $total,
                ]);
            });

            return redirect()->route('pelayan.orders.show', $order)
                ->with('success', 'Pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function cancel(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status_pesanan !== 'menunggu') {
            return redirect()->route('pelayan.orders.index')
                ->with('error', 'Pesanan hanya bisa dibatalkan saat status menunggu.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                Menu::where('id', $item->menu_id)->increment('stok', $item->jumlah);
            }

            $order->update([
                'status_pesanan' => 'dibatalkan',
                'status_pembayaran' => 'dibatalkan',
            ]);
        });

        return redirect()->route('pelayan.orders.index')
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    private function authorizeOrder(Order $order): void
    {
        // Waiters have access to all orders in the system
    }

    private function generateOrderCode(): string
    {
        $today = Carbon::now()->format('Ymd');
        $countToday = Order::whereDate('created_at', Carbon::today())->count() + 1;

        return 'ORD-' . $today . '-' . str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
    }

    private function filterItems(array $items)
    {
        return collect($items)
            ->filter(fn ($item) => !empty($item['jumlah']))
            ->values();
    }
}
