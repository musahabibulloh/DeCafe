<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            background-color: #f7f2ea;
        }
        .receipt {
            max-width: 680px;
            margin: 40px auto;
            background: #fff;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="text-center mb-4">
            <h3 class="mb-1">DeCafe</h3>
            <p class="mb-0">Bukti Pembayaran</p>
        </div>

        <div class="mb-3">
            <p><strong>Kode Pembayaran:</strong> {{ $payment->kode_pembayaran }}</p>
            <p><strong>Kode Pesanan:</strong> {{ $payment->order->kode_pesanan }}</p>
            <p><strong>Nama Pelanggan:</strong> {{ $payment->order->nama_pelanggan ?? '-' }}</p>
            <p><strong>Nomor Meja:</strong> {{ $payment->order->nomor_meja }}</p>
            <p><strong>Nama Kasir:</strong> {{ $payment->kasir->name ?? '-' }}</p>
            <p><strong>Tanggal Bayar:</strong> {{ optional($payment->paid_at)->format('d M Y H:i') }}</p>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payment->order->items as $item)
                        <tr>
                            <td>
                                {{ $item->nama_menu }}
                                @if($item->catatan_item)
                                    <div class="small text-muted font-monospace mt-1" style="font-size: 0.8rem; white-space: pre-line;">
                                        * {!! e($item->catatan_item) !!}
                                    </div>
                                @endif
                            </td>
                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th>Rp {{ number_format($payment->total_bayar, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mb-3">
            <p><strong>Metode Pembayaran:</strong> {{ strtoupper($payment->metode_pembayaran) }}</p>
            <p><strong>Uang Diterima:</strong> {{ $payment->uang_diterima ? 'Rp ' . number_format($payment->uang_diterima, 0, ',', '.') : '-' }}</p>
            <p><strong>Kembalian:</strong> Rp {{ number_format($payment->kembalian, 0, ',', '.') }}</p>
        </div>

        <div class="d-flex gap-2 no-print">
            <button class="btn btn-primary" onclick="window.print()">Cetak Struk</button>
            <a href="{{ route('kasir.orders.show', $payment->order) }}" class="btn btn-outline-secondary">Kembali ke Kasir</a>
        </div>
    </div>
</body>
</html>
