import React, { useState, useEffect } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function KasirOrdersShow() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [order, setOrder] = useState(null);
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchOrderDetails();
  }, [id]);

  const fetchOrderDetails = async () => {
    try {
      setLoading(true);
      const { data: orderData, error: orderError } = await supabase
        .from('orders')
        .select('*')
        .eq('id', id)
        .single();
        
      if (orderError) throw orderError;
      
      const { data: itemsData, error: itemsError } = await supabase
        .from('order_items')
        .select('*')
        .eq('order_id', id);
        
      if (itemsError) throw itemsError;
      
      setOrder(orderData);
      setItems(itemsData || []);
    } catch (error) {
      console.error('Error fetching order details:', error.message);
      alert('Gagal memuat detail pesanan');
      navigate('/kasir/orders');
    } finally {
      setLoading(false);
    }
  };

  const handleCompleteOrder = async () => {
    if (!window.confirm('Selesaikan pesanan ini?')) return;
    
    try {
      const { error } = await supabase
        .from('orders')
        .update({ status_pesanan: 'selesai' })
        .eq('id', id);

      if (error) throw error;
      
      alert('Pesanan diselesaikan!');
      fetchOrderDetails();
    } catch (error) {
      console.error('Error completing order:', error.message);
      alert('Gagal menyelesaikan pesanan');
    }
  };

  const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;
  if (!order) return <div className="text-center py-5">Pesanan tidak ditemukan</div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Pesanan</h2>
        <Link to="/kasir/orders" className="btn btn-outline-secondary">Kembali</Link>
      </div>

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <p><strong>Kode Pesanan:</strong> {order.kode_pesanan}</p>
          <p><strong>Nomor Meja:</strong> {order.nomor_meja || '-'}</p>
          <p><strong>Atas Nama:</strong> {order.atas_nama || '-'}</p>
          <p><strong>Nama Pelanggan:</strong> {order.nama_pelanggan || '-'}</p>
          <p><strong>Tipe Pesanan:</strong>
            {order.tipe_pesanan === 'take_away' ? (
              <span className="badge bg-info"><i className="bi bi-bag me-1"></i>Take Away (Bungkus)</span>
            ) : (
              <span className="badge bg-primary"><i className="bi bi-shop me-1"></i>Dine In (Makan di Tempat)</span>
            )}
          </p>
          <p><strong>Status Pembayaran:</strong> 
            <span className={`badge bg-${order.status_pembayaran === 'lunas' ? 'success' : (order.status_pembayaran === 'menunggu_konfirmasi' ? 'info text-dark' : 'warning')} text-capitalize ms-2`}>
              {order.status_pembayaran ? order.status_pembayaran.replace(/_/g, ' ') : 'Belum Bayar'}
            </span>
          </p>

          {order.bukti_pembayaran && (
            <div className="mt-4 pt-3 border-top">
              <h6 className="fw-bold mb-2"><i className="bi bi-receipt"></i> Bukti Pembayaran QRIS:</h6>
              <div style={{ maxWidth: '300px' }}>
                <a href={order.bukti_pembayaran} target="_blank" rel="noreferrer" className="d-block border rounded p-2 text-center bg-light text-decoration-none">
                  <img src={order.bukti_pembayaran} className="img-fluid rounded" style={{ maxHeight: '200px' }} alt="Bukti Pembayaran" />
                  <span className="d-block small text-muted mt-2"><i className="bi bi-zoom-in"></i> Klik untuk memperbesar</span>
                </a>
              </div>
            </div>
          )}
        </div>
      </div>

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <h5 className="mb-3">Item Pesanan</h5>
          {/* Desktop View */}
          <div className="d-none d-md-block table-responsive">
            <table className="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Menu</th>
                  <th>Harga</th>
                  <th>Jumlah</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                {items.map(item => (
                  <tr key={item.id}>
                    <td>
                      {item.nama_menu}
                      {item.catatan_item && (
                        <div className="small text-muted font-monospace mt-1" style={{ fontSize: '0.8rem', whiteSpace: 'pre-line' }}>
                          <i className="bi bi-gear-wide-connected"></i> {item.catatan_item}
                        </div>
                      )}
                    </td>
                    <td>Rp {formatRupiah(item.harga)}</td>
                    <td>{item.jumlah}</td>
                    <td>Rp {formatRupiah(item.subtotal)}</td>
                  </tr>
                ))}
              </tbody>
              <tfoot>
                <tr>
                  <th colSpan="3" className="text-end">Total</th>
                  <th>Rp {formatRupiah(order.total_harga)}</th>
                </tr>
              </tfoot>
            </table>
          </div>

          {/* Mobile View */}
          <div className="d-md-none list-group list-group-flush">
            {items.map(item => (
              <div key={item.id} className="list-group-item px-0 py-2 border-0 border-bottom" style={{ background: 'transparent' }}>
                <div className="d-flex justify-content-between align-items-start mb-1">
                  <span className="fw-semibold" style={{ color: 'var(--bs-body-color)' }}>
                    {item.nama_menu}
                    {item.catatan_item && (
                      <div className="small text-muted font-monospace mt-1" style={{ fontSize: '0.75rem', whiteSpace: 'pre-line', fontWeight: 'normal' }}>
                        <i className="bi bi-gear-wide-connected"></i> {item.catatan_item}
                      </div>
                    )}
                  </span>
                  <span className="fw-bold text-primary">Rp {formatRupiah(item.subtotal)}</span>
                </div>
                <div className="d-flex justify-content-between align-items-center text-muted small">
                  <span>Rp {formatRupiah(item.harga)}</span>
                  <span>Jumlah: <strong>{item.jumlah}x</strong></span>
                </div>
              </div>
            ))}
            <div className="d-flex justify-content-between align-items-center mt-3 pt-2">
              <span className="fw-bold" style={{ color: 'var(--bs-body-color)' }}>Total:</span>
              <h5 className="fw-bold text-success mb-0">Rp {formatRupiah(order.total_harga)}</h5>
            </div>
          </div>
        </div>
      </div>

      <div className="d-flex gap-2">
        {order.status_pembayaran !== 'lunas' && order.status_pesanan !== 'dibatalkan' && (
          <Link to={`/kasir/orders/${order.id}/payment`} className="btn btn-success">Konfirmasi Pembayaran</Link>
        )}
        {(order.status_pesanan === 'siap_saji' || order.status_pesanan === 'diproses' || order.status_pesanan === 'diterima_dapur') && (
          <button className="btn btn-success" onClick={handleCompleteOrder}>
            <i className="bi bi-check2-circle me-1"></i> Selesaikan Pesanan
          </button>
        )}
        {order.status_pembayaran === 'lunas' && (
          <Link to={`/kasir/orders/${order.id}/receipt`} className="btn btn-outline-primary">Lihat Struk</Link>
        )}
      </div>
    </div>
  );
}
