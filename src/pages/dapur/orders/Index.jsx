import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function DapurOrdersIndex() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchOrders();

    const subscription = supabase
      .channel('public:orders:dapur')
      .on('postgres_changes', { event: '*', schema: 'public', table: 'orders' }, payload => {
        fetchOrders();
      })
      .subscribe();

    return () => {
      supabase.removeChannel(subscription);
    };
  }, []);

  const fetchOrders = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('orders')
        .select('*')
        .neq('status_pesanan', 'dibatalkan')
        .neq('status_pesanan', 'selesai')
        .order('created_at', { ascending: false });

      if (error) throw error;
      setOrders(data || []);
    } catch (error) {
      console.error('Error fetching orders:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const getStatusBadge = (status) => {
    if (status === 'menunggu') return <span className="badge bg-warning text-dark">Menunggu</span>;
    if (status === 'diterima_dapur') return <span className="badge bg-info text-dark">Diterima Dapur</span>;
    if (status === 'diproses') return <span className="badge bg-primary">Diproses</span>;
    if (status === 'siap_saji') return <span className="badge bg-success">Siap Saji</span>;
    return <span className="badge bg-secondary text-capitalize">{status ? status.replace(/_/g, ' ') : ''}</span>;
  };

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Pesanan Masuk (Dapur)</h2>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          {/* Desktop View */}
          <div className="d-none d-md-block table-responsive">
            <table className="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Kode</th>
                  <th>Meja</th>
                  <th>Atas Nama</th>
                  <th>Status</th>
                  <th className="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {orders.length > 0 ? orders.map(order => (
                  <tr key={order.id}>
                    <td>{order.kode_pesanan}</td>
                    <td>{order.nomor_meja || '-'}</td>
                    <td>{order.atas_nama || order.nama_pelanggan || '-'}</td>
                    <td>{getStatusBadge(order.status_pesanan)}</td>
                    <td className="text-end">
                      <Link to={`/dapur/orders/${order.id}`} className="btn btn-sm btn-outline-secondary">Detail</Link>
                    </td>
                  </tr>
                )) : (
                  <tr>
                    <td colSpan="5" className="text-center text-muted">Belum ada pesanan aktif.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {/* Mobile View */}
          <div className="d-md-none">
            {orders.length > 0 ? orders.map(order => (
              <div key={order.id} className="card border-0 shadow-sm mb-3" style={{ backgroundColor: 'var(--bs-card-bg)', border: '1px solid rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                <div className="card-body p-3">
                  <div className="d-flex justify-content-between align-items-center mb-2">
                    <h6 className="fw-bold mb-0 text-primary">{order.kode_pesanan}</h6>
                    {getStatusBadge(order.status_pesanan)}
                  </div>
                  <hr className="my-2 opacity-50" style={{ color: 'var(--bs-body-color)' }} />
                  <div className="row g-2 mb-3">
                    <div className="col-12">
                      <small className="text-muted d-block">Meja / Atas Nama</small>
                      <span className="fw-semibold">{order.nomor_meja || '-'} / {order.atas_nama || order.nama_pelanggan || '-'}</span>
                    </div>
                  </div>
                  <div className="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style={{ borderTopColor: 'rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                    <Link to={`/dapur/orders/${order.id}`} className="btn btn-sm btn-outline-secondary px-3">Detail</Link>
                  </div>
                </div>
              </div>
            )) : (
              <div className="text-center py-4 text-muted">Belum ada pesanan aktif.</div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
