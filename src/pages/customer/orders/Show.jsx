import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function CustomerOrdersShow() {
  const { id } = useParams();
  const [order, setOrder] = useState(null);
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [paymentMethod, setPaymentMethod] = useState('qris');
  const [processing, setProcessing] = useState(false);
  const [showQris, setShowQris] = useState(false);
  const [file, setFile] = useState(null);
  const [uploading, setUploading] = useState(false);

  useEffect(() => {
    fetchOrderDetails();
  }, [id]);

  // Optionally poll if waiting for confirmation
  useEffect(() => {
    let interval;
    if (order && order.status_pembayaran === 'menunggu_konfirmasi') {
      interval = setInterval(() => {
        checkStatus();
      }, 3000);
    }
    return () => {
      if (interval) clearInterval(interval);
    };
  }, [order]);

  const checkStatus = async () => {
    try {
      const { data, error } = await supabase
        .from('orders')
        .select('status_pembayaran')
        .eq('id', id)
        .single();
      
      if (error) throw error;
      if (data.status_pembayaran === 'lunas') {
        fetchOrderDetails(); // Refresh to show success
      }
    } catch (e) {
      console.error(e);
    }
  };

  const fetchOrderDetails = async () => {
    try {
      const { data: orderData, error: orderError } = await supabase
        .from('orders')
        .select('*')
        .eq('id', id)
        .single();

      if (orderError) throw orderError;
      setOrder(orderData);

      const { data: itemsData, error: itemsError } = await supabase
        .from('order_items')
        .select('*')
        .eq('order_id', id);

      if (itemsError) throw itemsError;
      setItems(itemsData || []);
    } catch (e) {
      console.error('Error fetching order details:', e.message);
      alert('Gagal memuat detail pesanan');
    } finally {
      setLoading(false);
    }
  };

  const formatRupiah = (num) => new Intl.NumberFormat('id-ID').format(num || 0);
  const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()} ${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}`;
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

  const handlePayment = async (e) => {
    e.preventDefault();
    setProcessing(true);
    
    try {
      if (paymentMethod === 'cash') {
        alert(`Silakan bayar tunai ke Kasir dan sebutkan Kode Pesanan: ${order.kode_pesanan}`);
      } else if (paymentMethod === 'qris') {
        setShowQris(true);
      }
    } catch (error) {
      console.error('Payment error:', error.message);
      alert('Gagal memproses pembayaran: ' + error.message);
    } finally {
      setProcessing(false);
    }
  };

  const handleUpload = async (e) => {
    e.preventDefault();
    if (!file) {
      alert("Pilih file bukti pembayaran terlebih dahulu");
      return;
    }
    
    setUploading(true);
    try {
      const fileExt = file.name.split('.').pop();
      const fileName = `bukti_${order.kode_pesanan}_${Date.now()}.${fileExt}`;

      // Upload ke Supabase Storage (bucket: receipts)
      const { error: uploadError } = await supabase.storage
        .from('receipts')
        .upload(fileName, file);

      if (uploadError) throw uploadError;

      // Ambil Public URL gambar yang baru diupload
      const { data: publicUrlData } = supabase.storage
        .from('receipts')
        .getPublicUrl(fileName);

      const actualUrl = publicUrlData.publicUrl;

      const { error: updateError } = await supabase
        .from('orders')
        .update({ 
           status_pembayaran: 'menunggu_konfirmasi',
           bukti_pembayaran: actualUrl
        })
        .eq('id', id);

      if (updateError) throw updateError;
      
      alert('Bukti pembayaran berhasil diunggah!');
      setShowQris(false);
      fetchOrderDetails();
    } catch (err) {
      console.error(err);
      alert('Gagal mengunggah bukti pembayaran: ' + err.message);
    } finally {
      setUploading(false);
    }
  };

  if (loading) {
    return <div className="container-fluid py-5 text-center"><div className="spinner-border text-primary"></div></div>;
  }

  if (!order) {
    return <div className="container-fluid py-5 text-center">Pesanan tidak ditemukan.</div>;
  }

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3">Detail Pesanan</h1>
        <Link to="/customer/orders" className="btn btn-secondary">Kembali</Link>
      </div>

      <div className="row">
        <div className="col-md-8">
          <div className="card border-0 shadow-sm mb-4" style={{ backgroundColor: 'var(--bg-card)', borderColor: 'var(--border-color)' }}>
            <div className="card-header bg-primary text-white">
              <h5 className="mb-0">Informasi Pesanan</h5>
            </div>
            <div className="card-body">
              <div className="row">
                <div className="col-md-6">
                  <p><strong>Kode Pesanan:</strong> <span style={{ color: 'var(--text-main)' }}>{order.kode_pesanan}</span></p>
                  <p><strong>Tanggal:</strong> <span style={{ color: 'var(--text-main)' }}>{formatDate(order.created_at)}</span></p>
                  <p><strong>Atas Nama:</strong> <span style={{ color: 'var(--text-main)' }}>{order.atas_nama || '-'}</span></p>
                  <p><strong>Nomor Meja:</strong> <span style={{ color: 'var(--text-main)' }}>{order.nomor_meja || '-'}</span></p>
                  <p>
                    <strong>Tipe Pesanan:</strong>{' '}
                    {order.tipe_pesanan === 'take_away' ? (
                      <span className="badge bg-info text-dark"><i className="bi bi-bag me-1"></i>Take Away (Bungkus)</span>
                    ) : (
                      <span className="badge bg-primary"><i className="bi bi-shop me-1"></i>Dine In (Makan di Tempat)</span>
                    )}
                  </p>
                </div>
                <div className="col-md-6">
                  <p><strong>Status Pesanan:</strong> {renderStatusPesanan(order.status_pesanan)}</p>
                  <p>
                    <strong>Status Pembayaran:</strong>{' '}
                    {order.status_pembayaran === 'lunas' ? (
                      <span className="badge bg-success">Lunas</span>
                    ) : order.status_pembayaran === 'menunggu_konfirmasi' ? (
                      <span className="badge bg-warning text-dark">Menunggu Konfirmasi Kasir</span>
                    ) : (
                      <span className="badge bg-danger">Belum Bayar</span>
                    )}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div className="card border-0 shadow-sm" style={{ backgroundColor: 'var(--bg-card)' }}>
            <div className="card-header bg-success text-white">
              <h5 className="mb-0">Item Pesanan</h5>
            </div>
            <div className="card-body">
              {/* Desktop Table */}
              <table className="table d-none d-md-table align-middle">
                <thead>
                  <tr>
                    <th style={{ color: 'var(--text-main)' }}>Menu</th>
                    <th style={{ color: 'var(--text-main)' }}>Harga</th>
                    <th style={{ color: 'var(--text-main)' }}>Jumlah</th>
                    <th style={{ color: 'var(--text-main)' }}>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  {items.map(item => (
                    <tr key={item.id}>
                      <td style={{ color: 'var(--text-main)' }}>
                        {item.nama_menu}
                        {item.catatan_item && (
                          <div className="small text-muted font-monospace mt-1" style={{ fontSize: '0.8rem', whiteSpace: 'pre-line' }}>
                            <i className="bi bi-gear-wide-connected"></i> {item.catatan_item}
                          </div>
                        )}
                      </td>
                      <td style={{ color: 'var(--text-main)' }}>Rp {formatRupiah(item.harga)}</td>
                      <td style={{ color: 'var(--text-main)' }}>{item.jumlah}</td>
                      <td style={{ color: 'var(--text-main)' }}>Rp {formatRupiah(item.subtotal)}</td>
                    </tr>
                  ))}
                </tbody>
                <tfoot>
                  <tr>
                    <th colSpan="3" className="text-end" style={{ color: 'var(--text-main)' }}>Total:</th>
                    <th style={{ color: 'var(--text-main)' }}>Rp {formatRupiah(order.total_harga)}</th>
                  </tr>
                </tfoot>
              </table>

              {/* Mobile List */}
              <div className="d-md-none list-group list-group-flush">
                {items.map(item => (
                  <div key={item.id} className="list-group-item px-0 py-2 border-0 border-bottom bg-transparent">
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
        </div>

        <div className="col-md-4 mt-4 mt-md-0">
          <div className="card border-0 shadow-sm" style={{ backgroundColor: 'var(--bg-card)' }}>
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Pembayaran</h5>
            </div>
            <div className="card-body text-center">
              {showQris ? (
                <div>
                  <h5 className="fw-bold mb-2">Pembayaran QRIS</h5>
                  <p className="text-muted small">Silakan scan QRIS di bawah ini dan unggah bukti transfer Anda</p>
                  
                  <div className="bg-white p-2 rounded-4 mx-auto mb-3 shadow-sm" style={{ maxWidth: '280px', border: '1px solid #ddd' }}>
                    <img 
                      src="/images/qris.jpeg" 
                      alt="QRIS Cak Win" 
                      className="img-fluid rounded" 
                      style={{ width: '100%', height: 'auto', display: 'block' }} 
                    />
                  </div>
                  <h4 className="fw-bold text-success mb-4">Rp {formatRupiah(order.total_harga)}</h4>

                  <form onSubmit={handleUpload} className="text-start">
                    <div className="mb-3">
                      <label className="form-label fw-semibold small" style={{ color: 'var(--text-main)' }}>Unggah Bukti Pembayaran</label>
                      <input 
                        className="form-control" 
                        type="file" 
                        accept="image/*" 
                        onChange={(e) => setFile(e.target.files[0])}
                        required 
                      />
                    </div>
                    <button type="submit" className="btn btn-success w-100 fw-bold mb-2" disabled={uploading}>
                      {uploading ? 'Mengunggah...' : <><i className="bi bi-cloud-upload me-1"></i> Kirim Bukti Pembayaran</>}
                    </button>
                    <button type="button" className="btn btn-outline-secondary w-100" onClick={() => setShowQris(false)} disabled={uploading}>
                      Batal
                    </button>
                  </form>
                </div>
              ) : order.status_pembayaran === 'lunas' ? (
                <div className="alert alert-success">
                  <h4>LUNAS</h4>
                  <p className="mb-0">Pesanan sudah dibayar</p>
                </div>
              ) : order.status_pembayaran === 'menunggu_konfirmasi' ? (
                <div className="alert alert-info">
                  <h5 className="fw-bold"><i className="bi bi-hourglass-split me-1"></i> MENUNGGU VERIFIKASI</h5>
                  <p className="mb-0 small">Menunggu kasir memverifikasi pembayaran Anda.</p>
                </div>
              ) : order.status_pesanan !== 'dibatalkan' ? (
                <>
                  <div className="alert alert-warning">
                    <h4>BELUM BAYAR</h4>
                    <p className="mb-0">Total: Rp {formatRupiah(order.total_harga)}</p>
                  </div>
                  
                  <form onSubmit={handlePayment}>
                    <div className="mb-4 text-start">
                      <label className="form-label fw-bold d-block mb-3" style={{ color: 'var(--text-main)' }}>Pilih Metode Pembayaran</label>
                      <div className="row g-3">
                        <div className="col-6">
                          <input 
                            type="radio" 
                            className="btn-check" 
                            name="metode" 
                            id="pay_qris" 
                            value="qris" 
                            checked={paymentMethod === 'qris'}
                            onChange={(e) => setPaymentMethod(e.target.value)}
                          />
                          <label className="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2" htmlFor="pay_qris" style={{ borderWidth: '2px' }}>
                            <i className="bi bi-qr-code-scan fs-3"></i>
                            <span className="fw-bold">QRIS</span>
                          </label>
                        </div>
                        <div className="col-6">
                          <input 
                            type="radio" 
                            className="btn-check" 
                            name="metode" 
                            id="pay_cash" 
                            value="cash"
                            checked={paymentMethod === 'cash'}
                            onChange={(e) => setPaymentMethod(e.target.value)}
                          />
                          <label className="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2" htmlFor="pay_cash" style={{ borderWidth: '2px' }}>
                            <i className="bi bi-cash-coin fs-3"></i>
                            <span className="fw-bold">Tunai (Kasir)</span>
                          </label>
                        </div>
                      </div>
                    </div>

                    {paymentMethod === 'cash' && (
                      <div className="alert alert-info text-start small" style={{ borderLeft: '4px solid #0dcaf0', background: 'rgba(13, 202, 240, 0.1)', color: '#0dcaf0' }}>
                        <i className="bi bi-info-circle-fill me-1"></i> Silakan informasikan Kode Pesanan <strong>{order.kode_pesanan}</strong> ke Kasir untuk membayar secara tunai.
                      </div>
                    )}
                    
                    <button type="submit" className={`btn btn-lg w-100 mt-2 ${paymentMethod === 'cash' ? 'btn-primary' : 'btn-success'}`} disabled={processing}>
                      {processing ? 'Memproses...' : paymentMethod === 'cash' ? (
                        <><i className="bi bi-cash-coin me-1"></i> Konfirmasi Pembayaran Tunai</>
                      ) : (
                        <>Lanjutkan Pembayaran <i className="bi bi-arrow-right ms-1"></i></>
                      )}
                    </button>
                  </form>
                </>
              ) : (
                <div className="alert alert-danger">
                  <h4>DIBATALKAN</h4>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
