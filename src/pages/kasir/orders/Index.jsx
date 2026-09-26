import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function KasirOrdersIndex() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchOrders();

    // Subscribe to new orders
    const subscription = supabase
      .channel('public:orders')
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
        .order('created_at', { ascending: false });

      if (error) throw error;
      setOrders(data || []);
    } catch (error) {
      console.error('Error fetching orders:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

  const getStatusBadge = (status) => {
    if (status === 'lunas') return <span className="badge bg-success">Lunas</span>;
    if (status === 'menunggu_konfirmasi') return <span className="badge bg-info text-dark">Menunggu Konfirmasi</span>;
    return <span className="badge bg-warning text-dark">{status ? status.replace(/_/g, ' ') : 'Belum Bayar'}</span>;
  };

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Pesanan Kasir</h2>
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
                  <th>Total</th>
                  <th>Status Pembayaran</th>
                  <th className="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {orders.length > 0 ? orders.map(order => (
                  <tr key={order.id}>
                    <td>{order.kode_pesanan}</td>
                    <td>{order.nomor_meja || '-'}</td>
                    <td>{order.atas_nama || order.nama_pelanggan || '-'}</td>
                    <td>Rp {formatRupiah(order.total_harga)}</td>
                    <td className="text-capitalize">{getStatusBadge(order.status_pembayaran)}</td>
                    <td className="text-end">
                      <Link to={`/kasir/orders/${order.id}`} className="btn btn-sm btn-outline-secondary">Detail</Link>
                    </td>
                  </tr>
                )) : (
                  <tr>
                    <td colSpan="6" className="text-center text-muted">Belum ada pesanan.</td>
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
                    <div className="text-capitalize">{getStatusBadge(order.status_pembayaran)}</div>
                  </div>
                  <hr className="my-2 opacity-50" style={{ color: 'var(--bs-body-color)' }} />
                  <div className="row g-2 mb-3">
                    <div className="col-6">
                      <small className="text-muted d-block">Meja / Atas Nama</small>
                      <span className="fw-semibold">{order.nomor_meja || '-'} / {order.atas_nama || order.nama_pelanggan || '-'}</span>
                    </div>
                    <div className="col-6 text-end">
                      <small className="text-muted d-block">Total</small>
                      <span className="fw-bold text-success">Rp {formatRupiah(order.total_harga)}</span>
                    </div>
                  </div>
                  <div className="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style={{ borderTopColor: 'rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                    <Link to={`/kasir/orders/${order.id}`} className="btn btn-sm btn-outline-secondary px-3">Detail</Link>
                  </div>
                </div>
              </div>
            )) : (
              <div className="text-center py-4 text-muted">Belum ada pesanan.</div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
