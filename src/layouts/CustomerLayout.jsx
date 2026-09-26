import React, { useState } from 'react';
import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom';

export default function CustomerLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [theme, setTheme] = useState(localStorage.getItem('theme') || 'dark');
  const location = useLocation();
  const navigate = useNavigate();

  const toggleTheme = () => {
    const newTheme = theme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    setTheme(newTheme);
  };

  const isActive = (path) => location.pathname === path ? 'active' : '';

  return (
    <div className="container-fluid" style={{ minHeight: '100vh', overflowX: 'hidden' }}>
      <div className="row">
        {/* Sidebar (Desktop Only) */}
        <nav className="col-md-3 col-lg-2 d-none d-md-block sidebar">
          <div className="d-flex flex-column justify-content-between w-100" style={{ minHeight: 'calc(100vh - 3rem)' }}>
            <div>
              <div className="d-flex align-items-center justify-content-between mb-3 px-3">
                <Link to="/customer/dashboard" className="navbar-brand mb-0" style={{ marginBottom: '0 !important', flexGrow: 1, textAlign: 'left' }}>
                  Nasi Bakar Cak Win
                </Link>
              </div>
              
              <div className="px-3">
                <span className="role-badge">Role: customer</span>
              </div>
              
              <ul className="nav flex-column mt-2">
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/customer/dashboard')}`} to="/customer/dashboard">
                    <i className="bi bi-grid-1x2-fill"></i> Dashboard
                  </Link>
                </li>
                
                <li className="nav-item mt-2">
                  <Link className={`nav-link ${isActive('/customer/menus')}`} to="/customer/menus">
                    <i className="bi bi-menu-button-wide"></i> Menu
                  </Link>
                </li>
                
                <li className="nav-item">
                  <Link className={`nav-link ${isActive('/customer/orders')}`} to="/customer/orders">
                    <i className="bi bi-receipt"></i> Pesanan Saya
                  </Link>
                </li>
              </ul>
            </div>

            <div className="px-2 mt-auto pt-4">
              <button onClick={() => navigate('/login')} className="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                <i className="bi bi-box-arrow-right"></i> Keluar
              </button>
            </div>
          </div>
        </nav>



        {/* Main Content */}
        <main className="col-md-9 col-lg-10 ms-sm-auto px-md-4">
          <div className="d-flex justify-content-between align-items-center topbar">
            <div className="d-flex align-items-center gap-2">
              <h6 className="mb-0 d-sm-block fw-bold text-primary">Nasi Bakar Cak Win</h6>
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
                  <span className="d-none d-sm-inline">Customer</span>
                </button>
                <ul className="dropdown-menu dropdown-menu-end shadow-sm border-0" style={{ backgroundColor: 'var(--bg-card)' }}>
                  <li><h6 className="dropdown-header text-muted">Customer DeCafe</h6></li>
                  <li><hr className="dropdown-divider" style={{ borderColor: 'var(--border-color)' }} /></li>
                  <li>
                    <button onClick={() => navigate('/login')} className="dropdown-item text-danger"><i className="bi bi-box-arrow-right me-2"></i>Keluar</button>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div className="py-4" style={{ paddingBottom: '80px !important' }}>
            <Outlet />
          </div>
        </main>

        {/* Bottom Navigation for Mobile */}
        <div className="d-md-none position-fixed bottom-0 start-0 end-0 border-top shadow" style={{ zIndex: 1040, backgroundColor: 'var(--bg-card)', borderColor: 'var(--border-color)', height: '60px' }}>
          <div className="d-flex justify-content-around align-items-center h-100">
            <Link to="/customer/dashboard" className={`text-decoration-none text-center d-flex flex-column align-items-center ${location.pathname === '/customer/dashboard' ? 'text-primary' : 'text-muted'}`} style={{ width: '33%' }}>
              <i className={`bi fs-4 mb-1 ${location.pathname === '/customer/dashboard' ? 'bi-grid-1x2-fill' : 'bi-grid-1x2'}`} style={{ lineHeight: 1 }}></i>
              <small style={{ fontSize: '10px', fontWeight: '600' }}>Beranda</small>
            </Link>
            <Link to="/customer/menus" className={`text-decoration-none text-center d-flex flex-column align-items-center ${location.pathname === '/customer/menus' ? 'text-primary' : 'text-muted'}`} style={{ width: '33%' }}>
              <i className={`bi fs-4 mb-1 ${location.pathname === '/customer/menus' ? 'bi-menu-button-wide-fill' : 'bi-menu-button-wide'}`} style={{ lineHeight: 1 }}></i>
              <small style={{ fontSize: '10px', fontWeight: '600' }}>Menu</small>
            </Link>
            <Link to="/customer/orders" className={`text-decoration-none text-center d-flex flex-column align-items-center ${location.pathname.startsWith('/customer/orders') ? 'text-primary' : 'text-muted'}`} style={{ width: '33%' }}>
              <i className={`bi fs-4 mb-1 ${location.pathname.startsWith('/customer/orders') ? 'bi-receipt-cutoff' : 'bi-receipt'}`} style={{ lineHeight: 1 }}></i>
              <small style={{ fontSize: '10px', fontWeight: '600' }}>Pesanan</small>
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
