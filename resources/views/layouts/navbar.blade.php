<nav class="navbar topbar sticky-top px-3 px-md-4">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-secondary btn-sm d-md-none me-1" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-5"></i>
        </button>
        <i class="bi bi-person-circle text-primary fs-5"></i>
        <h6 class="mb-0 fw-semibold fs-7 fs-sm-6"><span class="d-none d-sm-inline">Selamat datang, </span>{{ auth()->user()->name ?? 'Pengguna' }}</h6>
    </div>
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" onclick="toggleTheme()">
            <i class="bi bi-sun-fill" id="themeIcon"></i>
            <span id="themeText" class="d-none d-sm-inline">Light Mode</span>
        </button>
    </div>
</nav>
