import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function CustomerOrders() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  
  // Note: in a real app, you would fetch orders by user_id or session token.
  // For now we just fetch the latest orders.
  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async () => {
    try {
      const savedOrders = JSON.parse(localStorage.getItem('customer_order_ids') || '[]');
      
      if (savedOrders.length === 0) {
        setOrders([]);
        setLoading(false);
        return;
      }

      // Filter untuk order hari ini / 24 jam terakhir agar otomatis kereset esok harinya
      const yesterday = new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString();

      const { data, error } = await supabase
        .from('orders')
        .select('*')
        .in('id', savedOrders)
        .gte('created_at', yesterday)
        .order('created_at', { ascending: false });

      if (error) throw error;
      setOrders(data || []);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  const formatRupiah = (num) => new Intl.NumberFormat('id-ID').format(num);
  const formatDate = (dateStr) => {
    const d = new Date(dateStr);
    return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth()+1).toString().padStart(2, '0')}/${d.getFullYear()} ${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}`;
  };

  const renderStatusPesanan = (status) => {
    switch (status) {
      case 'menunggu': return <span className="badge bg-warning text-dark">Menunggu</span>;
      case 'diterima_dapur': return <span className="badge bg-info text-dark">Dikonfirmasi Dapur</span>;
      case 'diproses': return <span className="badge bg-primary">Diproses</span>;
      case 'siap_saji': return <span className="badge bg-success">Siap Saji</span>;
      case 'selesai': return <span className="badge bg-secondary">Selesai</span>;
      case 'dibatalkan': return <span className="badge bg-danger">Dibatalkan</span>;
      default: return <span className="badge bg-secondary">{status}</span>;
    }
  };

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3">Pesanan Saya</h1>
        <Link to="/customer/dashboard" className="btn btn-success">Pesan Lagi</Link>
      </div>

      <div className="card border-0 shadow-sm" style={{ backgroundColor: 'var(--bg-card)', borderColor: 'var(--border-color)' }}>
        <div className="card-body">
          {loading ? (
            <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>
          ) : orders.length === 0 ? (
            <div className="text-center py-5">
              <p className="text-muted">Belum ada pesanan.</p>
              <Link to="/customer/dashboard" className="btn btn-primary">Buat Pesanan Pertama</Link>
            </div>
          ) : (
            <>
              {/* Desktop View */}
              <div className="d-none d-md-block table-responsive">
                <table className="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th style={{ color: 'var(--text-main)' }}>Kode</th>
                      <th style={{ color: 'var(--text-main)' }}>Tanggal</th>
                      <th style={{ color: 'var(--text-main)' }}>Meja</th>
                      <th style={{ color: 'var(--text-main)' }}>Total</th>
                      <th style={{ color: 'var(--text-main)' }}>Status Pesanan</th>
                      <th style={{ color: 'var(--text-main)' }}>Status Pembayaran</th>
                      <th style={{ color: 'var(--text-main)' }}>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {orders.map(order => (
                      <tr key={order.id}>
                        <td><strong style={{ color: 'var(--text-main)' }}>{order.kode_pesanan}</strong></td>
                        <td style={{ color: 'var(--text-main)' }}>{formatDate(order.created_at)}</td>
                        <td style={{ color: 'var(--text-main)' }}>{order.nomor_meja || '-'}</td>
                        <td style={{ color: 'var(--text-main)' }}>Rp {formatRupiah(order.total_harga)}</td>
                        <td>{renderStatusPesanan(order.status_pesanan)}</td>
                        <td>
                          {order.status_pembayaran === 'lunas' ? (
                            <span className="badge bg-success">Lunas</span>
                          ) : (
                            <span className="badge bg-danger">Belum Bayar</span>
                          )}
                        </td>
                        <td>
                          <Link to={`/customer/orders/${order.id}`} className="btn btn-sm btn-primary me-2">Detail</Link>
                          {order.status_pembayaran !== 'lunas' && order.status_pesanan !== 'dibatalkan' && (
                            <Link to={`/customer/orders/${order.id}`} className="btn btn-sm btn-success">Bayar</Link>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile View */}
              <div className="d-md-none">
                {orders.map(order => (
                  <div key={order.id} className="card border-0 shadow-sm mb-3" style={{ backgroundColor: 'var(--bg-card)', border: '1px solid rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                    <div className="card-body p-3">
                      <div className="d-flex justify-content-between align-items-center mb-2">
                        <h6 className="fw-bold mb-0 text-primary">{order.kode_pesanan}</h6>
                        <small className="text-muted">{formatDate(order.created_at)}</small>
                      </div>
                      <hr className="my-2 opacity-50" style={{ color: 'var(--bs-body-color)' }} />
                      <div className="row g-2 mb-3">
                        <div className="col-6">
                          <small className="text-muted d-block">Meja</small>
                          <span className="fw-semibold" style={{ color: 'var(--text-main)' }}>{order.nomor_meja || '-'}</span>
                        </div>
                        <div className="col-6 text-end">
                          <small className="text-muted d-block">Total</small>
                          <span className="fw-bold text-success">Rp {formatRupiah(order.total_harga)}</span>
                        </div>
                      </div>
                      <div className="d-flex flex-wrap gap-2 mb-3">
                        <div>
                          <small className="text-muted d-block mb-1">Status Pesanan</small>
                          {renderStatusPesanan(order.status_pesanan)}
                        </div>
                        <div className="ms-auto text-end">
                          <small className="text-muted d-block mb-1">Pembayaran</small>
                          {order.status_pembayaran === 'lunas' ? (
                            <span className="badge bg-success">Lunas</span>
                          ) : (
                            <span className="badge bg-danger">Belum Bayar</span>
                          )}
                        </div>
                      </div>
                      <div className="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style={{ borderTopColor: 'rgba(var(--bs-body-color-rgb), 0.1)' }}>
                        <Link to={`/customer/orders/${order.id}`} className="btn btn-sm btn-primary px-3">Detail</Link>
                        {order.status_pembayaran !== 'lunas' && order.status_pesanan !== 'dibatalkan' && (
                          <Link to={`/customer/orders/${order.id}`} className="btn btn-sm btn-success px-3">Bayar</Link>
                        )}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
