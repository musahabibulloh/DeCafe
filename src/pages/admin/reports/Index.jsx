import React, { useState, useEffect, useMemo } from 'react';
import { Link } from 'react-router-dom';
import supabase from '../../../lib/supabase';

export default function ReportsIndex() {
  const [loading, setLoading] = useState(true);
  const [orders, setOrders] = useState([]);
  const [orderItems, setOrderItems] = useState([]);
  const [menus, setMenus] = useState([]);
  const [users, setUsers] = useState([]);
  
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      
      const [ordersRes, itemsRes, menusRes, usersRes] = await Promise.all([
        supabase.from('orders').select('*').order('created_at', { ascending: false }),
        supabase.from('order_items').select('*'),
        supabase.from('menus').select('id, nama_menu'),
        supabase.from('users').select('id, name')
      ]);

      if (ordersRes.error) throw ordersRes.error;
      
      setOrders(ordersRes.data || []);
      setOrderItems(itemsRes.data || []);
      setMenus(menusRes.data || []);
      setUsers(usersRes.data || []);
    } catch (error) {
      console.error('Error fetching reports data:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleFilter = (e) => {
    e.preventDefault();
    // In a real app we might refetch with date filters in the Supabase query.
    // For this migration, we'll filter client-side for simplicity if dates are provided.
  };

  const handleReset = () => {
    setStartDate('');
    setEndDate('');
  };

  const filteredOrders = useMemo(() => {
    let filtered = orders;
    if (startDate) {
      filtered = filtered.filter(o => new Date(o.created_at) >= new Date(startDate));
    }
    if (endDate) {
      // Add one day to include the end date fully
      const end = new Date(endDate);
      end.setDate(end.getDate() + 1);
      filtered = filtered.filter(o => new Date(o.created_at) < end);
    }
    return filtered;
  }, [orders, startDate, endDate]);

  const totalTransaksi = filteredOrders.filter(o => o.status_pesanan === 'selesai' || o.status_pesanan === 'dibayar').length;
  const totalPendapatan = filteredOrders
    .filter(o => o.status_pesanan === 'selesai' || o.status_pesanan === 'dibayar')
    .reduce((sum, o) => sum + (o.total_harga || 0), 0);

  const statusCounts = useMemo(() => {
    const counts = {};
    filteredOrders.forEach(o => {
      const status = o.status_pesanan || 'pending';
      counts[status] = (counts[status] || 0) + 1;
    });
    return counts;
  }, [filteredOrders]);

  const menuTerlaris = useMemo(() => {
    // We only count items from filtered orders
    const validOrderIds = new Set(filteredOrders.map(o => o.id));
    const validItems = orderItems.filter(item => validOrderIds.has(item.order_id));
    
    const menuCounts = {};
    validItems.forEach(item => {
      menuCounts[item.menu_id] = (menuCounts[item.menu_id] || 0) + item.jumlah;
    });

    const result = Object.entries(menuCounts)
      .map(([menuId, qty]) => {
        const menu = menus.find(m => m.id === parseInt(menuId));
        return {
          nama_menu: menu ? menu.nama_menu : 'Menu Dihapus',
          total_terjual: qty
        };
      })
      .sort((a, b) => b.total_terjual - a.total_terjual)
      .slice(0, 5); // top 5

    return result;
  }, [filteredOrders, orderItems, menus]);

  const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID').format(number);
  };

  const formatDate = (isoString) => {
    if (!isoString) return '-';
    const d = new Date(isoString);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
  };

  const getUserName = (id) => {
    const u = users.find(user => user.id === id);
    return u ? u.name : '-';
  };

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;

  return (
    <>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h2>Laporan Penjualan</h2>
      </div>

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <form className="row g-3" onSubmit={handleFilter}>
            <div className="col-md-4">
              <label className="form-label">Tanggal Mulai</label>
              <input 
                type="date" 
                className="form-control" 
                value={startDate} 
                onChange={e => setStartDate(e.target.value)} 
              />
            </div>
            <div className="col-md-4">
              <label className="form-label">Tanggal Akhir</label>
              <input 
                type="date" 
                className="form-control" 
                value={endDate} 
                onChange={e => setEndDate(e.target.value)} 
              />
            </div>
            <div className="col-md-4 d-flex align-items-end gap-2">
              <button className="btn btn-primary" type="submit">Filter</button>
              <button type="button" className="btn btn-outline-secondary" onClick={handleReset}>Reset</button>
            </div>
          </form>
        </div>
      </div>

      <div className="row g-3 mb-4">
        <div className="col-md-4">
          <div className="card card-stat shadow-sm">
            <div className="card-body">
              <p className="text-muted mb-1">Total Transaksi Selesai</p>
              <h4 className="mb-0">{totalTransaksi}</h4>
            </div>
          </div>
        </div>
        <div className="col-md-4">
          <div className="card card-stat shadow-sm">
            <div className="card-body">
              <p className="text-muted mb-1">Total Pendapatan</p>
              <h4 className="mb-0">Rp {formatRupiah(totalPendapatan)}</h4>
            </div>
          </div>
        </div>
        <div className="col-md-4">
          <div className="card card-stat shadow-sm">
            <div className="card-body">
              <p className="text-muted mb-1">Total Pesanan</p>
              <h4 className="mb-0">{filteredOrders.length}</h4>
            </div>
          </div>
        </div>
      </div>

      <div className="row g-3 mb-4">
        <div className="col-md-6">
          <div className="card shadow-sm h-100">
            <div className="card-body">
              <h5 className="mb-3">Menu Terlaris</h5>
              <ul className="list-group list-group-flush">
                {menuTerlaris.length > 0 ? menuTerlaris.map((menu, i) => (
                  <li key={i} className="list-group-item d-flex justify-content-between px-0">
                    <span>{menu.nama_menu}</span>
                    <span className="fw-semibold">{menu.total_terjual}</span>
                  </li>
                )) : (
                  <li className="list-group-item text-muted px-0">Belum ada data.</li>
                )}
              </ul>
            </div>
          </div>
        </div>
        <div className="col-md-6">
          <div className="card shadow-sm h-100">
            <div className="card-body">
              <h5 className="mb-3">Total Pesanan per Status</h5>
              <ul className="list-group list-group-flush">
                {Object.keys(statusCounts).length > 0 ? Object.entries(statusCounts).map(([status, total], i) => (
                  <li key={i} className="list-group-item d-flex justify-content-between px-0">
                    <span className="text-capitalize">{status.replace(/_/g, ' ')}</span>
                    <span className="fw-semibold">{total}</span>
                  </li>
                )) : (
                  <li className="list-group-item text-muted px-0">Belum ada data.</li>
                )}
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          <h5 className="mb-3">Daftar Transaksi</h5>
          
          {/* Desktop View */}
          <div className="d-none d-md-block table-responsive">
            <table className="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Kode Pesanan</th>
                  <th>Pelayan</th>
                  <th>Meja</th>
                  <th>Total</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>
                {filteredOrders.length > 0 ? filteredOrders.map(order => (
                  <tr key={order.id}>
                    <td>{order.kode_pesanan}</td>
                    <td>{getUserName(order.pelayan_id)}</td>
                    <td>{order.nomor_meja}</td>
                    <td>Rp {formatRupiah(order.total_harga)}</td>
                    <td>{formatDate(order.created_at)}</td>
                  </tr>
                )) : (
                  <tr>
                    <td colSpan="5" className="text-center text-muted">Belum ada transaksi.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {/* Mobile View */}
          <div className="d-md-none">
            {filteredOrders.length > 0 ? filteredOrders.map(order => (
              <div key={order.id} className="card border-0 shadow-sm mb-3" style={{ backgroundColor: 'var(--bs-card-bg)', border: '1px solid rgba(var(--bs-body-color-rgb), 0.1) !important' }}>
                <div className="card-body p-3">
                  <div className="d-flex justify-content-between align-items-center mb-2">
                    <h6 className="fw-bold mb-0 text-primary">{order.kode_pesanan}</h6>
                    <small className="text-muted">{formatDate(order.created_at)}</small>
                  </div>
                  <hr className="my-2 opacity-50" style={{ color: 'var(--bs-body-color)' }} />
                  <div className="row g-2">
                    <div className="col-6">
                      <small className="text-muted d-block">Meja</small>
                      <span className="fw-semibold">{order.nomor_meja}</span>
                    </div>
                    <div className="col-6 text-end">
                      <small className="text-muted d-block">Total</small>
                      <span className="fw-bold text-success">Rp {formatRupiah(order.total_harga)}</span>
                    </div>
                    <div className="col-12 mt-2">
                      <small className="text-muted d-block">Pelayan</small>
                      <span className="fw-semibold">{getUserName(order.pelayan_id)}</span>
                    </div>
                  </div>
                </div>
              </div>
            )) : (
              <div className="text-center py-4 text-muted">Belum ada transaksi.</div>
            )}
          </div>
        </div>
      </div>
    </>
  );
}
