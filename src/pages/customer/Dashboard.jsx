import React, { useState, useEffect } from 'react';
import { useSearchParams, useNavigate, Link } from 'react-router-dom';
import supabase from '../../lib/supabase';

export default function CustomerDashboard() {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const t = searchParams.get('t'); // QR Token
  
  const savedMeja = localStorage.getItem('customer_meja') || '';
  const [nomorMeja, setNomorMeja] = useState(savedMeja);
  
  const [recommendedMenus, setRecommendedMenus] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchRecommendations();
    if (t) {
      resolveToken(t);
    }
  }, [t]);

  const resolveToken = async (token) => {
    try {
      const { data, error } = await supabase
        .from('meja')
        .select('nomor_meja')
        .eq('token', token)
        .single();
        
      if (!error && data) {
        setNomorMeja(data.nomor_meja);
        localStorage.setItem('customer_meja', data.nomor_meja);
      }
    } catch (e) {
      console.error(e);
    }
  };

  const fetchRecommendations = async () => {
    try {
      const { data, error } = await supabase
        .from('menus')
        .select('*')
        .eq('status', 'tersedia')
        .limit(4);

      if (!error && data) {
        setRecommendedMenus(data);
      }
    } catch (error) {
      console.error('Error fetching menus:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const formatRupiah = (num) => new Intl.NumberFormat('id-ID').format(num);

  return (
    <>
      <style>
        {`
          .dashboard-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: 1px solid var(--border-color) !important; background-color: var(--bg-card); }
          .dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border-color: var(--primary-color) !important; }
          .step-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: rgba(var(--bs-primary-rgb), 0.1); color: var(--primary-color); margin-bottom: 1rem; }
        `}
      </style>

      {/* Hero Welcome Section */}
      <div className="welcome-banner p-4 p-md-5 rounded-4 mb-5 shadow-sm text-center text-md-start" style={{ background: 'var(--primary-gradient)', color: '#fff', position: 'relative', overflow: 'hidden' }}>
        <div className="row align-items-center position-relative" style={{ zIndex: 1 }}>
          <div className="col-md-8">
            <h1 className="fw-bold mb-3 display-5">Selamat Datang di Nasi Bakar Cak Win! 😋</h1>
            {nomorMeja ? (
              <div className="d-inline-flex align-items-center gap-2 bg-white text-dark px-4 py-2 rounded-pill shadow-sm mb-4">
                <i className="bi bi-geo-alt-fill text-danger fs-5"></i>
                <span className="fs-5">Anda berada di <strong>Meja {nomorMeja}</strong></span>
              </div>
            ) : (
              <p className="fs-5 mb-4 opacity-90">Pesan makanan dan minuman favorit Anda langsung dari meja tanpa harus antre di kasir.</p>
            )}
            <div>
              <Link to="/customer/menus" className="btn btn-light btn-lg px-5 py-3 fw-bold shadow text-primary" style={{ borderRadius: '30px' }}>
                <i className="bi bi-journal-text me-2"></i> Lihat Menu & Pesan Sekarang
              </Link>
            </div>
          </div>
          <div className="col-md-4 d-none d-md-block text-center">
            <i className="bi bi-shop" style={{ fontSize: '10rem', opacity: 0.8 }}></i>
          </div>
        </div>
        <div style={{ position: 'absolute', top: '-10%', right: '-5%', fontSize: '15rem', opacity: 0.1, zIndex: 0, transform: 'rotate(15deg)' }}>
          <i className="bi bi-cup-hot-fill"></i>
        </div>
      </div>

      {/* How it Works Section */}
      <div className="mb-5">
        <h4 className="fw-bold mb-4 text-center">Cara Pesan Mudah</h4>
        <div className="row g-4 text-center">
          <div className="col-md-4">
            <div className="card dashboard-card h-100 p-4 border-0 rounded-4">
              <div className="d-flex justify-content-center">
                <div className="step-icon"><i className="bi bi-hand-index-thumb"></i></div>
              </div>
              <h5 className="fw-bold">1. Pilih Menu</h5>
              <p className="text-muted small mb-0">Lihat daftar menu kami, sesuaikan pesanan (lauk, sambal) lalu masukkan ke keranjang.</p>
            </div>
          </div>
          <div className="col-md-4">
            <div className="card dashboard-card h-100 p-4 border-0 rounded-4">
              <div className="d-flex justify-content-center">
                <div className="step-icon text-success" style={{ background: 'rgba(25, 135, 84, 0.1)' }}><i className="bi bi-qr-code-scan"></i></div>
              </div>
              <h5 className="fw-bold">2. Bayar Mudah</h5>
              <p className="text-muted small mb-0">Selesaikan pesanan Anda dan bayar dengan memindai kode QRIS langsung dari HP.</p>
            </div>
          </div>
          <div className="col-md-4">
            <div className="card dashboard-card h-100 p-4 border-0 rounded-4">
              <div className="d-flex justify-content-center">
                <div className="step-icon text-warning" style={{ background: 'rgba(255, 193, 7, 0.1)' }}><i className="bi bi-emoji-smile"></i></div>
              </div>
              <h5 className="fw-bold">3. Nikmati Makanan</h5>
              <p className="text-muted small mb-0">Tunggu dengan santai di meja Anda, pelayan kami akan mengantarkan pesanan segera.</p>
            </div>
          </div>
        </div>
      </div>

      {/* Recommendations Section */}
      <div className="mb-5">
        <div className="d-flex justify-content-between align-items-end mb-4">
          <div>
            <h4 className="fw-bold mb-1">Rekomendasi Kami <i className="bi bi-stars text-warning"></i></h4>
            <p className="text-muted small mb-0">Pilihan favorit pelanggan Nasi Bakar Cak Win</p>
          </div>
          <Link to="/customer/menus" className="btn btn-outline-primary btn-sm d-none d-sm-inline-block" style={{ borderRadius: '20px' }}>
            Lihat Semua <i className="bi bi-arrow-right"></i>
          </Link>
        </div>
        
        {loading ? (
          <div className="text-center py-4"><div className="spinner-border text-primary"></div></div>
        ) : (
          <div className="row g-3">
            {recommendedMenus.map(menu => (
              <div key={menu.id} className="col-6 col-md-3">
                <div className="card dashboard-card h-100 overflow-hidden rounded-4">
                  {menu.gambar ? (
                    <img src={`/storage/${menu.gambar}`} className="card-img-top" alt={menu.nama_menu} style={{ height: 120, objectFit: 'cover' }} onError={(e) => { e.target.onerror = null; e.target.src = "https://via.placeholder.com/300x160?text=No+Image"; }} />
                  ) : (
                    <div className="bg-secondary-subtle d-flex align-items-center justify-content-center" style={{ height: 120 }}>
                      <i className="bi bi-cup-hot fs-2 text-muted opacity-50"></i>
                    </div>
                  )}
                  <div className="card-body p-3">
                    <h6 className="fw-bold mb-1 text-truncate" title={menu.nama_menu}>{menu.nama_menu}</h6>
                    <p className="text-primary fw-bold small mb-0">Rp {formatRupiah(menu.harga)}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
        <div className="text-center mt-4 d-sm-none">
          <Link to="/customer/menus" className="btn btn-outline-primary rounded-pill w-100">
            Lihat Semua Menu <i className="bi bi-arrow-right"></i>
          </Link>
        </div>
      </div>

    </>
  );
}
