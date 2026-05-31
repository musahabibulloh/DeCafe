<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua QR Code - Nasi Bakar Cak Win</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #b27318;
        }

        .header p {
            color: #888;
            font-size: 0.9rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .qr-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            page-break-inside: avoid;
        }

        .brand-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #b27318;
            margin-bottom: 4px;
        }

        .brand-tagline {
            font-size: 0.7rem;
            color: #aaa;
            margin-bottom: 16px;
        }

        .qr-container {
            background: #fff;
            padding: 12px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 12px;
            border: 2px solid #f0f0f0;
        }

        .table-label {
            font-size: 0.8rem;
            color: #888;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .table-number {
            font-size: 2rem;
            font-weight: 800;
            color: #222;
            margin-bottom: 8px;
        }

        .instruction {
            font-size: 0.72rem;
            color: #aaa;
            border-top: 1px dashed #eee;
            padding-top: 12px;
        }

        .print-btn-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .print-btn {
            padding: 14px 40px;
            background: #b27318;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .qr-card {
                box-shadow: none;
                border: 1.5px solid #333;
            }

            .grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="header no-print">
        <h1>🖨️ Cetak Semua QR Code</h1>
        <p>{{ $mejas->count() }} meja terdaftar</p>
    </div>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Semua</button>
    </div>

    <div class="grid">
        @foreach($mejas as $meja)
            <div class="qr-card">
                <div class="brand-name">Nasi Bakar Cak Win</div>
                <div class="brand-tagline">Scan untuk pesan langsung</div>
                <div class="qr-container">
                    <div id="qrcode-{{ $meja->id }}"></div>
                </div>
                <div class="table-label">MEJA</div>
                <div class="table-number">{{ $meja->nomor_meja }}</div>
                <div class="instruction">
                    Arahkan kamera ke QR Code untuk mulai memesan
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($mejas as $meja)
                new QRCode(document.getElementById("qrcode-{{ $meja->id }}"), {
                    text: "{{ $meja->scan_url }}",
                    width: 180,
                    height: 180,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            @endforeach
        });
    </script>
</body>
</html>
