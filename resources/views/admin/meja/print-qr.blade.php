<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - Meja {{ $meja->nomor_meja }} - Nasi Bakar Cak Win</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f0f0f0;
        }

        .qr-card {
            width: 320px;
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .brand-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: #b27318;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .brand-tagline {
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 24px;
        }

        .qr-container {
            background: #ffffff;
            padding: 16px;
            border-radius: 16px;
            display: inline-block;
            margin-bottom: 20px;
            border: 2px solid #f0f0f0;
        }

        .table-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #222;
            margin-bottom: 4px;
        }

        .table-label {
            font-size: 0.95rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
        }

        .instruction {
            font-size: 0.82rem;
            color: #999;
            line-height: 1.5;
            border-top: 1px dashed #ddd;
            padding-top: 16px;
        }

        .instruction strong {
            color: #b27318;
        }

        @media print {
            body {
                background: #fff;
            }

            .qr-card {
                box-shadow: none;
                border: 2px solid #333;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div>
        <div class="qr-card">
            <div class="brand-name">Nasi Bakar Cak Win</div>
            <div class="brand-tagline">Scan untuk pesan langsung</div>

            <div class="qr-container">
                <div id="qrcode"></div>
            </div>

            <div class="table-label">MEJA</div>
            <div class="table-number">{{ $meja->nomor_meja }}</div>

            <div class="instruction">
                Arahkan kamera HP Anda ke QR Code di atas untuk <strong>mulai memesan</strong>.
            </div>
        </div>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" style="padding: 12px 32px; background: #b27318; color: #fff; border: none; border-radius: 12px; font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 600; cursor: pointer;">
                🖨️ Cetak QR Code
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $meja->scan_url }}",
            width: 220,
            height: 220,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
