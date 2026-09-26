import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function UserEdit() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    role: '',
    password: ''
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    fetchUser();
  }, [id]);

  const fetchUser = async () => {
    try {
      const { data, error } = await supabase
        .from('users')
        .select('*')
        .eq('id', id)
        .single();
      
      if (error) throw error;
      if (data) {
        setFormData({
          name: data.name || '',
          email: data.email || '',
          role: data.role || '',
          password: ''
        });
      }
    } catch (error) {
      console.error('Error fetching user:', error.message);
      alert('Gagal memuat data user');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);
    
    try {
      // Update data in public.users
      const { error } = await supabase
        .from('users')
        .update({
          name: formData.name,
          role: formData.role
        })
        .eq('id', id);

      if (error) throw error;
      
      alert('User berhasil diubah!');
      navigate('/admin/users');
    } catch (error) {
      console.error('Error updating user:', error.message);
      alert('Gagal mengubah user');
    } finally {
      setSaving(false);
    }
  };

  if (loading) return <div className="text-center py-4">Memuat data...</div>;

  return (
    <>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Ubah User</h2>
        <Link to="/admin/users" className="btn btn-outline-secondary">Kembali</Link>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          <form onSubmit={handleSubmit}>
            <div className="row g-3">
              <div className="col-md-6">
                <label className="form-label">Nama</label>
                <input 
                  type="text" 
                  name="name" 
                  className="form-control" 
                  value={formData.name} 
                  onChange={handleChange} 
                  required 
                />
              </div>
              <div className="col-md-6">
                <label className="form-label">Email</label>
                {/* Email cannot be easily changed via public client in Supabase without confirmation flow */}
                <input 
                  type="email" 
                  name="email" 
                  className="form-control" 
                  value={formData.email} 
                  disabled
                  title="Email tidak dapat diubah di sini"
                />
              </div>
              <div className="col-md-6">
                <label className="form-label">Role</label>
                <select 
                  name="role" 
                  className="form-select" 
                  value={formData.role} 
                  onChange={handleChange} 
                  required
                >
                  <option value="">Pilih Role</option>
                  <option value="admin">Admin</option>
                  <option value="kasir">Kasir</option>
                  <option value="dapur">Dapur</option>
                </select>
              </div>
              <div className="col-md-6">
                <label className="form-label">Password (Opsional)</label>
                <input 
                  type="password" 
                  name="password" 
                  className="form-control" 
                  value={formData.password} 
                  onChange={handleChange}
                  placeholder="Kosongkan jika tidak ingin mengubah"
                />
              </div>
            </div>
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
