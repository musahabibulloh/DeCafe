import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function UserIndex() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const { data, error } = await supabase
        .from('users')
        .select('*')
        .neq('role', 'customer')
        .neq('role', 'pelayan')
        .order('created_at', { ascending: false });

      if (error) throw error;
      setUsers(data || []);
    } catch (error) {
      console.error('Error fetching users:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Hapus user ini?')) return;
    
    try {
      // Ini hanya akan menghapus dari public.users (karena RLS/foreign key cascade dari auth.users)
      // Untuk benar-benar menghapus user dari auth, butuh akses Supabase Admin API backend.
      const { error } = await supabase
        .from('users')
        .delete()
        .eq('id', id);

      if (error) throw error;
      
      setUsers(users.filter(u => u.id !== id));
      alert('User berhasil dihapus (dari public)');
    } catch (error) {
      console.error('Error deleting user:', error.message);
      alert('Gagal menghapus user');
    }
  };

  return (
    <>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Data User</h2>
        <Link to="/admin/users/create" className="btn btn-primary">Tambah User</Link>
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
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th className="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {users.length === 0 ? (
                      <tr>
                        <td colSpan="4" className="text-center text-muted">Belum ada user.</td>
                      </tr>
                    ) : (
                      users.map(user => (
                        <tr key={user.id}>
                          <td>{user.name}</td>
                          <td>{user.email}</td>
                          <td>
                            <span className="badge bg-dark">{user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                          </td>
                          <td className="text-end">
                            <Link to={`/admin/users/${user.id}`} className="btn btn-sm btn-outline-secondary me-1">Detail</Link>
                            <Link to={`/admin/users/${user.id}/edit`} className="btn btn-sm btn-outline-primary me-1">Ubah</Link>
                            <button onClick={() => handleDelete(user.id)} className="btn btn-sm btn-outline-danger">Hapus</button>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>

              {/* Mobile View */}
              <div className="d-md-none">
                {users.length === 0 ? (
                  <div className="text-center py-4 text-muted">Belum ada user.</div>
                ) : (
                  users.map(user => (
                    <div key={user.id} className="card border-0 shadow-sm mb-3" style={{ backgroundColor: 'var(--bs-card-bg)', border: '1px solid rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                      <div className="card-body p-3">
                        <div className="d-flex justify-content-between align-items-center mb-2">
                          <h6 className="fw-bold mb-0 text-primary">{user.name}</h6>
                          <span className="badge bg-dark">{user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                        </div>
                        <hr className="my-2 opacity-50" style={{ color: 'var(--bs-body-color)' }} />
                        <div className="mb-3">
                          <small className="text-muted d-block">Email</small>
                          <span className="fw-semibold">{user.email}</span>
                        </div>
                        <div className="d-grid gap-2 d-flex justify-content-end mt-2 pt-2 border-top" style={{ borderTopColor: 'rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                          <Link to={`/admin/users/${user.id}`} className="btn btn-sm btn-outline-secondary px-3">Detail</Link>
                          <Link to={`/admin/users/${user.id}/edit`} className="btn btn-sm btn-outline-primary px-3">Ubah</Link>
                          <button onClick={() => handleDelete(user.id)} className="btn btn-sm btn-outline-danger px-3">Hapus</button>
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
