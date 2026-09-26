import React, { useState, useEffect } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function DapurOrdersShow() {
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
      navigate('/dapur/orders');
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateStatus = async (newStatus, confirmMessage) => {
    if (!window.confirm(confirmMessage)) return;
    
    try {
      const { error } = await supabase
        .from('orders')
        .update({ status_pesanan: newStatus })
        .eq('id', id);

      if (error) throw error;
      
      fetchOrderDetails();
    } catch (error) {
      console.error('Error updating order:', error.message);
      alert('Gagal mengupdate pesanan');
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
  if (!order) return <div className="text-center py-5">Pesanan tidak ditemukan</div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Pesanan Dapur</h2>
        <Link to="/dapur/orders" className="btn btn-outline-secondary">Kembali</Link>
      </div>

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <p><strong>Kode Pesanan:</strong> {order.kode_pesanan}</p>
          <p><strong>Nomor Meja:</strong> {order.nomor_meja || '-'}</p>
          <p><strong>Atas Nama:</strong> {order.atas_nama || '-'}</p>
          <p><strong>Tipe Pesanan:</strong>
            {order.tipe_pesanan === 'take_away' ? (
              <span className="badge bg-info ms-2"><i className="bi bi-bag me-1"></i>Take Away (Bungkus)</span>
            ) : (
              <span className="badge bg-primary ms-2"><i className="bi bi-shop me-1"></i>Dine In (Makan di Tempat)</span>
            )}
          </p>
          <p><strong>Status Pesanan:</strong> 
            <span className="ms-2">{getStatusBadge(order.status_pesanan)}</span>
          </p>
          <p><strong>Catatan:</strong> {order.catatan || '-'}</p>
        </div>
      </div>

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <h5 className="mb-3">Item Pesanan</h5>
          <div className="d-none d-md-block table-responsive">
            <table className="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Menu</th>
                  <th>Jumlah</th>
                  <th>Catatan</th>
                </tr>
              </thead>
              <tbody>
                {items.map(item => (
                  <tr key={item.id}>
                    <td className="fw-semibold">{item.nama_menu}</td>
                    <td><span className="badge bg-secondary fs-6">{item.jumlah}x</span></td>
                    <td style={{ whiteSpace: 'pre-line' }}>{item.catatan_item || '-'}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="d-md-none list-group list-group-flush">
            {items.map(item => (
              <div key={item.id} className="list-group-item px-0 py-2 border-0 border-bottom" style={{ background: 'transparent' }}>
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <span className="fw-semibold" style={{ color: 'var(--bs-body-color)' }}>{item.nama_menu}</span>
                  <span className="badge bg-secondary">{item.jumlah}x</span>
                </div>
                <div className="text-muted small" style={{ whiteSpace: 'pre-line' }}>
                  Catatan: {item.catatan_item || '-'}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="d-flex gap-2">
        {order.status_pesanan === 'menunggu' && (
          <button className="btn btn-warning" onClick={() => handleUpdateStatus('diterima_dapur', 'Terima pesanan ini untuk diproses?')}>
            Terima Pesanan
          </button>
        )}
        {order.status_pesanan === 'diterima_dapur' && (
          <button className="btn btn-info" onClick={() => handleUpdateStatus('diproses', 'Mulai proses masak pesanan ini?')}>
            Proses Pesanan
          </button>
        )}
        {order.status_pesanan === 'diproses' && (
          <button className="btn btn-success" onClick={() => handleUpdateStatus('siap_saji', 'Pesanan ini sudah selesai dimasak dan siap disajikan?')}>
            Siap Saji
          </button>
        )}
      </div>
    </div>
  );
}
