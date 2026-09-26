import React, { useState, useEffect } from 'react';
import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom';
import supabase from '../lib/supabase';

export default function DashboardLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [theme, setTheme] = useState(localStorage.getItem('theme') || 'dark');
  const location = useLocation();
  const navigate = useNavigate();

  useEffect(() => {
    supabase.auth.getSession().then(({ data: { session } }) => {
      if (!session) {
        navigate('/login');
      }
    });

    const {
      data: { subscription },
    } = supabase.auth.onAuthStateChange((_event, session) => {
      if (!session) {
        navigate('/login');
      }
    });

    return () => subscription.unsubscribe();
  }, [navigate]);

  const toggleTheme = () => {
    const newTheme = theme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    setTheme(newTheme);
  };

  const handleLogout = async (e) => {
    e.preventDefault();
    await supabase.auth.signOut();
    navigate('/login');
  };

  const isActive = (path) => location.pathname === path ? 'active' : '';

  return (
    <div className="container-fluid" style={{ minHeight: '100vh', overflowX: 'hidden' }}>
      <div className="row">
        {/* Sidebar */}
        <nav className={`col-md-3 col-lg-2 d-md-block sidebar ${sidebarOpen ? 'show' : 'collapse'}`}>
          <div className="d-flex flex-column justify-content-between w-100" style={{ minHeight: 'calc(100vh - 3rem)' }}>
            <div>
              <div className="d-flex align-items-center justify-content-between mb-3 px-3">
                <Link to="/dashboard" className="navbar-brand mb-0" style={{ marginBottom: '0 !important', flexGrow: 1, textAlign: 'left' }}>
                  Nasi Bakar Cak Win
                </Link>
                <button className="btn-close d-md-none" type="button" onClick={() => setSidebarOpen(false)} aria-label="Close"></button>
              </div>
              
              <div className="px-3">
                <span className="role-badge">Role: admin</span>
              </div>
              
              <ul className="nav flex-column mt-2">
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/dashboard')}`} to="/dashboard">
                    <i className="bi bi-grid-1x2-fill"></i> Dashboard
                  </Link>
                </li>
                
                <div className="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style={{ fontSize: '0.7rem', letterSpacing: '0.5px' }}>Admin</div>
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/admin/menus')}`} to="/admin/menus">
                    <i className="bi bi-cup-hot-fill"></i> Kelola Menu
                  </Link>
                </li>
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/admin/users')}`} to="/admin/users">
                    <i className="bi bi-people-fill"></i> Kelola User
                  </Link>
                </li>
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/admin/reports')}`} to="/admin/reports">
                    <i className="bi bi-graph-up-arrow"></i> Laporan
                  </Link>
                </li>
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/admin/meja')}`} to="/admin/meja">
                    <i className="bi bi-qr-code-scan"></i> Kelola Meja & QR
                  </Link>
                </li>

                <div className="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style={{ fontSize: '0.7rem', letterSpacing: '0.5px' }}>Kasir</div>
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/kasir')}`} to="/kasir">
                    <i className="bi bi-cash-coin"></i> Pembayaran (Kasir)
                  </Link>
                </li>

                <div className="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style={{ fontSize: '0.7rem', letterSpacing: '0.5px' }}>Pesanan</div>
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/pelayan/orders/create')}`} to="/pelayan/orders/create">
                    <i className="bi bi-cart-plus"></i> Buat Pesanan
                  </Link>
                </li>

                <div className="sidebar-heading px-3 mt-3 mb-1 text-muted text-uppercase fw-bold" style={{ fontSize: '0.7rem', letterSpacing: '0.5px' }}>Dapur</div>
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/dapur')}`} to="/dapur/orders">
                    <i className="bi bi-egg-fried"></i> Antrean Dapur
                  </Link>
                </li>

              </ul>
            </div>

            <div className="px-2 mt-auto pt-4">
              <form onSubmit={handleLogout} className="w-100">
                <button className="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2" type="submit">
                  <i className="bi bi-box-arrow-right"></i> Logout
                </button>
              </form>
            </div>
          </div>
        </nav>

        {/* Backdrop for mobile */}
        {sidebarOpen && (
          <div className="sidebar-backdrop show" onClick={() => setSidebarOpen(false)}></div>
        )}

        {/* Main Content */}
        <main className="col-md-9 col-lg-10 ms-sm-auto px-md-4">
          <div className="d-flex justify-content-between align-items-center topbar">
            <div className="d-flex align-items-center gap-2">
              <button className="btn btn-outline-secondary d-md-none me-2" onClick={() => setSidebarOpen(true)}>
                <i className="bi bi-list"></i>
              </button>
              <h6 className="mb-0 d-none d-sm-block">Nasi Bakar Cak Win - System</h6>
            </div>
            
            <div className="d-flex align-items-center gap-3">
              <button 
                onClick={toggleTheme} 
                className="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2"
                style={{ borderRadius: '20px !important' }}
              >
                <i id="themeIcon" className={theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill'}></i>
                <span id="themeText" className="d-none d-sm-inline">{theme === 'dark' ? 'Light Mode' : 'Dark Mode'}</span>
              </button>
              
              <div className="dropdown">
                <button className="btn btn-outline-primary btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style={{ borderRadius: '20px !important' }}>
                  <i className="bi bi-person-circle"></i>
                  <span className="d-none d-sm-inline">Admin</span>
                </button>
                <ul className="dropdown-menu dropdown-menu-end shadow-sm border-0" style={{ backgroundColor: 'var(--bg-card)' }}>
                  <li><h6 className="dropdown-header text-muted">Admin DeCafe</h6></li>
                  <li><Link className="dropdown-item" to="/dashboard" style={{ color: 'var(--text-main)' }}><i className="bi bi-person me-2"></i>Profile</Link></li>
                  <li><hr className="dropdown-divider" style={{ borderColor: 'var(--border-color)' }} /></li>
                  <li>
                    <form onSubmit={handleLogout}>
                      <button type="submit" className="dropdown-item text-danger"><i className="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div className="py-4">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
}
