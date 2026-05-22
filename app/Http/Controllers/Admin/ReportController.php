<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::query()
            ->where('status_pesanan', 'selesai')
            ->where('status_pembayaran', 'lunas');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->with('items', 'user')->latest()->get();

        $totalTransaksi = $orders->count();
        $totalPendapatan = $orders->sum('total_harga');

        $menuTerlaris = OrderItem::query()
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status_pesanan', 'selesai')
                    ->where('status_pembayaran', 'lunas');

                if ($startDate) {
                    $q->whereDate('created_at', '>=', $startDate);
                }

                if ($endDate) {
                    $q->whereDate('created_at', '<=', $endDate);
                }
            })
            ->selectRaw('nama_menu, SUM(jumlah) as total_terjual')
            ->groupBy('nama_menu')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        $statusCounts = Order::query()
            ->selectRaw('status_pesanan, COUNT(*) as total')
            ->groupBy('status_pesanan')
            ->pluck('total', 'status_pesanan');

        return view('admin.reports.index', [
            'orders' => $orders,
            'totalTransaksi' => $totalTransaksi,
            'totalPendapatan' => $totalPendapatan,
            'menuTerlaris' => $menuTerlaris,
            'statusCounts' => $statusCounts,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
