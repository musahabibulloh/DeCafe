<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="d-flex flex-column justify-content-between w-100" style="min-height: calc(100vh - 3rem);">
        <div>
            <div class="d-flex align-items-center justify-content-between mb-3 px-3">
                <a class="navbar-brand mb-0" href="#" style="margin-bottom: 0 !important; flex-grow: 1; text-align: left;">Nasi Bakar Cak Win</a>
                <button class="btn-close d-md-none" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar" aria-label="Close"></button>
            </div>
            
            @auth
                <div class="px-3">
                    <span class="role-badge">Role: {{ auth()->user()->role ?? '-' }}</span>
                </div>
                
                <ul class="nav flex-column mt-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}" href="{{ route(auth()->user()->role . '.dashboard') }}">
                            <i class="bi bi-grid-1x2-fill"></i> Dashboard
                        </a>
                    </li>
                    
                    @if (auth()->user()->role === 'admin')
                        <!-- Admin Management -->
                        <div class="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Admin</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}" href="{{ route('admin.menus.index') }}">
                                <i class="bi bi-cup-hot-fill"></i> Kelola Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.lauks.*') ? 'active' : '' }}" href="{{ route('admin.lauks.index') }}">
                                <i class="bi bi-gear-fill"></i> Kelola Lauk & Ekstra
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people-fill"></i> Kelola User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                <i class="bi bi-graph-up-arrow"></i> Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.meja.*') ? 'active' : '' }}" href="{{ route('admin.meja.index') }}">
                                <i class="bi bi-qr-code-scan"></i> Kelola Meja & QR
                            </a>
                        </li>

                        <!-- Kasir Section -->
                        <div class="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Kasir</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}" href="{{ route('kasir.orders.index') }}">
                                <i class="bi bi-cash-coin"></i> Pembayaran (Kasir)
                            </a>
                        </li>

                        <!-- Pelayan Section -->
                        <div class="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pelayan</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pelayan.*') ? 'active' : '' }}" href="{{ route('pelayan.orders.index') }}">
                                <i class="bi bi-card-checklist"></i> Pesanan (Pelayan)
                            </a>
                        </li>

                        <!-- Dapur Section -->
                        <div class="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Dapur</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dapur.*') ? 'active' : '' }}" href="{{ route('dapur.orders.index') }}">
                                <i class="bi bi-egg-fried"></i> Antrean Dapur
                            </a>
                        </li>

                    @else
                        @if (auth()->user()->role === 'kasir')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}" href="{{ route('kasir.orders.index') }}">
                                    <i class="bi bi-cash-coin"></i> Pembayaran
                                </a>
                            </li>
                        @endif
                        
                        @if (auth()->user()->role === 'pelayan')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pelayan.*') ? 'active' : '' }}" href="{{ route('pelayan.orders.index') }}">
                                    <i class="bi bi-card-checklist"></i> Pesanan
                                </a>
                            </li>
                        @endif
                        
                        @if (auth()->user()->role === 'dapur')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dapur.*') ? 'active' : '' }}" href="{{ route('dapur.orders.index') }}">
                                    <i class="bi bi-egg-fried"></i> Pesanan Masuk
                                </a>
                            </li>
                        @endif
                        
                        @if (auth()->user()->role === 'customer')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('customer.menus') ? 'active' : '' }}" href="{{ route('customer.menus') }}">
                                    <i class="bi bi-menu-button-wide"></i> Menu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('customer.orders.index') || request()->routeIs('customer.orders.show') ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">
                                    <i class="bi bi-receipt"></i> Pesanan Saya
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            @endauth
        </div>

        @auth
            <div class="px-2 mt-auto pt-4">
                <form action="{{ route('logout') }}" method="POST" class="w-100">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2" type="submit">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>