import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function PelayanOrdersEdit() {
  const { id } = useParams();
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
  const [originalItems, setOriginalItems] = useState([]); // to revert stock if changed

  useEffect(() => {
    fetchData();
  }, [id]);

  const fetchData = async () => {
    try {
      const { data: menuData, error: menuError } = await supabase
        .from('menus')
        .select('*')
        .order('nama_menu');
      if (menuError) throw menuError;

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

      setMenus(menuData || []);
      
      setNomorMeja(orderData.nomor_meja || '');
      setAtasNama(orderData.atas_nama || '');
      setNamaPelanggan(orderData.nama_pelanggan || '');
      setTipePesanan(orderData.tipe_pesanan || 'dine_in');
      setCatatan(orderData.catatan || '');

      const initialQtys = {};
      itemsData.forEach(item => {
        initialQtys[item.menu_id] = item.jumlah;
      });
      setQuantities(initialQtys);
      setOriginalItems(itemsData || []);

    } catch (error) {
      console.error('Error fetching data:', error.message);
      alert('Gagal memuat data');
      navigate('/pelayan/orders');
    } finally {
      setLoading(false);
    }
  };

  const handleQtyChange = (menuId, maxStok, val) => {
    let newQty = parseInt(val) || 0;
    if (newQty < 0) newQty = 0;
    
    // Original qty + current stok is the absolute max available
    const origItem = originalItems.find(i => i.menu_id === menuId);
    const origQty = origItem ? origItem.jumlah : 0;
    const absoluteMax = maxStok + origQty;

    if (newQty > absoluteMax) {
      alert(`Jumlah melebihi stok yang tersedia (${absoluteMax}).`);
      newQty = absoluteMax;
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

      const { error: orderError } = await supabase
        .from('orders')
        .update({
          nama_pelanggan: namaPelanggan || null,
          atas_nama: atasNama,
          tipe_pesanan: tipePesanan,
          nomor_meja: nomorMeja,
          total_harga,
          catatan
        })
        .eq('id', id);

      if (orderError) throw orderError;

      // Restore old stock
      for (const item of originalItems) {
        const menu = menus.find(m => m.id === item.menu_id);
        if (menu) {
          await supabase
            .from('menus')
            .update({ stok: menu.stok + item.jumlah })
            .eq('id', item.menu_id);
        }
      }

      // Delete old items
      await supabase.from('order_items').delete().eq('order_id', id);

      // Insert new items
      const newOrderItems = selectedItems.map(item => ({
        ...item,
        order_id: id
      }));

      const { error: itemsError } = await supabase
        .from('order_items')
        .insert(newOrderItems);

      if (itemsError) throw itemsError;

      // Deduct new stock
      // Note: In a real app we'd fetch fresh stock or use a Postgres function to avoid race conditions
      for (const item of selectedItems) {
        const menu = menus.find(m => m.id === item.menu_id);
        if (menu) {
          const orig = originalItems.find(i => i.menu_id === menu.id);
          const currentFreshStok = menu.stok + (orig ? orig.jumlah : 0);
          await supabase
            .from('menus')
            .update({ stok: Math.max(0, currentFreshStok - item.jumlah) })
            .eq('id', item.menu_id);
        }
      }

      alert('Pesanan berhasil diubah!');
      navigate('/pelayan/orders');
    } catch (error) {
      console.error('Error updating order:', error.message);
      alert('Gagal mengubah pesanan');
    } finally {
      setSaving(false);
    }
  };

  const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Ubah Pesanan</h2>
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
                    <th>Stok (Real)</th>
                    <th width="120">Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  {menus.map(menu => {
                    const orig = originalItems.find(i => i.menu_id === menu.id);
                    const origQty = orig ? orig.jumlah : 0;
                    const maxStok = menu.stok; // DB stock
                    const totalStokAvailable = maxStok + origQty;

                    return (
                    <tr key={menu.id}>
                      <td><span className="fw-semibold">{menu.nama_menu}</span></td>
                      <td>Rp {formatRupiah(menu.harga)}</td>
                      <td>
                        {totalStokAvailable > 0 ? <span className="badge bg-success">{totalStokAvailable}</span> : <span className="badge bg-danger">Habis</span>}
                      </td>
                      <td>
                        <input 
                          type="number" 
                          className="form-control" 
                          min="0" 
                          max={totalStokAvailable} 
                          value={quantities[menu.id] || ''}
                          onChange={e => handleQtyChange(menu.id, maxStok, e.target.value)}
                          disabled={totalStokAvailable === 0 && (quantities[menu.id] || 0) === 0}
                          placeholder={totalStokAvailable === 0 ? "Habis" : "0"}
                        />
                      </td>
                    </tr>
                  )})}
                </tbody>
              </table>
            </div>

            <div className="d-md-none">
              <div className="row g-2">
                {menus.map(menu => {
                  const orig = originalItems.find(i => i.menu_id === menu.id);
                  const origQty = orig ? orig.jumlah : 0;
                  const totalStokAvailable = menu.stok + origQty;

                  return (
                  <div className="col-12" key={menu.id}>
                    <div className="card shadow-sm p-3" style={{ backgroundColor: 'var(--bs-card-bg)', border: '1px solid var(--border-color) !important' }}>
                      <div className="d-flex justify-content-between align-items-center mb-2">
                        <span className="fw-bold text-primary">{menu.nama_menu}</span>
                        {totalStokAvailable > 0 ? <span className="badge bg-secondary">Stok: {totalStokAvailable}</span> : <span className="badge bg-danger">Habis</span>}
                      </div>
                      <div className="d-flex justify-content-between align-items-center">
                        <span className="text-success fw-semibold">Rp {formatRupiah(menu.harga)}</span>
                        <div style={{ width: '100px' }}>
                          <input 
                            type="number" 
                            className="form-control form-control-sm text-center" 
                            min="0" 
                            max={totalStokAvailable} 
                            value={quantities[menu.id] || ''}
                            onChange={e => handleQtyChange(menu.id, menu.stok, e.target.value)}
                            disabled={totalStokAvailable === 0 && (quantities[menu.id] || 0) === 0}
                            placeholder="0"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                )})}
              </div>
            </div>

            <button className="btn btn-primary mt-3 w-100" type="submit" disabled={saving}>
              {saving ? 'Menyimpan...' : 'Simpan Perubahan'}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
