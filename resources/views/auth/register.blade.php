<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi - DeCafe</title>
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

        .register-card {
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

        .btn-success {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: #fff !important;
            padding: 0.7rem 1.5rem !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(224, 158, 57, 0.3) !important;
            transition: all 0.25s ease !important;
        }

        .btn-success:hover {
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
    <div class="card register-card shadow">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <span class="brand-title">DeCafe</span>
                <p class="text-muted mt-1 mb-0">Daftar akun baru DeCafe</p>
            </div>
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('register.process') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group mb-1">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border: 1px solid var(--border-color); border-left: none; background-color: rgba(255, 255, 255, 0.04); color: var(--text-muted); border-radius: 0 10px 10px 0 !important; padding: 0.7rem 1.1rem; transition: all 0.2s ease;">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation" style="border: 1px solid var(--border-color); border-left: none; background-color: rgba(255, 255, 255, 0.04); color: var(--text-muted); border-radius: 0 10px 10px 0 !important; padding: 0.7rem 1.1rem; transition: all 0.2s ease;">
                            <i class="bi bi-eye-slash" id="toggleConfirmationIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Daftar</button>
            </form>
            <p class="text-muted small mt-3 text-center">
                Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
            </p>
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

        const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
        const passwordConfirmation = document.querySelector('#password_confirmation');
        togglePasswordConfirmation.addEventListener('click', function (e) {
            const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmation.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>