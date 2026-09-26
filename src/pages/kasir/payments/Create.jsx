import React, { useState, useEffect } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function KasirPaymentsCreate() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  
  const [nomorMeja, setNomorMeja] = useState('');
  const [metodePembayaran, setMetodePembayaran] = useState('');
  const [uangDiterima, setUangDiterima] = useState('');
  
  const [showModal, setShowModal] = useState(false);

  useEffect(() => {
    fetchOrderDetails();
  }, [id]);

  const fetchOrderDetails = async () => {
    try {
      const { data, error } = await supabase
        .from('orders')
        .select('*')
        .eq('id', id)
        .single();
        
      if (error) throw error;
      setOrder(data);
      setNomorMeja(data.nomor_meja === '-' ? '' : (data.nomor_meja || ''));
      if (data.status_pembayaran === 'menunggu_konfirmasi') {
        setMetodePembayaran('qris');
      }
    } catch (error) {
      console.error('Error fetching order details:', error.message);
      alert('Gagal memuat detail pesanan');
      navigate('/kasir/orders');
    } finally {
      setLoading(false);
    }
  };

  const handleOpenModal = (e) => {
    e.preventDefault();
    if (!nomorMeja.trim()) return alert('Masukkan nomor meja.');
    if (!metodePembayaran) return alert('Pilih metode pembayaran.');

    if (metodePembayaran === 'tunai') {
      const uang = parseInt(uangDiterima) || 0;
      if (uang < order.total_harga) {
        return alert('Uang diterima kurang dari total tagihan.');
      }
    }
    
    setShowModal(true);
  };

  const handleConfirmSubmit = async () => {
    try {
      setSaving(true);
      
      const totalHarga = order.total_harga;
      const uang = metodePembayaran === 'tunai' ? parseInt(uangDiterima) : totalHarga;
      const kembalian = metodePembayaran === 'tunai' ? (uang - totalHarga) : 0;
      
      // Update order
      const { error: updateError } = await supabase
        .from('orders')
        .update({ 
          nomor_meja: nomorMeja,
          status_pembayaran: 'lunas'
        })
        .eq('id', id);

      if (updateError) throw updateError;
      
      // Generate a random payment code
      const kode_pembayaran = 'PAY-' + Date.now().toString().slice(-6);

      // Insert payment
      const { error: paymentError } = await supabase
        .from('payments')
        .insert([{
          order_id: id,
          kode_pembayaran: kode_pembayaran,
          metode_pembayaran: metodePembayaran,
          total_bayar: totalHarga,
          uang_diterima: uang,
          kembalian: kembalian,
          status: 'lunas',
          paid_at: new Date().toISOString()
        }]);

      if (paymentError) throw paymentError;

      alert('Pembayaran berhasil!');
      navigate(`/kasir/orders/${id}`);
    } catch (error) {
      console.error('Error confirming payment:', error.message);
      alert('Gagal memproses pembayaran');
    } finally {
      setSaving(false);
      setShowModal(false);
    }
  };

  const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;
  if (!order) return <div className="text-center py-5">Pesanan tidak ditemukan</div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Pembayaran Pesanan</h2>
        <Link to={`/kasir/orders/${id}`} className="btn btn-outline-secondary">Kembali</Link>
      </div>

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <p><strong>Kode Pesanan:</strong> {order.kode_pesanan}</p>
          <p><strong>Nomor Meja:</strong> {order.nomor_meja}</p>
          <p><strong>Total Bayar:</strong> Rp {formatRupiah(order.total_harga)}</p>

          {order.bukti_pembayaran && (
            <div className="mt-3 pt-3 border-top">
              <h6 className="fw-bold mb-2 text-warning"><i className="bi bi-receipt"></i> Bukti Pembayaran QRIS:</h6>
              <div style={{ maxWidth: '250px' }}>
                <a href={order.bukti_pembayaran} target="_blank" rel="noreferrer" className="d-block border rounded p-2 text-center bg-light text-decoration-none">
                  <img src={order.bukti_pembayaran} className="img-fluid rounded" style={{ maxHeight: '180px' }} alt="Bukti Pembayaran" />
                  <span className="d-block small text-muted mt-2"><i className="bi bi-zoom-in"></i> Klik untuk memperbesar</span>
                </a>
              </div>
            </div>
          )}
        </div>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          <form onSubmit={handleOpenModal}>
            <div className="mb-3">
              <label className="form-label">Nomor Meja</label>
              <input 
                type="text" 
                className="form-control" 
                value={nomorMeja} 
                onChange={(e) => setNomorMeja(e.target.value)}
                required 
                placeholder="Masukkan nomor meja (misal: 05)" 
              />
            </div>
            <div className="mb-3">
              <label className="form-label">Metode Pembayaran</label>
              <select 
                className="form-select" 
                value={metodePembayaran} 
                onChange={(e) => setMetodePembayaran(e.target.value)}
                required
              >
                <option value="">Pilih Metode</option>
                <option value="tunai">Tunai</option>
                <option value="qris">QRIS</option>
                <option value="transfer">Transfer</option>
                <option value="ewallet">E-Wallet</option>
              </select>
            </div>
            {metodePembayaran === 'tunai' && (
              <div className="mb-3">
                <label className="form-label">Uang Diterima (Tunai)</label>
                <input 
                  type="number" 
                  className="form-control" 
                  value={uangDiterima} 
                  onChange={(e) => setUangDiterima(e.target.value)}
                  min="0"
                  required
                />
              </div>
            )}
            <button type="submit" className="btn btn-success w-100">Konfirmasi Pembayaran</button>
          </form>
        </div>
      </div>

      {/* Modal Konfirmasi Pembayaran */}
      {showModal && (
        <>
          <div className="modal-backdrop fade show"></div>
          <div className="modal fade show d-block" tabIndex="-1">
            <div className="modal-dialog modal-dialog-centered">
              <div className="modal-content" style={{ backgroundColor: 'var(--bs-card-bg)', border: '1px solid rgba(var(--bs-body-color-rgb), 0.1)', borderRadius: '16px' }}>
                <div className="modal-header border-bottom">
                  <h5 className="modal-title">Konfirmasi Transaksi</h5>
                  <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
                </div>
                <div className="modal-body">
                  <p className="text-muted mb-3">Pastikan rincian pembayaran sudah sesuai.</p>
                  <div className="d-flex flex-column gap-2 mb-3">
                    <div className="d-flex justify-content-between">
                      <span className="text-muted">Total Tagihan:</span>
                      <strong>Rp {formatRupiah(order.total_harga)}</strong>
                    </div>
                    <div className="d-flex justify-content-between">
                      <span className="text-muted">Metode Pembayaran:</span>
                      <strong className="text-uppercase">{metodePembayaran}</strong>
                    </div>
                    {metodePembayaran === 'tunai' && (
                      <>
                        <div className="d-flex justify-content-between">
                          <span className="text-muted">Uang Diterima:</span>
                          <strong>Rp {formatRupiah(uangDiterima || 0)}</strong>
                        </div>
                        <div className="d-flex justify-content-between border-top pt-2">
                          <span className="fw-bold">Kembalian:</span>
                          <strong className="text-success h5 mb-0">Rp {formatRupiah((uangDiterima || 0) - order.total_harga)}</strong>
                        </div>
                      </>
                    )}
                  </div>
                </div>
                <div className="modal-footer border-top">
                  <button type="button" className="btn btn-outline-secondary" onClick={() => setShowModal(false)}>Batal</button>
                  <button type="button" className="btn btn-success" onClick={handleConfirmSubmit} disabled={saving}>
                    {saving ? 'Menyimpan...' : 'Konfirmasi & Simpan'}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  );
}
