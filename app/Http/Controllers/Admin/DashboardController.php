<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMenu = Menu::count();
        $totalUser = User::count();

        $pesananHariIni = Order::whereDate('created_at', Carbon::today())->count();
        $pendapatanHariIni = Order::whereDate('created_at', Carbon::today())
            ->where('status_pesanan', 'selesai')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_harga');

        $totalTransaksi = Order::where('status_pesanan', 'selesai')
            ->where('status_pembayaran', 'lunas')
            ->count();

        $pesananSelesai = Order::where('status_pesanan', 'selesai')->count();
        $pesananBelumBayar = Order::where('status_pembayaran', 'belum_bayar')->count();

        $menuTerlaris = OrderItem::query()
            ->whereHas('order', function ($query) {
                $query->where('status_pesanan', 'selesai')
                    ->where('status_pembayaran', 'lunas');
            })
            ->selectRaw('nama_menu, SUM(jumlah) as total_terjual')
            ->groupBy('nama_menu')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMenu',
            'totalUser',
            'pesananHariIni',
            'pendapatanHariIni',
            'totalTransaksi',
            'pesananSelesai',
            'pesananBelumBayar',
            'menuTerlaris'
        ));
    }
}
