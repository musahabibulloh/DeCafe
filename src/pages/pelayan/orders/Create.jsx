import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function PelayanOrdersCreate() {
  const navigate = useNavigate();
  const [menus, setMenus] = useState([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  const [nomorMeja, setNomorMeja] = useState('');
  const [atasNama, setAtasNama] = useState('');
  const [namaPelanggan, setNamaPelanggan] = useState('');
  const [tipePesanan, setTipePesanan] = useState('dine_in');
  const [catatan, setCatatan] = useState('');
  
  const [quantities, setQuantities] = useState({});

  useEffect(() => {
    fetchMenus();
  }, []);

  const fetchMenus = async () => {
    try {
      const { data, error } = await supabase
        .from('menus')
        .select('*')
        .eq('status', 'tersedia')
        .order('nama_menu');

      if (error) throw error;
      setMenus(data || []);
    } catch (error) {
      console.error('Error fetching menus:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleQtyChange = (menuId, maxStok, val) => {
    let newQty = parseInt(val) || 0;
    if (newQty < 0) newQty = 0;
    if (newQty > maxStok) {
      alert(`Jumlah melebihi stok yang tersedia (${maxStok}).`);
      newQty = maxStok;
    }
    setQuantities(prev => ({ ...prev, [menuId]: newQty }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!atasNama) return alert('Atas Nama wajib diisi');
    
    const selectedItems = menus.filter(m => quantities[m.id] > 0).map(m => ({
      menu_id: m.id,
      nama_menu: m.nama_menu,
      harga: m.harga,
      jumlah: quantities[m.id],
      subtotal: m.harga * quantities[m.id]
    }));

    if (selectedItems.length === 0) {
      return alert('Pilih minimal 1 menu');
    }

    try {
      setSaving(true);
      
      const total_harga = selectedItems.reduce((sum, item) => sum + item.subtotal, 0);
      const kode_pesanan = 'ORD-' + Date.now().toString().slice(-6);

      // Get pelayan ID
      const { data: userData } = await supabase.auth.getUser();
      const pelayan_id = userData?.user?.id || null;

      const { data: order, error: orderError } = await supabase
        .from('orders')
        .insert([{
          kode_pesanan,
          user_id: pelayan_id,
          nama_pelanggan: namaPelanggan || null,
          atas_nama: atasNama,
          tipe_pesanan: tipePesanan,
          nomor_meja: nomorMeja,
          total_harga,
          status_pesanan: 'menunggu',
          status_pembayaran: 'belum_bayar',
          catatan
        }])
        .select()
        .single();

      if (orderError) throw orderError;

      const orderItems = selectedItems.map(item => ({
        ...item,
        order_id: order.id
      }));

      const { error: itemsError } = await supabase
        .from('order_items')
        .insert(orderItems);

      if (itemsError) throw itemsError;

      // Deduct stock
      for (const item of selectedItems) {
        const menu = menus.find(m => m.id === item.menu_id);
        if (menu && menu.stok > 0) {
          await supabase
            .from('menus')
            .update({ stok: Math.max(0, menu.stok - item.jumlah) })
            .eq('id', item.menu_id);
        }
      }

      alert('Pesanan berhasil dibuat!');
      navigate('/pelayan/orders');
    } catch (error) {
      console.error('Error creating order:', error.message);
      alert('Gagal membuat pesanan');
    } finally {
      setSaving(false);
    }
  };

  const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Buat Pesanan</h2>
        <Link to="/pelayan/orders" className="btn btn-outline-secondary">Kembali</Link>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          <form onSubmit={handleSubmit}>
            <div className="row g-3">
              <div className="col-md-6">
                <label className="form-label">Nomor Meja</label>
                <input type="text" className="form-control" value={nomorMeja} onChange={e => setNomorMeja(e.target.value)} required placeholder="Contoh: 05" />
              </div>
              <div className="col-md-6">
                <label className="form-label">Atas Nama (Pemesan) <span className="text-danger">*</span></label>
                <input type="text" className="form-control" value={atasNama} onChange={e => setAtasNama(e.target.value)} placeholder="Nama pemesan" required />
              </div>
              <div className="col-md-6">
                <label className="form-label">Nama Pelanggan (User Akun)</label>
                <input type="text" className="form-control" value={namaPelanggan} onChange={e => setNamaPelanggan(e.target.value)} placeholder="Nama pelanggan (opsional)" />
              </div>
              <div className="col-md-6">
                <label className="form-label">Tipe Pesanan <span className="text-danger">*</span></label>
                <div className="d-flex gap-3 pt-2">
                  <div className="form-check">
                    <input className="form-check-input" type="radio" name="tipe_pesanan" id="tipe_dine_in" value="dine_in" checked={tipePesanan === 'dine_in'} onChange={e => setTipePesanan(e.target.value)} />
                    <label className="form-check-label" htmlFor="tipe_dine_in">Dine In (Makan di Tempat)</label>
                  </div>
                  <div className="form-check">
                    <input className="form-check-input" type="radio" name="tipe_pesanan" id="tipe_take_away" value="take_away" checked={tipePesanan === 'take_away'} onChange={e => setTipePesanan(e.target.value)} />
                    <label className="form-check-label" htmlFor="tipe_take_away">Take Away (Bungkus)</label>
                  </div>
                </div>
              </div>
              <div className="col-12">
                <label className="form-label">Catatan Pesanan</label>
                <textarea className="form-control" rows="2" value={catatan} onChange={e => setCatatan(e.target.value)} placeholder="Catatan khusus, misal: tidak pedas, dll."></textarea>
              </div>
            </div>

            <hr className="my-4" />
            <h5 className="mb-3">Pilih Menu & Jumlah</h5>

            <div className="d-none d-md-block table-responsive">
              <table className="table table-bordered align-middle">
                <thead>
                  <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th width="120">Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  {menus.map(menu => (
                    <tr key={menu.id}>
                      <td><span className="fw-semibold">{menu.nama_menu}</span></td>
                      <td>Rp {formatRupiah(menu.harga)}</td>
                      <td>
                        {menu.stok > 0 ? <span className="badge bg-success">{menu.stok}</span> : <span className="badge bg-danger">Habis</span>}
                      </td>
                      <td>
                        <input 
                          type="number" 
                          className="form-control" 
                          min="0" 
                          max={menu.stok} 
                          value={quantities[menu.id] || ''}
                          onChange={e => handleQtyChange(menu.id, menu.stok, e.target.value)}
                          disabled={menu.stok === 0}
                          placeholder={menu.stok === 0 ? "Habis" : "0"}
                        />
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="d-md-none">
              <div className="row g-2">
                {menus.map(menu => (
                  <div className="col-12" key={menu.id}>
                    <div className="card shadow-sm p-3" style={{ backgroundColor: 'var(--bs-card-bg)', border: '1px solid var(--border-color) !important' }}>
                      <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="fw-bold text-primary">{menu.nama_menu}</span>
                        {menu.stok > 0 ? <span className="badge bg-secondary">Stok: {menu.stok}</span> : <span className="badge bg-danger">Habis</span>}
                      </div>
                      <div className="d-flex justify-content-between align-items-center">
                        <span className="text-success fw-semibold">Rp {formatRupiah(menu.harga)}</span>
                        <div style={{ width: '100px' }}>
                          <input 
                            type="number" 
                            className="form-control form-control-sm text-center" 
                            min="0" 
                            max={menu.stok} 
                            value={quantities[menu.id] || ''}
                            onChange={e => handleQtyChange(menu.id, menu.stok, e.target.value)}
                            disabled={menu.stok === 0}
                            placeholder="0"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <button className="btn btn-primary mt-3 w-100" type="submit" disabled={saving}>
              {saving ? 'Menyimpan...' : 'Simpan Pesanan'}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
