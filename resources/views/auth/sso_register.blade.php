<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar dengan Google</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        body {
            background-color: #f0f4f9;
            color: #1f1f1f;
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }

        .google-card {
            max-width: 450px;
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 28px;
            padding: 40px;
            margin: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.025);
        }

        .google-logo {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
        }

        .google-title {
            font-size: 24px;
            font-weight: 400;
            color: #1f1f1f;
            margin-bottom: 8px;
        }

        .google-subtitle {
            font-size: 16px;
            color: #444746;
            margin-bottom: 28px;
        }

        .form-control {
            border: 1px solid #747775;
            border-radius: 4px;
            padding: 12px 16px;
            font-size: 16px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: #0b57d0;
            box-shadow: 0 0 0 1px #0b57d0;
            outline: none;
            color: #1f1f1f;
        }

        .btn-google-primary {
            background-color: #0b57d0;
            color: #ffffff;
            font-weight: 500;
            border-radius: 100px;
            padding: 10px 24px;
            border: none;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .btn-google-primary:hover {
            background-color: #0842a0;
            color: #ffffff;
        }

        .btn-google-text {
            color: #0b57d0;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 100px;
            transition: background-color 0.2s ease;
        }

        .btn-google-text:hover {
            background-color: rgba(11, 87, 208, 0.08);
            color: #0842a0;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="card google-card">
        <div class="card-body p-0">
            <!-- Google Logo -->
            <div class="text-start">
                <svg class="google-logo" viewBox="0 0 24 24" width="48" height="48" xmlns="http://www.w3.org/2000/svg">
                    <g transform="matrix(1, 0, 0, 1, 0, 0)">
                        <path d="M21.35,11.1H12v2.7h5.38c-0.24,1.28 -0.96,2.37 -2.05,3.1v2.58h3.31c1.94,-1.78 3.06,-4.41 3.06,-7.48c0,-0.6 -0.05,-1.18 -0.15,-1.7H21.35z" fill="#4285F4" />
                        <path d="M12,20.6c2.43,0 4.47,-0.8 5.96,-2.2l-2.91,-2.26c-0.8,0.54 -1.84,0.87 -3.05,0.87c-2.35,0 -4.33,-1.58 -5.04,-3.72H3.54v2.66C5.03,18.94 8.27,20.6 12,20.6z" fill="#34A853" />
                        <path d="M6.96,13.29c-0.18,-0.54 -0.28,-1.11 -0.28,-1.7c0,-0.59 0.1,-1.16 0.28,-1.7V7.23H3.54C2.93,8.45 2.58,9.83 2.58,11.3c0,1.47 0.35,2.85 0.96,4.07l2.67,-2.08H6.96z" fill="#FBBC05" />
                        <path d="M12,5.39c1.32,0 2.51,0.45 3.44,1.34l2.58,-2.58C16.47,2.77 14.43,1.99 12,1.99c-3.73,0 -6.97,1.66 -8.46,4.58l3.42,2.66c0.71,-2.14 2.69,-3.72 5.04,-3.72z" fill="#EA4335" />
                    </g>
                </svg>
            </div>
            
            <h1 class="google-title">Langkah Terakhir</h1>
            <p class="google-subtitle">Masukkan nama lengkap Anda untuk menyelesaikan pendaftaran.</p>

            <form method="POST" action="{{ route('sso.google.register.process') }}">
                @csrf
                <div class="mb-3">
                    <label for="email_display" class="form-label text-muted small">Email Google Anda</label>
                    <input type="email" id="email_display" class="form-control w-100 bg-light text-muted" value="{{ $email }}" disabled style="cursor: not-allowed; border-color: #e0e0e0;">
                </div>

                <div class="mb-4">
                    <label for="name" class="form-label text-muted small">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control w-100" placeholder="Nama Lengkap" value="{{ old('name', session('sso_name')) }}" required autofocus>
                    @error('name')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5">
                    <a href="{{ route('login') }}" class="btn-google-text">Batalkan</a>
                    <button type="submit" class="btn-google-primary">Daftar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
