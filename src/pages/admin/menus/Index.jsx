import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function MenuIndex() {
  const [menus, setMenus] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchMenus();
  }, []);

  const fetchMenus = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('menus')
        .select('*')
        .order('created_at', { ascending: false });

      if (error) throw error;
      setMenus(data || []);
    } catch (error) {
      console.error('Error fetching menus:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Hapus menu ini?')) return;
    
    try {
      const { error } = await supabase
        .from('menus')
        .delete()
        .eq('id', id);

      if (error) throw error;
      
      setMenus(menus.filter(m => m.id !== id));
      alert('Menu berhasil dihapus');
    } catch (error) {
      console.error('Error deleting menu:', error.message);
      alert('Gagal menghapus menu');
    }
  };

  const getStatusBadgeClass = (status) => {
    if (status === 'tersedia') return 'bg-success';
    if (status === 'habis') return 'bg-warning';
    return 'bg-secondary';
  };

  const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
  };

  return (
    <>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Menu</h2>
        <Link to="/admin/menus/create" className="btn btn-primary">Tambah Menu</Link>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          {loading ? (
            <div className="text-center py-4 text-muted">Memuat data...</div>
          ) : (
            <>
              {/* Desktop View */}
              <div className="d-none d-md-block table-responsive">
                <table className="table table-striped align-middle">
                  <thead>
                    <tr>
                      <th style={{ width: '60px' }}>Foto</th>
                      <th>Nama</th>
                      <th>Kategori</th>
                      <th>Harga</th>
                      <th>Stok</th>
                      <th>Status</th>
                      <th className="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {menus.length === 0 ? (
                      <tr>
                        <td colSpan="7" className="text-center text-muted">Belum ada menu.</td>
                      </tr>
                    ) : (
                      menus.map(menu => (
                        <tr key={menu.id}>
                          <td>
                            {menu.gambar ? (
                              <img src={menu.gambar} alt={menu.nama_menu} style={{ width: '40px', height: '40px', objectFit: 'cover', borderRadius: '4px' }} />
                            ) : (
                              <div style={{ width: '40px', height: '40px', backgroundColor: '#e9ecef', borderRadius: '4px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                <i className="bi bi-image text-muted"></i>
                              </div>
                            )}
                          </td>
                          <td className="fw-semibold">{menu.nama_menu}</td>
                          <td>{menu.kategori.charAt(0).toUpperCase() + menu.kategori.slice(1)}</td>
                          <td>{formatRupiah(menu.harga)}</td>
                          <td>{menu.stok}</td>
                          <td>
                            <span className={`badge ${getStatusBadgeClass(menu.status)}`}>
                              {menu.status.replace('_', ' ')}
                            </span>
                          </td>
                          <td className="text-end">
                            <Link to={`/admin/menus/${menu.id}`} className="btn btn-sm btn-outline-secondary me-1">Detail</Link>
                            <Link to={`/admin/menus/${menu.id}/edit`} className="btn btn-sm btn-outline-primary me-1">Ubah</Link>
                            <button onClick={() => handleDelete(menu.id)} className="btn btn-sm btn-outline-danger">Hapus</button>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>

              {/* Mobile View */}
              <div className="d-md-none">
                {menus.length === 0 ? (
                  <div className="text-center py-4 text-muted">Belum ada menu.</div>
                ) : (
                  menus.map(menu => (
                    <div key={menu.id} className="card border-0 shadow-sm mb-3" style={{ backgroundColor: 'var(--bs-card-bg)', border: '1px solid rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                      <div className="card-body p-3">
                        <div className="d-flex align-items-center mb-3">
                          <div className="me-3 flex-shrink-0">
                            {menu.gambar ? (
                              <img src={menu.gambar} alt={menu.nama_menu} style={{ width: '60px', height: '60px', objectFit: 'cover', borderRadius: '8px' }} />
                            ) : (
                              <div style={{ width: '60px', height: '60px', backgroundColor: '#e9ecef', borderRadius: '8px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                <i className="bi bi-image text-muted fs-4"></i>
                              </div>
                            )}
                          </div>
                          <div className="flex-grow-1">
                            <div className="d-flex justify-content-between align-items-start">
                              <h6 className="fw-bold mb-1 text-primary">{menu.nama_menu}</h6>
                              <span className={`badge ${getStatusBadgeClass(menu.status)}`}>
                                {menu.status.replace('_', ' ')}
                              </span>
                            </div>
                            <small className="text-muted">{menu.kategori.charAt(0).toUpperCase() + menu.kategori.slice(1)}</small>
                          </div>
                        </div>
                        <hr className="my-2 opacity-50" style={{ color: 'var(--bs-body-color)' }} />
                        <div className="row g-2 mb-3">
                          <div className="col-6">
                            <small className="text-muted d-block">Kategori</small>
                            <span className="fw-semibold">{menu.kategori.charAt(0).toUpperCase() + menu.kategori.slice(1)}</span>
                          </div>
                          <div className="col-6 text-end">
                            <small className="text-muted d-block">Harga</small>
                            <span className="fw-bold text-success">{formatRupiah(menu.harga)}</span>
                          </div>
                        </div>
                        <div className="mb-3">
                          <small className="text-muted d-block">Stok</small>
                          <span className="fw-semibold">{menu.stok}</span>
                        </div>
                        <div className="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style={{ borderTopColor: 'rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                          <Link to={`/admin/menus/${menu.id}`} className="btn btn-sm btn-outline-secondary px-3">Detail</Link>
                          <Link to={`/admin/menus/${menu.id}/edit`} className="btn btn-sm btn-outline-primary px-3">Ubah</Link>
                          <button onClick={() => handleDelete(menu.id)} className="btn btn-sm btn-outline-danger px-3">Hapus</button>
                        </div>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </>
          )}
        </div>
      </div>
    </>
  );
}
