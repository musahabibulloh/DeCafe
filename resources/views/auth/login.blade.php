<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - DeCafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        :root {
            --bg-dark: #08080a;
            --bg-card: rgba(19, 18, 26, 0.75);
            --primary-gradient: linear-gradient(135deg, #e09e39 0%, #b27318 100%);
            --primary-color: #e09e39;
            --text-main: #f5f0e6;
            --text-muted: #9c97a6;
            --border-color: rgba(255, 255, 255, 0.06);
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.85)), url('{{ asset('background.avif') }}') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        .login-card {
            max-width: 440px;
            width: 100%;
            background-color: var(--bg-card) !important;
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color) !important;
            border-radius: 20px !important;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.6) !important;
            margin: 20px;
        }

        .brand-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1.5px;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid var(--border-color) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 0.7rem 1.1rem !important;
            transition: all 0.25s ease !important;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(224, 158, 57, 0.25) !important;
            color: #fff !important;
        }

        .form-label {
            color: var(--text-main) !important;
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .btn-primary {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: #fff !important;
            padding: 0.7rem 1.5rem !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(224, 158, 57, 0.3) !important;
            transition: all 0.25s ease !important;
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(224, 158, 57, 0.5) !important;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        a:hover {
            color: #fff;
        }

        .alert {
            border-radius: 10px !important;
            border: none !important;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.15) !important;
            color: #e74c3c !important;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.15) !important;
            color: #2ecc71 !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .input-group .form-control {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .input-group .btn {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-left: none !important;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="card login-card shadow">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <span class="brand-title">DeCafe</span>
                <p class="text-muted mt-1 mb-0">Silakan login untuk masuk ke aplikasi</p>
            </div>
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('login.process') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border: 1px solid var(--border-color); border-left: none; background-color: rgba(255, 255, 255, 0.04); color: var(--text-muted); border-radius: 0 10px 10px 0 !important; padding: 0.7rem 1.1rem; transition: all 0.2s ease;">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>

                <div class="d-flex align-items-center my-3">
                    <hr class="flex-grow-1" style="color: var(--border-color); opacity: 0.2;">
                    <span class="mx-3 text-muted small" style="opacity: 0.7;">atau</span>
                    <hr class="flex-grow-1" style="color: var(--border-color); opacity: 0.2;">
                </div>

                <a href="{{ route('sso.google') }}" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2" style="border: 1px solid var(--border-color); border-radius: 10px; background-color: rgba(255, 255, 255, 0.02); color: var(--text-main); font-weight: 500; transition: background-color 0.2s ease;">
                    <svg class="google-logo" viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
                        <g transform="matrix(1, 0, 0, 1, 0, 0)">
                            <path d="M21.35,11.1H12v2.7h5.38c-0.24,1.28 -0.96,2.37 -2.05,3.1v2.58h3.31c1.94,-1.78 3.06,-4.41 3.06,-7.48c0,-0.6 -0.05,-1.18 -0.15,-1.7H21.35z" fill="#4285F4" />
                            <path d="M12,20.6c2.43,0 4.47,-0.8 5.96,-2.2l-2.91,-2.26c-0.8,0.54 -1.84,0.87 -3.05,0.87c-2.35,0 -4.33,-1.58 -5.04,-3.72H3.54v2.66C5.03,18.94 8.27,20.6 12,20.6z" fill="#34A853" />
                            <path d="M6.96,13.29c-0.18,-0.54 -0.28,-1.11 -0.28,-1.7c0,-0.59 0.1,-1.16 0.28,-1.7V7.23H3.54C2.93,8.45 2.58,9.83 2.58,11.3c0,1.47 0.35,2.85 0.96,4.07l2.67,-2.08H6.96z" fill="#FBBC05" />
                            <path d="M12,5.39c1.32,0 2.51,0.45 3.44,1.34l2.58,-2.58C16.47,2.77 14.43,1.99 12,1.99c-3.73,0 -6.97,1.66 -8.46,4.58l3.42,2.66c0.71,-2.14 2.69,-3.72 5.04,-3.72z" fill="#EA4335" />
                        </g>
                    </svg>
                    Masuk dengan Google
                </a>
            </form>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
