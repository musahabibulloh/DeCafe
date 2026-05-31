<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Nasi Bakar Cak Win') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        // Apply theme early to prevent flashing
        const currentTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', currentTheme);
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        :root {
            /* Dark theme (default) */
            --bg-dark: #07070b;
            --bg-card: #181620;
            --bg-card-hover: #211f2c;
            --card-bg: var(--bg-card);
            --primary-gradient: linear-gradient(135deg, #e09e39 0%, #b27318 100%);
            --primary-color: #e09e39;
            --primary-color-hover: #c5862a;
            --text-main: #f5f0e6;
            --text-color: var(--text-main);
            --text-muted: #c0b8ca;
            --border-color: rgba(255, 255, 255, 0.16);
            --shadow-main: 0 10px 34px rgba(0, 0, 0, 0.5);
            --transition-speed: 0.25s;

            /* Override Bootstrap 5 variables */
            --bs-body-bg: #07070b;
            --bs-body-color: #f5f0e6;
            --bs-body-color-rgb: 245, 240, 230;
            --bs-secondary-color: #c0b8ca;
            --bs-secondary-color-rgb: 192, 184, 202;
            --bs-border-color: rgba(255, 255, 255, 0.16);
            --bs-heading-color: #ffffff;
            
            --sidebar-bg: #111016;
            --sidebar-brand: #ffffff;
            --input-bg: #1a1921;
            --input-focus-bg: #1f1e29;
            --table-header-bg: rgba(255, 255, 255, 0.08);
            --table-hover-bg: rgba(255, 255, 255, 0.07);
            --list-item-bg: rgba(255, 255, 255, 0.05);
            --list-item-hover-bg: rgba(255, 255, 255, 0.09);
            --disabled-page-bg: #0f0e13;
            --disabled-page-color: rgba(255, 255, 255, 0.2);
            --card-shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.6);

            /* Action Buttons Variables */
            --success-color: #2ecc71;
            --btn-success-bg: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            --btn-success-shadow: rgba(46, 204, 113, 0.2);
            
            --danger-color: #e55353;
            --btn-danger-bg: linear-gradient(135deg, #e55353 0%, #a82525 100%);
            --btn-danger-shadow: rgba(229, 83, 83, 0.2);
            
            --btn-primary-shadow: rgba(224, 158, 57, 0.2);
        }

        [data-theme="light"] {
            /* Light theme */
            --bg-dark: #f1ece2;
            --bg-card: #ffffff;
            --bg-card-hover: #f8f3e9;
            --card-bg: var(--bg-card);
            --text-main: #2b1b15;
            --text-color: var(--text-main);
            --text-muted: #68564c;
            --border-color: rgba(62, 39, 35, 0.18);
            --shadow-main: 0 10px 30px rgba(62, 39, 35, 0.09);

            --bs-body-bg: #f1ece2;
            --bs-body-color: #2b1b15;
            --bs-body-color-rgb: 43, 27, 21;
            --bs-secondary-color: #68564c;
            --bs-secondary-color-rgb: 104, 86, 76;
            --bs-border-color: rgba(62, 39, 35, 0.18);
            --bs-heading-color: #2b1b15;
            
            --sidebar-bg: #e7dccb;
            --sidebar-brand: #2b1b15;
            --input-bg: #ffffff;
            --input-focus-bg: #fffaf1;
            --table-header-bg: rgba(62, 39, 35, 0.08);
            --table-hover-bg: rgba(62, 39, 35, 0.06);
            --list-item-bg: rgba(62, 39, 35, 0.04);
            --list-item-hover-bg: rgba(62, 39, 35, 0.08);
            --disabled-page-bg: #e8e0d4;
            --disabled-page-color: rgba(62, 39, 35, 0.3);
            --card-shadow-hover: 0 12px 40px rgba(62, 39, 35, 0.1);

            /* Action Buttons Variables */
            --primary-gradient: linear-gradient(135deg, #b27318 0%, #834f0e 100%);
            --primary-color: #b27318;
            --primary-color-hover: #834f0e;
            
            --success-color: #27ae60;
            --btn-success-bg: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            --btn-success-shadow: rgba(39, 174, 96, 0.15);
            
            --danger-color: #c0392b;
            --btn-danger-bg: linear-gradient(135deg, #c0392b 0%, #962d22 100%);
            --btn-danger-shadow: rgba(192, 57, 43, 0.15);
            
            --btn-primary-shadow: rgba(131, 79, 14, 0.15);
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Typography and text color overrides for dark mode readability */
        /* Typography and text color overrides for readability */
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            color: var(--bs-heading-color) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-dark {
            color: var(--text-main) !important;
        }

        .text-black {
            color: var(--bs-heading-color) !important;
        }

        .form-text {
            color: var(--text-muted) !important;
        }

        /* Sidebar styling */
        .sidebar {
            background: var(--sidebar-bg) !important;
            border-right: 1px solid var(--border-color);
            box-shadow: var(--shadow-main);
            min-height: 100vh;
            padding: 1.5rem 1rem !important;
            transition: all var(--transition-speed) ease;
        }

        @media (min-width: 768px) {
            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
            }
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -260px;
                bottom: 0;
                width: 260px;
                z-index: 1050;
                min-height: 100vh !important;
                margin-left: 0;
                transition: transform var(--transition-speed) ease, visibility var(--transition-speed) ease;
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.3);
                display: block !important;
                visibility: hidden;
            }
            
            .sidebar.show {
                transform: translateX(260px);
                visibility: visible;
            }

            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 1040;
                display: none;
                opacity: 0;
                transition: opacity var(--transition-speed) ease;
            }
            
            .sidebar-backdrop.show {
                display: block;
                opacity: 1;
            }
            
            main {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }

        .fs-7 {
            font-size: 0.85rem !important;
        }

        .sidebar .navbar-brand {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--sidebar-brand);
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            display: block;
            text-align: center;
        }

        .sidebar .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all var(--transition-speed) ease;
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            transition: transform var(--transition-speed) ease;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(var(--bs-body-color-rgb), 0.04);
            color: var(--bs-heading-color);
        }

        .sidebar .nav-link:hover i {
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            background: var(--primary-gradient) !important;
            color: #fff !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(224, 158, 57, 0.3);
        }

        .sidebar .role-badge {
            background: rgba(var(--bs-body-color-rgb), 0.04);
            border: 1px solid var(--border-color);
            color: var(--primary-color);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1.5rem;
            display: block;
        }

        /* Topbar / Navbar styling */
        .topbar {
            background: var(--bg-card) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem !important;
            border-radius: 0 0 15px 15px;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-main);
        }

        .topbar h6 {
            color: var(--bs-heading-color);
            font-weight: 500;
        }

        /* General Card Overrides */
        .card {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 16px !important;
            box-shadow: var(--shadow-main) !important;
            transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease, border-color var(--transition-speed) ease !important;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover) !important;
            border-color: rgba(224, 158, 57, 0.3) !important;
        }

        .card-header {
            background-color: var(--table-header-bg) !important;
            border-bottom: 1px solid var(--border-color) !important;
            padding: 1.2rem 1.5rem !important;
            border-radius: 16px 16px 0 0 !important;
            color: var(--bs-heading-color) !important;
            font-weight: 600;
        }

        .card-header.bg-primary,
        .card-header.bg-success,
        .card-header.bg-secondary,
        .card-header.bg-dark {
            background: var(--primary-gradient) !important;
            color: #ffffff !important;
            border-bottom-color: rgba(255, 255, 255, 0.22) !important;
        }

        .card-header.bg-primary h1,
        .card-header.bg-primary h2,
        .card-header.bg-primary h3,
        .card-header.bg-primary h4,
        .card-header.bg-primary h5,
        .card-header.bg-primary h6,
        .card-header.bg-success h1,
        .card-header.bg-success h2,
        .card-header.bg-success h3,
        .card-header.bg-success h4,
        .card-header.bg-success h5,
        .card-header.bg-success h6,
        .card-header.bg-secondary h1,
        .card-header.bg-secondary h2,
        .card-header.bg-secondary h3,
        .card-header.bg-secondary h4,
        .card-header.bg-secondary h5,
        .card-header.bg-secondary h6,
        .card-header.bg-dark h1,
        .card-header.bg-dark h2,
        .card-header.bg-dark h3,
        .card-header.bg-dark h4,
        .card-header.bg-dark h5,
        .card-header.bg-dark h6 {
            color: #ffffff !important;
        }

        .card-body {
            padding: 1.5rem !important;
            color: var(--text-main);
        }

        .card-footer {
            background-color: var(--table-header-bg) !important;
            border-top: 1px solid var(--border-color) !important;
            padding: 1.2rem 1.5rem !important;
            border-radius: 0 0 16px 16px !important;
        }

        /* Button Styling Overrides */
        .btn {
            border-radius: 10px !important;
            padding: 0.6rem 1.4rem !important;
            font-weight: 500 !important;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.02) !important;
        }

        .btn:active {
            transform: translateY(1px) scale(0.98) !important;
        }

        .btn-primary {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 4px 15px var(--btn-primary-shadow) !important;
        }

        .btn-primary:hover {
            opacity: 0.95;
            box-shadow: 0 6px 20px var(--btn-primary-shadow) !important;
        }

        .btn-success {
            background: var(--btn-success-bg) !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 4px 15px var(--btn-success-shadow) !important;
        }

        .btn-success:hover {
            opacity: 0.95;
            box-shadow: 0 6px 20px var(--btn-success-shadow) !important;
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background: transparent !important;
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient) !important;
            color: #fff !important;
            border-color: transparent !important;
        }

        .btn-outline-success {
            color: var(--success-color) !important;
            border-color: var(--success-color) !important;
            background: transparent !important;
        }

        .btn-outline-success:hover {
            background: var(--btn-success-bg) !important;
            color: #fff !important;
            border-color: transparent !important;
        }

        .btn-outline-secondary {
            color: var(--text-muted) !important;
            border-color: var(--border-color) !important;
            background: transparent !important;
        }

        .btn-outline-secondary:hover {
            background: rgba(var(--bs-body-color-rgb), 0.05) !important;
            color: var(--bs-heading-color) !important;
            border-color: var(--border-color) !important;
        }

        .btn-danger {
            background: var(--btn-danger-bg) !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 4px 15px var(--btn-danger-shadow) !important;
        }

        .btn-danger:hover {
            opacity: 0.95;
            box-shadow: 0 6px 20px var(--btn-danger-shadow) !important;
        }

        .btn-outline-danger {
            color: var(--danger-color) !important;
            border-color: var(--danger-color) !important;
            background: transparent !important;
        }

        .btn-outline-danger:hover {
            background: var(--btn-danger-bg) !important;
            color: #fff !important;
            border-color: transparent !important;
        }

        /* Form Styling Overrides */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            border-radius: 10px !important;
            padding: 0.6rem 1rem !important;
            transition: all var(--transition-speed) ease !important;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--input-focus-bg) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(224, 158, 57, 0.25) !important;
            color: var(--text-main) !important;
        }

        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.6;
        }

        .form-control[type="file"]::file-selector-button {
            background-color: rgba(var(--bs-body-color-rgb), 0.08) !important;
            color: var(--text-main) !important;
            border-style: solid !important;
            border-width: 0 !important;
            border-inline-end-width: 1px !important;
            border-inline-end-color: var(--border-color) !important;
            margin-top: -0.6rem !important;
            margin-bottom: -0.6rem !important;
            margin-left: -1rem !important;
            margin-right: 1rem !important;
            padding: 0.6rem 1rem !important;
            transition: all var(--transition-speed) ease !important;
        }

        .form-control[type="file"]::file-selector-button:hover {
            background-color: rgba(var(--bs-body-color-rgb), 0.15) !important;
        }

        .form-label {
            color: var(--text-main) !important;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Table Design Overrides */
        .table {
            --bs-table-color: var(--text-main) !important;
            --bs-table-striped-color: var(--text-main) !important;
            --bs-table-bg: transparent !important;
            --bs-table-striped-bg: var(--table-hover-bg) !important;
            --bs-table-hover-bg: var(--table-hover-bg) !important;
            --bs-table-border-color: var(--border-color) !important;
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
            vertical-align: middle;
        }

        .table th {
            background-color: var(--table-header-bg) !important;
            color: var(--bs-heading-color) !important;
            font-weight: 600;
            padding: 1rem !important;
            border-bottom: 2px solid var(--border-color) !important;
        }

        .table td {
            padding: 1rem !important;
            color: var(--text-main) !important;
            background-color: transparent !important;
            border-bottom: 1px solid var(--border-color) !important;
        }

        .table-striped tbody tr:nth-of-type(odd) td {
            background-color: var(--table-hover-bg) !important;
        }

        .table-hover tbody tr:hover td {
            background-color: var(--table-hover-bg) !important;
        }

        /* Badges Overrides */
        .badge {
            padding: 0.45em 0.85em !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .bg-success {
            background-color: rgba(46, 204, 113, 0.15) !important;
            color: #2ecc71 !important;
            border: 1px solid rgba(46, 204, 113, 0.3) !important;
        }

        .bg-secondary {
            background-color: rgba(156, 151, 166, 0.15) !important;
            color: #9c97a6 !important;
            border: 1px solid rgba(156, 151, 166, 0.3) !important;
        }

        .bg-warning {
            background-color: rgba(241, 196, 15, 0.15) !important;
            color: #f1c40f !important;
            border: 1px solid rgba(241, 196, 15, 0.3) !important;
        }

        .bg-danger {
            background-color: rgba(231, 76, 60, 0.15) !important;
            color: #e74c3c !important;
            border: 1px solid rgba(231, 76, 60, 0.3) !important;
        }

        .bg-primary {
            background-color: rgba(52, 152, 219, 0.15) !important;
            color: #3498db !important;
            border: 1px solid rgba(52, 152, 219, 0.3) !important;
        }

        .bg-dark {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: var(--text-main) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        /* Alert Styling */
        .alert {
            border-radius: 12px !important;
            padding: 1rem 1.25rem !important;
            border: none !important;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.15) !important;
            color: #2ecc71 !important;
            border-left: 4px solid #2ecc71 !important;
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.15) !important;
            color: #e74c3c !important;
            border-left: 4px solid #e74c3c !important;
        }

        .alert-info {
            background-color: rgba(52, 152, 219, 0.15) !important;
            color: #3498db !important;
            border-left: 4px solid #3498db !important;
        }

        /* Pagination Overrides */
        .pagination {
            margin-top: 1.5rem;
        }

        .page-item .page-link {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            padding: 0.6rem 1rem;
            transition: all var(--transition-speed) ease;
        }

        .page-item:first-child .page-link {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .page-item:last-child .page-link {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .page-item .page-link:hover {
            background-color: var(--primary-color) !important;
            color: #fff !important;
            border-color: var(--primary-color) !important;
        }

        .page-item.active .page-link {
            background: var(--primary-gradient) !important;
            border-color: transparent !important;
            color: #fff !important;
        }

        .page-item.disabled .page-link {
            background-color: var(--disabled-page-bg) !important;
            color: var(--disabled-page-color) !important;
            border-color: var(--border-color) !important;
        }

        /* custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(var(--bs-body-color-rgb), 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* Card stat border left */
        .card-stat {
            border-left: 4px solid var(--primary-color) !important;
        }

        /* List group item override */
        .list-group-item {
            background-color: var(--list-item-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            transition: all var(--transition-speed) ease;
        }

        .list-group-item:hover {
            background-color: var(--list-item-hover-bg) !important;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
            @include('layouts.navbar')
            <div class="py-4">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeUI(newTheme);
    }

    function updateThemeUI(theme) {
        const themeIcon = document.getElementById('themeIcon');
        const themeText = document.getElementById('themeText');
        if (!themeIcon) return;
        
        if (theme === 'light') {
            themeIcon.className = 'bi bi-moon-fill';
            themeText.textContent = 'Dark Mode';
        } else {
            themeIcon.className = 'bi bi-sun-fill';
            themeText.textContent = 'Light Mode';
        }
    }

    // Call update on page load
    document.addEventListener('DOMContentLoaded', () => {
        const activeTheme = document.documentElement.getAttribute('data-theme') || 'dark';
        updateThemeUI(activeTheme);

        // Mobile responsive sidebar backdrop logic
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            const backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            document.body.appendChild(backdrop);
            
            sidebar.addEventListener('show.bs.collapse', () => {
                backdrop.classList.add('show');
            });
            sidebar.addEventListener('hide.bs.collapse', () => {
                backdrop.classList.remove('show');
            });
            
            backdrop.addEventListener('click', () => {
                const bsCollapse = bootstrap.Collapse.getInstance(sidebar);
                if (bsCollapse) {
                    bsCollapse.hide();
                } else {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                }
            });
        }

        // Prevent double submit on all forms globally
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (form.classList.contains('no-double-submit-prevent')) return;
                
                if (form.checkValidity() === false) {
                    return;
                }
                
                const submitButtons = form.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    setTimeout(() => {
                        button.disabled = true;
                        const originalText = button.textContent.trim() || 'Simpan';
                        button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${originalText}...`;
                    }, 10);
                });
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>
