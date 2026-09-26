import React, { useState, useEffect } from 'react';
import supabase from '../../../lib/supabase';
import { QRCodeSVG } from 'qrcode.react';

export default function MejaIndex() {
  const [mejas, setMejas] = useState([]);
  const [loading, setLoading] = useState(true);
  const [nomorMeja, setNomorMeja] = useState('');
  const [toastMessage, setToastMessage] = useState('');

  useEffect(() => {
    fetchMejas();
  }, []);

  const fetchMejas = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('meja')
        .select('*')
        .order('nomor_meja', { ascending: true });

      if (error) throw error;
      setMejas(data || []);
    } catch (error) {
      console.error('Error fetching meja:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleAdd = async (e) => {
    e.preventDefault();
    if (!nomorMeja) return;

    try {
      // Generate a random token
      const token = Math.random().toString(36).substring(2, 15);
      
      const { error } = await supabase
        .from('meja')
        .insert([{
          nomor_meja: nomorMeja,
          token: token,
          status: 'tersedia'
        }]);

      if (error) throw error;
      
      setNomorMeja('');
      fetchMejas();
      showToast('Meja berhasil ditambahkan!');
    } catch (error) {
      console.error('Error adding meja:', error.message);
      alert('Gagal menambahkan meja');
    }
  };

  const handleRegenerate = async (id, nomor) => {
    if (!window.confirm('Yakin ingin buat ulang token QR? QR lama tidak akan berlaku lagi.')) return;
    
    try {
      const token = Math.random().toString(36).substring(2, 15);
      const { error } = await supabase
        .from('meja')
        .update({ token: token })
        .eq('id', id);

      if (error) throw error;
      fetchMejas();
      showToast('Token QR berhasil diperbarui!');
    } catch (error) {
      console.error('Error regenerarting token:', error.message);
      alert('Gagal memperbarui token');
    }
  };

  const handleDelete = async (id, nomor) => {
    if (!window.confirm(`Yakin ingin hapus Meja ${nomor}?`)) return;
    
    try {
      const { error } = await supabase
        .from('meja')
        .delete()
        .eq('id', id);

      if (error) throw error;
      fetchMejas();
      showToast('Meja berhasil dihapus!');
    } catch (error) {
      console.error('Error deleting meja:', error.message);
      alert('Gagal menghapus meja');
    }
  };

  const showToast = (msg) => {
    setToastMessage(msg);
    setTimeout(() => setToastMessage(''), 3000);
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
      showToast('Link berhasil disalin!');
    }).catch(() => {
      prompt('Salin link ini:', text);
    });
  };

  const getScanUrl = (token) => {
    // Determine the base URL dynamically based on current window location
    const baseUrl = window.location.origin;
    return `${baseUrl}/customer/dashboard?t=${token}`;
  };

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 className="h3 mb-1">Kelola Meja & QR Code</h1>
          <p className="text-muted mb-0">Buat dan kelola QR code untuk setiap meja. Customer tinggal scan untuk langsung pesan.</p>
        </div>
      </div>

      {toastMessage && (
        <div className="alert alert-success alert-dismissible fade show" role="alert">
          <i className="bi bi-check-circle-fill me-2"></i>{toastMessage}
          <button type="button" className="btn-close" onClick={() => setToastMessage('')}></button>
        </div>
      )}

      {/* Add Table Form */}
      <div className="card border-0 shadow-sm mb-4">
        <div className="card-header bg-success text-white">
          <h5 className="mb-0"><i className="bi bi-plus-circle-fill me-2"></i>Tambah Meja Baru</h5>
        </div>
        <div className="card-body">
          <form onSubmit={handleAdd} className="row g-3 align-items-end">
            <div className="col-md-6">
              <label htmlFor="nomor_meja" className="form-label fw-bold">Nomor Meja</label>
              <input 
                type="text" 
                name="nomor_meja" 
                id="nomor_meja" 
                className="form-control" 
                placeholder="Contoh: 1, 2, 3, A1, VIP-01" 
                value={nomorMeja}
                onChange={(e) => setNomorMeja(e.target.value)}
                required 
              />
            </div>
            <div className="col-md-6">
              <button type="submit" className="btn btn-success">
                <i className="bi bi-plus-lg me-1"></i> Tambah Meja
              </button>
            </div>
          </form>
        </div>
      </div>

      {/* Tables List */}
      {loading ? (
        <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>
      ) : mejas.length === 0 ? (
        <div className="alert alert-info text-center py-5">
          <i className="bi bi-inbox fs-1 d-block mb-3"></i>
          <h5>Belum ada meja</h5>
          <p className="mb-0 text-muted">Tambahkan meja di atas untuk mulai membuat QR code.</p>
        </div>
      ) : (
        <div className="row g-4">
          {mejas.map(meja => {
            const scanUrl = getScanUrl(meja.token);
            return (
              <div key={meja.id} className="col-md-6 col-lg-4 col-xl-3">
                <div className="card border-0 shadow-sm h-100">
                  <div className="card-body text-center p-4">
                    {/* QR Code Display */}
                    <div className="mb-3 p-3 rounded-3" style={{ background: '#ffffff' }}>
                      <div className="d-flex justify-content-center">
                        <QRCodeSVG value={scanUrl} size={150} />
                      </div>
                    </div>

                    <h4 className="fw-bold mb-1">Meja {meja.nomor_meja}</h4>
                    <p className="text-muted small mb-3 text-break" style={{ fontSize: '0.75rem' }}>
                      {scanUrl}
                    </p>

                    {/* Actions */}
                    <div className="d-flex gap-2 flex-wrap justify-content-center">
                      <button type="button" className="btn btn-outline-secondary btn-sm" onClick={() => copyToClipboard(scanUrl)} title="Salin Link">
                        <i className="bi bi-clipboard"></i> Salin
                      </button>
                      <button type="button" onClick={() => handleRegenerate(meja.id, meja.nomor_meja)} className="btn btn-outline-warning btn-sm" title="Generate ulang QR">
                        <i className="bi bi-arrow-clockwise"></i>
                      </button>
                      <button type="button" onClick={() => handleDelete(meja.id, meja.nomor_meja)} className="btn btn-outline-danger btn-sm" title="Hapus Meja">
                        <i className="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            )
          })}
        </div>
      )}
    </div>
  );
}
