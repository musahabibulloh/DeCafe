import React, { useState, useEffect } from 'react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function KasirPaymentsReceipt() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [payment, setPayment] = useState(null);
  const [order, setOrder] = useState(null);
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchReceipt();
  }, [id]);

  const fetchReceipt = async () => {
    try {
      // id here might be order_id or payment_id depending on how we route.
      // Let's assume the route is /kasir/orders/:id/receipt where id = order_id
      const { data: paymentData, error: paymentError } = await supabase
        .from('payments')
        .select('*')
        .eq('order_id', id)
        .single();
        
      if (paymentError) throw paymentError;
      
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
      
      setPayment(paymentData);
      setOrder(orderData);
      setItems(itemsData || []);
    } catch (error) {
      console.error('Error fetching receipt:', error.message);
      alert('Struk tidak ditemukan');
      navigate('/kasir/orders');
    } finally {
      setLoading(false);
    }
  };

  const handlePrint = () => {
    window.print();
  };

  const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;
  if (!payment || !order) return <div className="text-center py-5">Data struk tidak valid</div>;

  return (
    <div className="container py-4 d-flex justify-content-center">
      <div className="card shadow" style={{ maxWidth: '400px', width: '100%', borderRadius: '12px', border: '1px solid var(--border-color)', backgroundColor: '#fff', color: '#000' }}>
        <div className="card-body p-4" id="print-area">
          <div className="text-center mb-4">
            <h3 className="fw-bold mb-0">NASI BAKAR CAK WIN</h3>
            <p className="text-muted small mb-0">Jl. Contoh Alamat No. 123, Kota</p>
            <p className="text-muted small mb-0">Telp: 0812-3456-7890</p>
          </div>
          
          <div className="border-top border-bottom py-2 mb-3 border-dashed" style={{ borderStyle: 'dashed !important' }}>
            <div className="d-flex justify-content-between small">
              <span>Waktu: {new Date(payment.paid_at || payment.created_at).toLocaleString('id-ID')}</span>
              <span>Kasir: {payment.kasir_id ? 'Kasir' : 'Auto'}</span>
            </div>
            <div className="d-flex justify-content-between small">
              <span>No. Pesanan: {order.kode_pesanan}</span>
              <span>Meja: {order.nomor_meja || '-'}</span>
            </div>
            <div className="small">
              <span>Pelanggan: {order.atas_nama || order.nama_pelanggan || '-'}</span>
            </div>
          </div>
          
          <div className="mb-3">
            <table className="table table-borderless table-sm small mb-0">
              <tbody>
                {items.map(item => (
                  <tr key={item.id}>
                    <td className="ps-0" colSpan="3">
                      {item.nama_menu}
                      {item.catatan_item && (
                        <div className="text-muted" style={{ fontSize: '0.75rem' }}>* {item.catatan_item}</div>
                      )}
                    </td>
                  </tr>
                ))}
                {items.map(item => (
                  <tr key={'price_'+item.id}>
                    <td className="ps-0 pb-2">{item.jumlah}x</td>
                    <td className="text-end pb-2">{formatRupiah(item.harga)}</td>
                    <td className="text-end pe-0 pb-2">{formatRupiah(item.subtotal)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          
          <div className="border-top pt-2 mb-3 border-dashed" style={{ borderStyle: 'dashed !important' }}>
            <div className="d-flex justify-content-between mb-1">
              <strong>Total</strong>
              <strong>Rp {formatRupiah(payment.total_bayar)}</strong>
            </div>
            <div className="d-flex justify-content-between small text-muted">
              <span>Tunai / Bayar</span>
              <span>Rp {formatRupiah(payment.uang_diterima || payment.total_bayar)}</span>
            </div>
            <div className="d-flex justify-content-between small text-muted">
              <span>Kembalian</span>
              <span>Rp {formatRupiah(payment.kembalian || 0)}</span>
            </div>
            <div className="d-flex justify-content-between small text-muted mt-1">
              <span>Metode</span>
              <span className="text-uppercase">{payment.metode_pembayaran}</span>
            </div>
          </div>
          
          <div className="text-center mt-4">
            <p className="mb-1 small">Terima kasih atas kunjungan Anda!</p>
            <p className="mb-0 small fw-bold">SILAKAN DATANG KEMBALI</p>
          </div>
        </div>
      </div>
      
      {/* Hide buttons when printing */}
      <style dangerouslySetInnerHTML={{__html: `
        @media print {
          body * { visibility: hidden; }
          #print-area, #print-area * { visibility: visible; }
          #print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 0 !important; }
          .no-print { display: none !important; }
        }
        .border-dashed { border-style: dashed !important; border-color: #ccc !important; }
      `}} />
      
      <div className="position-fixed bottom-0 end-0 p-4 no-print d-flex gap-2">
        <Link to={`/kasir/orders/${id}`} className="btn btn-secondary rounded-circle shadow" style={{ width: '56px', height: '56px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
          <i className="bi bi-arrow-left fs-4"></i>
        </Link>
        <button onClick={handlePrint} className="btn btn-primary rounded-circle shadow" style={{ width: '56px', height: '56px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
          <i className="bi bi-printer fs-4"></i>
        </button>
      </div>
    </div>
  );
}
