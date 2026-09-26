import React from 'react';
import { Link } from 'react-router-dom';

export default function AdminDashboard() {
  return (
    <>
      <style>
        {`
          .dashboard-stat-link {
            display: block;
            height: 100%;
            color: inherit;
            text-decoration: none;
            border-radius: 16px;
          }
          .dashboard-stat-link .card {
            height: 100%;
          }
          .dashboard-stat-link:focus-visible {
            outline: 3px solid var(--primary-color);
            outline-offset: 4px;
          }
          .dashboard-stat-link .stat-action {
            color: var(--primary-color);
            font-size: 0.85rem;
            font-weight: 600;
          }
        `}
      </style>

      <h2 className="mb-4">Dashboard Admin</h2>
      
      <div className="row g-3 mb-4">
        <div className="col-md-4">
          <Link to="/dashboard" className="dashboard-stat-link" aria-label="Buka Kelola Menu">
            <div className="card card-stat shadow-sm">
              <div className="card-body">
                <p className="text-muted mb-1">Total Menu</p>
                <h4 className="mb-2">12</h4>
                <span className="stat-action">Kelola Menu <i className="bi bi-arrow-right-short"></i></span>
              </div>
            </div>
          </Link>
        </div>
        <div className="col-md-4">
          <Link to="/dashboard" className="dashboard-stat-link" aria-label="Buka Kelola User">
            <div className="card card-stat shadow-sm">
              <div className="card-body">
                <p className="text-muted mb-1">Total User</p>
                <h4 className="mb-2">5</h4>
                <span className="stat-action">Kelola User <i className="bi bi-arrow-right-short"></i></span>
              </div>
            </div>
          </Link>
        </div>
        <div className="col-md-4">
          <Link to="/dashboard" className="dashboard-stat-link" aria-label="Buka Laporan Pesanan Hari Ini">
            <div className="card card-stat shadow-sm">
              <div className="card-body">
                <p className="text-muted mb-1">Pesanan Hari Ini</p>
                <h4 className="mb-2">8</h4>
                <span className="stat-action">Lihat Laporan <i className="bi bi-arrow-right-short"></i></span>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <div className="row g-3 mb-4">
        <div className="col-md-4">
          <Link to="/dashboard" className="dashboard-stat-link" aria-label="Buka Laporan Pendapatan Hari Ini">
            <div className="card card-stat shadow-sm">
              <div className="card-body">
                <p className="text-muted mb-1">Pendapatan Hari Ini</p>
                <h4 className="mb-2">Rp 1.550.000</h4>
                <span className="stat-action">Lihat Laporan <i className="bi bi-arrow-right-short"></i></span>
              </div>
            </div>
          </Link>
        </div>
        <div className="col-md-4">
          <Link to="/dashboard" className="dashboard-stat-link" aria-label="Buka Laporan Total Transaksi">
            <div className="card card-stat shadow-sm">
              <div className="card-body">
                <p className="text-muted mb-1">Total Transaksi</p>
                <h4 className="mb-2">142</h4>
                <span className="stat-action">Lihat Laporan <i className="bi bi-arrow-right-short"></i></span>
              </div>
            </div>
          </Link>
        </div>
        <div className="col-md-4">
          <Link to="/dashboard" className="dashboard-stat-link" aria-label="Buka Laporan Pesanan Belum Dibayar">
            <div className="card card-stat shadow-sm">
              <div className="card-body">
                <p className="text-muted mb-1">Pesanan Belum Dibayar</p>
                <h4 className="mb-2">2</h4>
                <span className="stat-action">Lihat Laporan <i className="bi bi-arrow-right-short"></i></span>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <div className="row g-3">
        <div className="col-md-6">
          <div className="card shadow-sm">
            <div className="card-body">
              <h5 className="mb-3">Menu Terlaris</h5>
              <ul className="list-group list-group-flush">
                <li className="list-group-item d-flex justify-content-between">
                  <span>Nasi Bakar Ayam Suwir</span>
                  <span className="fw-semibold">45</span>
                </li>
                <li className="list-group-item d-flex justify-content-between">
                  <span>Es Teh Manis</span>
                  <span className="fw-semibold">32</span>
                </li>
                <li className="list-group-item d-flex justify-content-between">
                  <span>Nasi Bakar Cumi Asin</span>
                  <span className="fw-semibold">28</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div className="col-md-6">
          <div className="card shadow-sm">
            <div className="card-body">
              <h5 className="mb-3">Ringkasan Pesanan</h5>
              <ul className="list-group list-group-flush">
                <li className="list-group-item d-flex justify-content-between">
                  <span>Pesanan Selesai</span>
                  <span className="fw-semibold">140</span>
                </li>
                <li className="list-group-item d-flex justify-content-between">
                  <span>Pesanan Belum Dibayar</span>
                  <span className="fw-semibold">2</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
