import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function UserCreate() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    role: '',
    password: ''
  });
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      setLoading(true);
      
      // Kita butuh membuat client Supabase baru tanpa sesi
      // supaya Admin tidak ter-logout saat mendaftarkan user baru
      const { createClient } = await import('@supabase/supabase-js');
      const tempSupabase = createClient(
        import.meta.env.VITE_SUPABASE_URL,
        import.meta.env.VITE_SUPABASE_ANON_KEY,
        {
          auth: {
            autoRefreshToken: false,
            persistSession: false,
            detectSessionInUrl: false
          }
        }
      );

      // 1. Buat User di Auth
      const { data: authData, error: authError } = await tempSupabase.auth.signUp({
        email: formData.email,
        password: formData.password,
        options: {
          data: { name: formData.name }
        }
      });

      if (authError) {
        if (authError.message.includes('User already registered')) {
          alert('Email ini sudah terdaftar sebelumnya!');
          return;
        }
        throw authError;
      }

      // 2. Jika Supabase mewajibkan konfirmasi Email, user mungkin tidak punya ID session.
      // Namun ID user tetap terbentuk.
      const userId = authData?.user?.id;
      
      if (!userId) {
        alert('User berhasil dibuat namun ID tidak ditemukan (mungkin butuh konfirmasi email).');
        navigate('/admin/users');
        return;
      }

      // 3. Tunggu sebentar agar Trigger database selesai memasukkan data ke tabel public.users
      await new Promise(resolve => setTimeout(resolve, 1000));

      // 4. Update role di tabel public.users
      const { error: updateError } = await supabase
        .from('users')
        .update({ role: formData.role })
        .eq('id', userId);

      if (updateError) {
        console.error('Gagal update role:', updateError);
        alert('User terbuat, tapi gagal mengatur Role. Pastikan RLS public.users dimatikan untuk Admin.');
      } else {
        alert('User berhasil dibuat!');
      }
      
      navigate('/admin/users');
    } catch (e) {
      console.error(e);
      alert('Gagal menambahkan user: ' + e.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah User</h2>
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
                <input 
                  type="email" 
                  name="email" 
                  className="form-control" 
                  value={formData.email} 
                  onChange={handleChange} 
                  required 
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
                <label className="form-label">Password</label>
                <input 
                  type="password" 
                  name="password" 
                  className="form-control" 
                  value={formData.password} 
                  onChange={handleChange} 
                  required 
                />
              </div>
            </div>
            <hr className="my-4" />
            <button className="btn btn-primary" type="submit" disabled={loading}>
              {loading ? 'Menyimpan...' : 'Simpan'}
            </button>
          </form>
        </div>
      </div>
    </>
  );
}
