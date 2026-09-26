import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function MenuEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    nama_menu: '',
    kategori: '',
    harga: '',
    stok: '0',
    status: 'tersedia',
    deskripsi: '',
    maksimal_lauk: '1',
    wajib_pilih_lauk: false,
    wajib_pilih_sambal: false
  });
  
  const [existingGambar, setExistingGambar] = useState(null);
  const [gambar, setGambar] = useState(null);
  
  const [options, setOptions] = useState([]);
  
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    fetchMenu();
  }, [id]);

  const fetchMenu = async () => {
    try {
      const { data, error } = await supabase
        .from('menus')
        .select('*')
        .eq('id', id)
        .single();
      
      if (error) throw error;
      if (data) {
        setFormData({
          nama_menu: data.nama_menu || '',
          kategori: data.kategori || '',
          harga: data.harga || '',
          stok: data.stok || '0',
          status: data.status || 'tersedia',
          deskripsi: data.deskripsi || '',
          maksimal_lauk: data.maksimal_lauk || '1',
          wajib_pilih_lauk: data.wajib_pilih_lauk || false,
          wajib_pilih_sambal: data.wajib_pilih_sambal || false
        });
        setExistingGambar(data.gambar);
      }

      const { data: optionsData, error: optionsError } = await supabase
        .from('menu_options')
        .select('*')
        .eq('menu_id', id)
        .order('sort_order', { ascending: true });
        
      if (optionsError) throw optionsError;
      if (optionsData) {
        setOptions(optionsData.map(opt => ({
          ...opt,
          id: opt.id, // we use DB id for existing ones, and Date.now() for new ones
          existing_gambar: opt.gambar,
          file: null
        })));
      }

    } catch (error) {
      console.error('Error fetching menu:', error.message);
      alert('Gagal memuat data menu');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value;
    setFormData({ ...formData, [e.target.name]: value });
  };

  const handleGambarChange = (e) => {
    if (e.target.files && e.target.files[0]) {
      setGambar(e.target.files[0]);
    }
  };

  const addOption = () => {
    setOptions([
      ...options,
      { id: Date.now(), nama_opsi: '', tipe: 'lauk', status: 'tersedia', file: null, existing_gambar: null }
    ]);
  };

  const removeOption = (id) => {
    setOptions(options.filter(opt => opt.id !== id));
  };

  const handleOptionChange = (id, field, value) => {
    setOptions(options.map(opt => (opt.id === id ? { ...opt, [field]: value } : opt)));
  };

  const handleOptionFileChange = (id, e) => {
    if (e.target.files && e.target.files[0]) {
      setOptions(options.map(opt => (opt.id === id ? { ...opt, file: e.target.files[0] } : opt)));
    }
  };

  const uploadImage = async (file) => {
    const fileExt = file.name.split('.').pop();
    const fileName = `${Math.random().toString(36).substring(2, 15)}_${Date.now()}.${fileExt}`;
    const filePath = `${fileName}`;

    const { error: uploadError } = await supabase.storage
      .from('menu_images')
      .upload(filePath, file);

    if (uploadError) {
      throw uploadError;
    }

    const { data } = supabase.storage
      .from('menu_images')
      .getPublicUrl(filePath);

    return data.publicUrl;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    
    try {
      let gambarUrl = existingGambar;
      if (gambar) {
        gambarUrl = await uploadImage(gambar);
      }

      const { error: updateError } = await supabase
        .from('menus')
        .update({
          nama_menu: formData.nama_menu,
          kategori: formData.kategori,
          harga: parseInt(formData.harga),
          stok: parseInt(formData.stok),
          status: formData.status,
          deskripsi: formData.deskripsi,
          maksimal_lauk: parseInt(formData.maksimal_lauk),
          wajib_pilih_lauk: formData.wajib_pilih_lauk,
          wajib_pilih_sambal: formData.wajib_pilih_sambal,
          gambar: gambarUrl
        })
        .eq('id', id);

      if (updateError) throw updateError;
      
      // Handle options
      // Simplest approach: Delete all existing options for this menu, then re-insert
      const { error: deleteOptionsError } = await supabase
        .from('menu_options')
        .delete()
        .eq('menu_id', id);

      if (deleteOptionsError) throw deleteOptionsError;

      if (options.length > 0) {
        for (let i = 0; i < options.length; i++) {
          let optGambarUrl = options[i].existing_gambar;
          if (options[i].file) {
            optGambarUrl = await uploadImage(options[i].file);
          }

          const { error: optError } = await supabase
            .from('menu_options')
            .insert([{
              menu_id: id,
              nama_opsi: options[i].nama_opsi,
              tipe: options[i].tipe,
              status: options[i].status,
              gambar: optGambarUrl,
              sort_order: i
            }]);

          if (optError) throw optError;
        }
      }

      alert('Menu berhasil diubah!');
      navigate('/admin/menus');
    } catch (error) {
      console.error('Error updating menu:', error.message || error);
      alert('Gagal mengubah menu: ' + (error.message || 'Error tidak diketahui'));
    } finally {
      setSaving(false);
    }
  };

  if (loading) return <div className="text-center py-4">Memuat data...</div>;

  return (
    <>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Ubah Menu</h2>
        <Link to="/admin/menus" className="btn btn-outline-secondary">Kembali</Link>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          <form onSubmit={handleSubmit}>
            <div className="row g-3">
              <div className="col-md-6">
                <label className="form-label">Nama Menu</label>
                <input type="text" name="nama_menu" className="form-control" value={formData.nama_menu} onChange={handleChange} required />
              </div>
              <div className="col-md-6">
                <label className="form-label">Kategori</label>
                <select name="kategori" className="form-select" value={formData.kategori} onChange={handleChange} required>
                  <option value="">Pilih Kategori</option>
                  <option value="makanan">Makanan</option>
                  <option value="minuman">Minuman</option>
                </select>
              </div>
              <div className="col-md-6">
                <label className="form-label">Harga (Rp)</label>
                <input type="number" name="harga" className="form-control" value={formData.harga} onChange={handleChange} required min="0" />
              </div>
              <div className="col-md-6">
                <label className="form-label">Stok</label>
                <input type="number" name="stok" className="form-control" value={formData.stok} onChange={handleChange} required min="0" />
              </div>
              <div className="col-md-6">
                <label className="form-label">Status</label>
                <select name="status" className="form-select" value={formData.status} onChange={handleChange} required>
                  <option value="tersedia">Tersedia</option>
                  <option value="habis">Habis</option>
                  <option value="nonaktif">Nonaktif</option>
                </select>
              </div>
              <div className="col-md-6">
                <label className="form-label">Gambar Menu</label>
                <input type="file" className="form-control" onChange={handleGambarChange} accept="image/*" />
                {existingGambar && (
                  <div className="mt-2">
                    <img src={existingGambar} alt="Menu" style={{ width: '80px', height: '80px', objectFit: 'cover' }} className="rounded border" />
                  </div>
                )}
              </div>
              <div className="col-12">
                <label className="form-label">Deskripsi</label>
                <textarea name="deskripsi" className="form-control" rows="3" value={formData.deskripsi} onChange={handleChange}></textarea>
              </div>
            </div>

            <hr className="my-4" />

            <div className="d-flex justify-content-between align-items-start gap-3 mb-3">
              <div>
                <h5 className="mb-1">Opsi Tambahan</h5>
                <p className="text-muted small mb-0">Tambahkan opsi yang boleh dipilih customer. Kosongkan jika menu tidak perlu tambahan.</p>
              </div>
              <button type="button" className="btn btn-outline-primary btn-sm" onClick={addOption}>
                <i className="bi bi-plus-lg"></i> Tambah Opsi
              </button>
            </div>

            <div className="table-responsive">
              <table className="table align-middle mb-2">
                <thead>
                  <tr>
                    <th>Nama Opsi</th>
                    <th style={{ width: '180px' }}>Jenis</th>
                    <th style={{ width: '160px' }}>Status</th>
                    <th style={{ width: '250px' }}>Gambar</th>
                    <th style={{ width: '70px' }}></th>
                  </tr>
                </thead>
                <tbody>
                  {options.length === 0 ? (
                    <tr>
                      <td colSpan="5" className="text-center text-muted py-3">Belum ada opsi tambahan.</td>
                    </tr>
                  ) : options.map((opt) => (
                    <tr key={opt.id}>
                      <td>
                        <input type="text" className="form-control" placeholder="Contoh: Ayam suwir" value={opt.nama_opsi} onChange={(e) => handleOptionChange(opt.id, 'nama_opsi', e.target.value)} required />
                      </td>
                      <td>
                        <select className="form-select" value={opt.tipe} onChange={(e) => handleOptionChange(opt.id, 'tipe', e.target.value)}>
                          <option value="lauk">Lauk</option>
                          <option value="sambal">Sambal</option>
                          <option value="ekstra_lauk">Ekstra Lauk</option>
                        </select>
                      </td>
                      <td>
                        <select className="form-select" value={opt.status} onChange={(e) => handleOptionChange(opt.id, 'status', e.target.value)}>
                          <option value="tersedia">Tersedia</option>
                          <option value="habis">Habis</option>
                        </select>
                      </td>
                      <td>
                        <div className="d-flex align-items-center gap-2">
                          {opt.existing_gambar && !opt.file && (
                            <img src={opt.existing_gambar} alt="Opt" style={{ width: '38px', height: '38px', objectFit: 'cover' }} className="rounded border" />
                          )}
                          <input type="file" className="form-control form-control-sm" accept="image/*" onChange={(e) => handleOptionFileChange(opt.id, e)} />
                        </div>
                      </td>
                      <td className="text-end">
                        <button type="button" className="btn btn-outline-danger btn-sm" onClick={() => removeOption(opt.id)} title="Hapus opsi">
                          <i className="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {formData.kategori === 'makanan' && (
              <div className="row g-3 mt-2">
                <div className="col-md-4">
                  <label className="form-label">Maksimal Pilih Lauk</label>
                  <input type="number" name="maksimal_lauk" className="form-control" value={formData.maksimal_lauk} onChange={handleChange} min="1" max="10" />
                </div>
                <div className="col-md-4 d-flex align-items-end">
                  <div className="form-check">
                    <input className="form-check-input" type="checkbox" name="wajib_pilih_lauk" id="wajib_pilih_lauk" checked={formData.wajib_pilih_lauk} onChange={handleChange} />
                    <label className="form-check-label" htmlFor="wajib_pilih_lauk">Wajib pilih lauk</label>
                  </div>
                </div>
                <div className="col-md-4 d-flex align-items-end">
                  <div className="form-check">
                    <input className="form-check-input" type="checkbox" name="wajib_pilih_sambal" id="wajib_pilih_sambal" checked={formData.wajib_pilih_sambal} onChange={handleChange} />
                    <label className="form-check-label" htmlFor="wajib_pilih_sambal">Wajib pilih sambal</label>
                  </div>
                </div>
              </div>
            )}

            <hr className="my-4" />
            <button className="btn btn-primary" type="submit" disabled={saving}>
              {saving ? 'Menyimpan...' : 'Simpan'}
            </button>
          </form>
        </div>
      </div>
    </>
  );
}
