import React, { useState, useEffect, useMemo } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import supabase from '../../lib/supabase';

export default function CustomerMenus() {
  const [searchParams] = useSearchParams();
  const navigate = useNavigate();
  const t = searchParams.get('t'); // QR Token
  
  const savedMeja = localStorage.getItem('customer_meja') || '';
  const [nomorMeja, setNomorMeja] = useState(savedMeja);
  
  const [menus, setMenus] = useState([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  
  // Cart state: { menuId: { qty: 0, customizations: [] } }
  const [cart, setCart] = useState({});
  
  // Modal states
  const [showOrderModal, setShowOrderModal] = useState(false);
  const [showCustomModal, setShowCustomModal] = useState(false);
  const [activeMenu, setActiveMenu] = useState(null);
  
  // Customization Form State
  const [customForm, setCustomForm] = useState({
    jenis_lauk: [],
    jenis_sambal: '',
    ekstra_lauk: []
  });

  // Order Confirmation State
  const [orderForm, setOrderForm] = useState({
    atas_nama: '',
    tipe_pesanan: 'dine_in',
    nomor_meja: savedMeja
  });

  useEffect(() => {
    fetchMenus();
    if (t) {
      resolveToken(t);
    }
  }, [t]);

  const resolveToken = async (token) => {
    try {
      const { data, error } = await supabase
        .from('meja')
        .select('nomor_meja')
        .eq('token', token)
        .single();
        
      if (!error && data) {
        setNomorMeja(data.nomor_meja);
        localStorage.setItem('customer_meja', data.nomor_meja);
        setOrderForm(prev => ({ ...prev, nomor_meja: data.nomor_meja }));
      }
    } catch (e) {
      console.error(e);
    }
  };

  const fetchMenus = async () => {
    try {
      const { data: menusData, error: menusError } = await supabase
        .from('menus')
        .select(`
          *,
          options:menu_options(*)
        `)
        .order('kategori')
        .order('nama_menu');

      if (menusError) throw menusError;
      
      // Filter out nonaktif
      const activeMenus = (menusData || []).filter(m => m.status !== 'nonaktif');
      setMenus(activeMenus);
    } catch (error) {
      console.error('Error fetching menus:', error.message);
    } finally {
      setLoading(false);
    }
  };

  const filteredMenus = useMemo(() => {
    if (!search) return menus;
    const lowerSearch = search.toLowerCase();
    return menus.filter(m => 
      m.nama_menu.toLowerCase().includes(lowerSearch) || 
      (m.deskripsi && m.deskripsi.toLowerCase().includes(lowerSearch))
    );
  }, [menus, search]);

  const groupedMenus = useMemo(() => {
    return filteredMenus.reduce((acc, menu) => {
      const cat = menu.kategori;
      if (!acc[cat]) acc[cat] = [];
      acc[cat].push(menu);
      return acc;
    }, {});
  }, [filteredMenus]);

  const calculatePortionPrice = (menuName, basePrice, customizationObj) => {
    let price = parseInt(basePrice) || 0;
    const nameLower = menuName.toLowerCase();
    const isNasiBakar = nameLower.includes('nasi bakar') || nameLower.includes('nasbak');
    
    if (isNasiBakar) {
      if (nameLower.includes('reguler') || nameLower.includes('regular')) {
        price = 10000;
        if (customizationObj.lauk.some(l => l.includes('*'))) {
          price = 12000;
        }
      } else if (nameLower.includes('mix')) {
        price = 12000;
      } else if (nameLower.includes('jumbo')) {
        price = 15000;
      }
      
      if (customizationObj.ekstra && customizationObj.ekstra.length > 0) {
        price += (customizationObj.ekstra.length * 3000);
      }
    }
    return price;
  };

  const handleIncrement = (menu) => {
    const currentItem = cart[menu.id] || { qty: 0, customizations: [] };
    if (currentItem.qty >= menu.stok) return;

    if (menu.options && menu.options.length > 0) {
      setActiveMenu(menu);
      setCustomForm({ jenis_lauk: [], jenis_sambal: '', ekstra_lauk: [] });
      
      // Select first available sambal by default if not set
      const sambalOptions = menu.options.filter(o => o.tipe === 'sambal' && o.status === 'tersedia');
      if (sambalOptions.length > 0) {
        setCustomForm(prev => ({ ...prev, jenis_sambal: sambalOptions[0].nama_opsi }));
      }
      
      setShowCustomModal(true);
    } else {
      setCart({
        ...cart,
        [menu.id]: {
          ...currentItem,
          qty: currentItem.qty + 1
        }
      });
    }
  };

  const handleDecrement = (menuId) => {
    const currentItem = cart[menuId];
    if (!currentItem || currentItem.qty <= 0) return;

    const newQty = currentItem.qty - 1;
    let newCustomizations = [...currentItem.customizations];
    if (newCustomizations.length > 0 && newQty < newCustomizations.length) {
      newCustomizations.pop();
    }

    if (newQty === 0) {
      const newCart = { ...cart };
      delete newCart[menuId];
      setCart(newCart);
    } else {
      setCart({
        ...cart,
        [menuId]: {
          qty: newQty,
          customizations: newCustomizations
        }
      });
    }
  };

  const saveCustomization = () => {
    if (activeMenu.wajib_pilih_lauk && customForm.jenis_lauk.length === 0) {
      alert('Silakan pilih minimal 1 jenis lauk.');
      return;
    }
    if (activeMenu.wajib_pilih_sambal && !customForm.jenis_sambal) {
      alert('Silakan pilih jenis sambal.');
      return;
    }

    const currentItem = cart[activeMenu.id] || { qty: 0, customizations: [] };
    
    const newCustomization = {
      lauk: customForm.jenis_lauk,
      sambal: customForm.jenis_sambal || 'Tidak ada',
      ekstra: customForm.ekstra_lauk
    };

    setCart({
      ...cart,
      [activeMenu.id]: {
        qty: currentItem.qty + 1,
        customizations: [...currentItem.customizations, newCustomization]
      }
    });

    setShowCustomModal(false);
  };

  const getCartTotals = () => {
    let totalQty = 0;
    let totalPrice = 0;
    
    Object.entries(cart).forEach(([menuId, item]) => {
      const menu = menus.find(m => m.id === parseInt(menuId));
      if (!menu) return;
      
      totalQty += item.qty;
      
      if (item.customizations && item.customizations.length > 0) {
        item.customizations.forEach(cust => {
          totalPrice += calculatePortionPrice(menu.nama_menu, menu.harga, cust);
        });
        if (item.qty > item.customizations.length) {
          totalPrice += (item.qty - item.customizations.length) * menu.harga;
        }
      } else {
        totalPrice += (item.qty * menu.harga);
      }
    });
    
    return { totalQty, totalPrice };
  };

  const { totalQty, totalPrice } = getCartTotals();

  const handleCustomFormChange = (e, type, maxLauk = 1) => {
    const { value, checked } = e.target;
    if (type === 'lauk') {
      let updated = [...customForm.jenis_lauk];
      if (checked) {
        if (updated.length >= maxLauk) {
          if (maxLauk === 1) updated = [value];
          else {
            alert(`Pilihan lauk maksimal ${maxLauk}.`);
            return;
          }
        } else {
          updated.push(value);
        }
      } else {
        updated = updated.filter(v => v !== value);
      }
      setCustomForm({ ...customForm, jenis_lauk: updated });
    } else if (type === 'sambal') {
      setCustomForm({ ...customForm, jenis_sambal: value });
    } else if (type === 'ekstra') {
      let updated = [...customForm.ekstra_lauk];
      if (checked) updated.push(value);
      else updated = updated.filter(v => v !== value);
      setCustomForm({ ...customForm, ekstra_lauk: updated });
    }
  };

  const formatRupiah = (num) => new Intl.NumberFormat('id-ID').format(num);

  const submitOrder = async (e) => {
    e.preventDefault();
    if (totalQty === 0) return;
    
    if (!orderForm.atas_nama) {
      alert('Atas nama wajib diisi!');
      return;
    }

    const finalNomorMeja = nomorMeja || orderForm.nomor_meja;

    if (!finalNomorMeja && orderForm.tipe_pesanan === 'dine_in') {
      alert('Nomor meja wajib diisi untuk Dine In!');
      return;
    }

    try {
      const kodePesanan = 'ORD-' + Math.random().toString(36).substr(2, 6).toUpperCase();
      
      // 1. Insert Order
      const { data: newOrder, error: orderError } = await supabase
        .from('orders')
        .insert([{
          kode_pesanan: kodePesanan,
          atas_nama: orderForm.atas_nama,
          tipe_pesanan: orderForm.tipe_pesanan,
          nomor_meja: orderForm.tipe_pesanan === 'dine_in' ? finalNomorMeja : null,
          total_harga: totalPrice,
          status_pesanan: 'menunggu',
          status_pembayaran: 'belum_bayar'
        }])
        .select()
        .single();
        
      if (orderError) throw orderError;

      // 2. Insert Items
      const orderItems = [];
      Object.entries(cart).forEach(([menuId, item]) => {
        const menu = menus.find(m => m.id === parseInt(menuId));
        if (!menu) return;

        let notes = [];
        if (item.customizations && item.customizations.length > 0) {
          item.customizations.forEach((cust, i) => {
            const lauk = cust.lauk.length > 0 ? cust.lauk.join(', ') : 'Tidak ada';
            const ekstra = cust.ekstra.length > 0 ? cust.ekstra.join(', ') : 'Tidak ada';
            notes.push(`Porsi #${i+1} [Lauk: ${lauk} | Sambal: ${cust.sambal} | Ekstra: ${ekstra}]`);
          });
        }
        
        let itemTotal = 0;
        if (item.customizations && item.customizations.length > 0) {
          item.customizations.forEach(cust => {
            itemTotal += calculatePortionPrice(menu.nama_menu, menu.harga, cust);
          });
          if (item.qty > item.customizations.length) {
            itemTotal += (item.qty - item.customizations.length) * menu.harga;
          }
        } else {
          itemTotal = item.qty * menu.harga;
        }

        orderItems.push({
          order_id: newOrder.id,
          menu_id: menu.id,
          nama_menu: menu.nama_menu,
          harga: menu.harga,
          jumlah: item.qty,
          subtotal: itemTotal,
          catatan_item: notes.length > 0 ? notes.join(' \n ') : null
        });
      });

      const { error: itemsError } = await supabase.from('order_items').insert(orderItems);
      if (itemsError) throw itemsError;

      // Save order ID to localStorage so only this device sees it
      const savedOrders = JSON.parse(localStorage.getItem('customer_order_ids') || '[]');
      savedOrders.push(newOrder.id);
      localStorage.setItem('customer_order_ids', JSON.stringify(savedOrders));

      setShowOrderModal(false);
      setCart({});
      setOrderForm({
        atas_nama: '',
        tipe_pesanan: 'dine_in',
        nomor_meja: nomorMeja
      });
      
      // Arahkan pelanggan langsung ke halaman detail pesanan / pembayaran
      navigate(`/customer/orders/${newOrder.id}`);
    } catch (err) {
      console.error(err);
      alert('Gagal membuat pesanan');
    }
  };

  return (
    <>
      <style>
        {`
          .qty-selector {
              display: flex; align-items: center; border: 1px solid var(--border-color);
              border-radius: 30px; background-color: var(--input-bg); padding: 2px;
              overflow: hidden; transition: border-color var(--transition-speed) ease;
          }
          .qty-selector:focus-within { border-color: var(--primary-color); }
          .qty-btn {
              width: 32px; height: 32px; border-radius: 50%; border: none; background: transparent;
              color: var(--text-main); font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;
              transition: background-color var(--transition-speed) ease, transform 0.1s ease; outline: none;
          }
          .qty-btn:hover { background-color: rgba(var(--bs-body-color-rgb), 0.08); }
          .qty-btn:active { transform: scale(0.9); }
          .qty-input {
              width: 40px; border: none; background: transparent; text-align: center;
              color: var(--text-main); font-weight: 600; font-size: 0.95rem; outline: none;
          }
          .menu-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: 1px solid var(--border-color) !important; }
          .menu-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important; border-color: var(--primary-color) !important; }
          .customise-option-btn { color: var(--text-main) !important; border-color: var(--border-color) !important; background-color: var(--input-bg) !important; transition: all 0.2s ease !important; }
          .customise-option-btn:hover { background-color: var(--bg-card-hover) !important; border-color: var(--primary-color) !important; color: var(--primary-color) !important; }
          .btn-check:checked + .customise-option-btn { background: var(--primary-gradient) !important; color: #fff !important; border-color: transparent !important; box-shadow: 0 4px 12px var(--btn-primary-shadow) !important; }
          
          .menu-card-inner { flex-direction: row; }
          .menu-img-wrap { width: 110px; min-width: 110px; height: 100%; }
          .menu-img { width: 100%; height: 100%; object-fit: cover; }
          
          @media (min-width: 768px) {
              .sticky-sidebar { position: sticky; top: 1.5rem; max-height: calc(100vh - 3rem); overflow-y: auto; }
              .menu-card-inner { flex-direction: column; }
              .menu-img-wrap { width: 100%; min-width: 100%; height: 160px; }
          }
        `}
      </style>

      {/* Welcome Banner */}
      <div className="welcome-banner p-4 rounded-4 mb-4 shadow-sm" style={{ background: 'var(--primary-gradient)', color: '#fff' }}>
        <div className="d-flex align-items-center justify-content-between">
          <div>
            <h2 className="fw-bold mb-1">Halo, Customer! 👋</h2>
            {nomorMeja ? (
              <p className="mb-0 opacity-90"><i className="bi bi-geo-alt-fill me-1"></i> Anda di <strong>Meja {nomorMeja}</strong> — Silakan pilih menu dan pesan langsung!</p>
            ) : (
              <p className="mb-0 opacity-90">Nikmati menu makanan dan minuman terbaik dari Nasi Bakar Cak Win. Pesan langsung dari meja Anda.</p>
            )}
          </div>
          <div className="d-none d-sm-block fs-1 px-3">
            {nomorMeja ? (
              <span className="badge bg-white text-dark fs-4 px-3 py-2 rounded-3 shadow-sm" style={{ fontFamily: "'Outfit', sans-serif" }}>{nomorMeja}</span>
            ) : (
              <i className="bi bi-cup-hot-fill"></i>
            )}
          </div>
        </div>
      </div>

      <div className="row mb-4">
        <div className="col-md-6">
          <div className="input-group shadow-sm rounded-3 overflow-hidden">
            <span className="input-group-text border-0" style={{ backgroundColor: 'var(--input-bg)', border: '1px solid var(--border-color)', borderRight: 'none', color: 'var(--text-muted)' }}>
              <i className="bi bi-search"></i>
            </span>
            <input 
              type="text" 
              className="form-control border-0" 
              placeholder="Cari makanan atau minuman..." 
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              style={{ backgroundColor: 'var(--input-bg)', border: '1px solid var(--border-color)', borderLeft: 'none', padding: '0.75rem 1rem' }}
            />
          </div>
        </div>
      </div>

      <div className="row g-4">
        <div className="col-lg-8">
          {loading ? (
            <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>
          ) : (
            Object.keys(groupedMenus).length === 0 ? (
              <div className="alert alert-info text-center py-4">
                <i className="bi bi-search fs-2 mb-2 d-block"></i>
                Menu yang Anda cari tidak ditemukan. Coba ketik kata kunci lain!
              </div>
            ) : (
              Object.entries(groupedMenus).map(([kategori, items]) => (
                <div key={kategori} className="mb-4">
                  <div className="d-flex align-items-center gap-2 mb-3">
                    <h4 className="fw-bold mb-0 text-capitalize">{kategori}</h4>
                    <span className="badge bg-secondary rounded-pill">{items.length} Menu</span>
                  </div>
                  <div className="row g-3">
                    {items.map(menu => {
                      const qty = cart[menu.id]?.qty || 0;
                      const hasStock = menu.stok > 0 && menu.status === 'tersedia';
                      return (
                        <div key={menu.id} className="col-12 col-md-6 col-xl-4">
                          <div className="card h-100 menu-card overflow-hidden">
                            <div className="d-flex h-100 menu-card-inner">
                              <div className="menu-img-wrap bg-secondary-subtle d-flex align-items-center justify-content-center" style={{ borderRight: '1px solid var(--border-color)', backgroundColor: 'rgba(255,255,255,0.02)' }}>
                                {menu.gambar ? (
                                  <img src={`/storage/${menu.gambar}`} className="menu-img" alt={menu.nama_menu} onError={(e) => { e.target.onerror = null; e.target.src = "https://via.placeholder.com/300x160?text=No+Image"; }} />
                                ) : (
                                  <i className="bi bi-cup-straw fs-1 opacity-50" style={{ color: 'var(--text-muted)' }}></i>
                                )}
                              </div>
                              <div className="card-body d-flex flex-column justify-content-between p-3 w-100" style={{ minWidth: 0 }}>
                                <div className="mb-2">
                                  <h6 className="card-title fw-bold mb-1 text-truncate">{menu.nama_menu}</h6>
                                  <p className="small text-muted mb-0 text-truncate" title={menu.deskripsi}>{menu.deskripsi || 'Menu nikmat dari Nasi Bakar Cak Win'}</p>
                                </div>
                                <div>
                                  <div className="d-flex align-items-center justify-content-between mb-2">
                                    <span className="text-primary fw-bold fs-6">Rp {formatRupiah(menu.harga)}</span>
                                    <small className="text-muted" style={{ fontSize: '0.7rem' }}>Stok: {menu.stok}</small>
                                  </div>
                                  {hasStock ? (
                                    <div className="qty-selector w-100">
                                      <button type="button" className="qty-btn" onClick={() => handleDecrement(menu.id)}><i className="bi bi-dash"></i></button>
                                      <input type="text" className="qty-input flex-grow-1" value={qty} readOnly />
                                      <button type="button" className="qty-btn" onClick={() => handleIncrement(menu)}><i className="bi bi-plus"></i></button>
                                    </div>
                                  ) : (
                                    <span className="badge bg-danger w-100 py-2">Habis</span>
                                  )}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      )
                    })}
                  </div>
                </div>
              ))
            )
          )}
          <div style={{ height: 100 }} className="d-lg-none"></div>
        </div>

        {/* Sidebar Cart Desktop */}
        <div className="col-lg-4 d-none d-lg-block">
          <div className="sticky-sidebar">
            <div className="card border-0 shadow-sm">
              <div className="card-header bg-dark text-white py-3">
                <h6 className="mb-0 fw-semibold d-flex align-items-center gap-2"><i className="bi bi-cart-fill"></i> Ringkasan Pesanan</h6>
              </div>
              <div className="card-body p-0">
                <div style={{ maxHeight: 250, overflowY: 'auto' }}>
                  <div className="list-group list-group-flush">
                    {totalQty === 0 ? (
                      <div className="text-center text-muted py-4">Belum ada item dipilih</div>
                    ) : (
                      Object.entries(cart).map(([menuId, item]) => {
                        const menu = menus.find(m => m.id === parseInt(menuId));
                        if (!menu) return null;
                        
                        let itemTotal = 0;
                        if (item.customizations && item.customizations.length > 0) {
                          item.customizations.forEach(cust => {
                            itemTotal += calculatePortionPrice(menu.nama_menu, menu.harga, cust);
                          });
                          if (item.qty > item.customizations.length) {
                            itemTotal += (item.qty - item.customizations.length) * menu.harga;
                          }
                        } else {
                          itemTotal = item.qty * menu.harga;
                        }

                        return (
                          <div key={menuId} className="list-group-item px-3 py-2 bg-transparent border-0 border-bottom">
                            <div className="d-flex justify-content-between align-items-start mb-1">
                              <div style={{ flexGrow: 1, minWidth: 0 }}>
                                <span className="fw-semibold text-truncate d-block" style={{ maxWidth: 180, color: 'var(--bs-body-color)' }}>{menu.nama_menu}</span>
                                <small className="text-muted">Jumlah: {item.qty}</small>
                                {item.customizations.length > 0 && (
                                  <div className="mt-1 small text-muted font-monospace" style={{ fontSize: '0.72rem', lineHeight: 1.3 }}>
                                    {item.customizations.map((cust, i) => {
                                      const linePrice = calculatePortionPrice(menu.nama_menu, menu.harga, cust);
                                      const lauk = cust.lauk.length > 0 ? cust.lauk.join(', ') : 'Tidak ada';
                                      const ekstra = cust.ekstra.length > 0 ? cust.ekstra.join(', ') : 'Tidak ada';
                                      const text = `Porsi #${i+1} [Lauk: ${lauk} | Sambal: ${cust.sambal} | Ekstra: ${ekstra}]`;
                                      return (
                                        <div key={i} className="text-truncate text-wrap mb-1" title={text}>
                                          <i className="bi bi-gear-wide-connected me-1 text-primary"></i> {text} <span className="text-success fw-bold">(Rp {formatRupiah(linePrice)})</span>
                                        </div>
                                      );
                                    })}
                                  </div>
                                )}
                              </div>
                              <span className="fw-bold text-primary ms-2">Rp {formatRupiah(itemTotal)}</span>
                            </div>
                          </div>
                        )
                      })
                    )}
                  </div>
                </div>
                <div className="p-3 border-top">
                  <div className="d-flex justify-content-between align-items-center mb-3">
                    <span className="fw-bold">Total Pembayaran:</span>
                    <h4 className="text-primary fw-bold mb-0">Rp {formatRupiah(totalPrice)}</h4>
                  </div>
                  <button 
                    className="btn btn-success btn-lg w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" 
                    disabled={totalQty === 0}
                    onClick={() => setShowOrderModal(true)}
                  >
                    <i className="bi bi-send-fill"></i> Pesan Sekarang
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Floating Cart Mobile */}
      {totalQty > 0 && (
        <div className="d-lg-none position-fixed start-0 end-0 border-top shadow-lg p-3" style={{ bottom: '60px', zIndex: 1030, backgroundColor: 'var(--bs-body-bg)', borderTop: '1px solid rgba(var(--bs-body-color-rgb), 0.15)' }}>
          <div className="d-flex justify-content-between align-items-center">
            <div>
              <small className="text-muted d-block">{totalQty} Item</small>
              <h5 className="fw-bold text-primary mb-0">Rp {formatRupiah(totalPrice)}</h5>
            </div>
            <button className="btn btn-success fw-bold px-4" onClick={() => setShowOrderModal(true)}>
              Pesan <i className="bi bi-send-fill ms-1"></i>
            </button>
          </div>
        </div>
      )}

      {/* Customization Modal */}
      {showCustomModal && activeMenu && (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered modal-lg">
            <div className="modal-content" style={{ backgroundColor: 'var(--bg-card)', border: '1px solid var(--border-color)', borderRadius: 16 }}>
              <div className="modal-header border-bottom" style={{ borderColor: 'var(--border-color)' }}>
                <h5 className="modal-title">Kustomisasi {activeMenu.nama_menu}</h5>
                <button type="button" className="btn-close" onClick={() => setShowCustomModal(false)} style={{ filter: 'var(--theme-close-btn, invert(1))' }}></button>
              </div>
              <div className="modal-body" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                <div className="alert alert-info py-2" style={{ backgroundColor: 'rgba(13, 202, 240, 0.1)', borderColor: 'rgba(13, 202, 240, 0.2)', color: '#0dcaf0' }}>
                  Silakan tentukan pilihan rasa untuk <strong>Porsi #{(cart[activeMenu.id]?.qty || 0) + 1}</strong>.
                </div>
                
                {/* Lauk */}
                <div className="mb-4">
                  <label className="form-label fw-bold mb-2">
                    Pilih Jenis Lauk {activeMenu.wajib_pilih_lauk && <span className="text-danger">*</span>}
                    <span className="small text-muted fw-normal ms-1">(Maksimal {activeMenu.maksimal_lauk || 1})</span>
                  </label>
                  <div className="row g-2">
                    {activeMenu.options?.filter(o => o.tipe === 'lauk').map((opt, i) => {
                      const isAvail = opt.status === 'tersedia';
                      return (
                        <div className="col-sm-6 col-md-4" key={opt.id}>
                          <input 
                            type="checkbox" 
                            className="btn-check" 
                            id={`lauk_${i}`} 
                            value={opt.nama_opsi}
                            checked={customForm.jenis_lauk.includes(opt.nama_opsi)}
                            onChange={(e) => handleCustomFormChange(e, 'lauk', activeMenu.maksimal_lauk)}
                            disabled={!isAvail}
                          />
                          <label className="btn customise-option-btn w-100 d-flex align-items-center gap-2 py-2 px-3 text-start text-truncate" htmlFor={`lauk_${i}`}>
                            {opt.gambar ? (
                              <img src={`/storage/${opt.gambar}`} className="rounded" style={{ width: 32, height: 32, objectFit: 'cover', border: '1px solid var(--border-color)' }} alt="" />
                            ) : (
                              <div className="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style={{ width: 32, height: 32, minWidth: 32 }}>
                                <i className="bi bi-egg text-white" style={{ fontSize: '0.95rem' }}></i>
                              </div>
                            )}
                            <span className="text-truncate">{opt.nama_opsi}</span>
                            {!isAvail && <span className="badge bg-danger ms-auto">Habis</span>}
                          </label>
                        </div>
                      )
                    })}
                  </div>
                </div>

                {/* Sambal */}
                <div className="mb-4">
                  <label className="form-label fw-bold mb-2">Pilih Jenis Sambal {activeMenu.wajib_pilih_sambal && <span className="text-danger">*</span>}</label>
                  <div className="row g-2">
                    {activeMenu.options?.filter(o => o.tipe === 'sambal').map((opt, i) => {
                      const isAvail = opt.status === 'tersedia';
                      return (
                        <div className="col-sm-6 col-md-4" key={opt.id}>
                          <input 
                            type="radio" 
                            className="btn-check" 
                            name="sambal_radio"
                            id={`sambal_${i}`} 
                            value={opt.nama_opsi}
                            checked={customForm.jenis_sambal === opt.nama_opsi}
                            onChange={(e) => handleCustomFormChange(e, 'sambal')}
                            disabled={!isAvail}
                          />
                          <label className="btn customise-option-btn w-100 d-flex align-items-center gap-2 py-2 px-3 text-start text-truncate" htmlFor={`sambal_${i}`}>
                            {opt.gambar ? (
                              <img src={`/storage/${opt.gambar}`} className="rounded" style={{ width: 32, height: 32, objectFit: 'cover', border: '1px solid var(--border-color)' }} alt="" />
                            ) : (
                              <div className="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style={{ width: 32, height: 32, minWidth: 32 }}>
                                <i className="bi bi-fire text-white" style={{ fontSize: '0.95rem' }}></i>
                              </div>
                            )}
                            <span className="text-truncate">{opt.nama_opsi}</span>
                            {!isAvail && <span className="badge bg-danger ms-auto">Habis</span>}
                          </label>
                        </div>
                      )
                    })}
                  </div>
                </div>

                {/* Ekstra */}
                <div className="mb-3">
                  <label className="form-label fw-bold mb-2">Pilih Ekstra Lauk (Opsional)</label>
                  <div className="row g-2">
                    {activeMenu.options?.filter(o => o.tipe === 'ekstra_lauk').map((opt, i) => {
                      const isAvail = opt.status === 'tersedia';
                      return (
                        <div className="col-sm-6 col-md-4" key={opt.id}>
                          <input 
                            type="checkbox" 
                            className="btn-check" 
                            id={`ekstra_${i}`} 
                            value={opt.nama_opsi}
                            checked={customForm.ekstra_lauk.includes(opt.nama_opsi)}
                            onChange={(e) => handleCustomFormChange(e, 'ekstra')}
                            disabled={!isAvail}
                          />
                          <label className="btn customise-option-btn w-100 d-flex align-items-center gap-2 py-2 px-3 text-start text-truncate" htmlFor={`ekstra_${i}`}>
                            {opt.gambar ? (
                              <img src={`/storage/${opt.gambar}`} className="rounded" style={{ width: 32, height: 32, objectFit: 'cover', border: '1px solid var(--border-color)' }} alt="" />
                            ) : (
                              <div className="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style={{ width: 32, height: 32, minWidth: 32 }}>
                                <i className="bi bi-plus text-white" style={{ fontSize: '0.95rem' }}></i>
                              </div>
                            )}
                            <span className="text-truncate">{opt.nama_opsi}</span>
                            {!isAvail && <span className="badge bg-danger ms-auto">Habis</span>}
                          </label>
                        </div>
                      )
                    })}
                  </div>
                </div>

              </div>
              <div className="modal-footer border-top" style={{ borderColor: 'var(--border-color)' }}>
                <button type="button" className="btn btn-outline-secondary" onClick={() => setShowCustomModal(false)}>Batal</button>
                <button type="button" className="btn btn-success" onClick={saveCustomization}>Simpan Porsi</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Order Confirmation Modal */}
      {showOrderModal && (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content" style={{ backgroundColor: 'var(--bg-card)', border: '1px solid var(--border-color)', borderRadius: 16 }}>
              <div className="modal-header border-bottom" style={{ borderColor: 'var(--border-color)' }}>
                <h5 className="modal-title">Konfirmasi Pesanan</h5>
                <button type="button" className="btn-close" onClick={() => setShowOrderModal(false)} style={{ filter: 'var(--theme-close-btn, invert(1))' }}></button>
              </div>
              <div className="modal-body">
                <p className="text-muted mb-3">Rincian pesanan yang akan dibuat:</p>
                <div className="list-group list-group-flush mb-4" style={{ maxHeight: 200, overflowY: 'auto' }}>
                  {Object.entries(cart).map(([menuId, item]) => {
                    const menu = menus.find(m => m.id === parseInt(menuId));
                    if (!menu) return null;
                    
                    let itemTotal = 0;
                    if (item.customizations && item.customizations.length > 0) {
                      item.customizations.forEach(cust => {
                        itemTotal += calculatePortionPrice(menu.nama_menu, menu.harga, cust);
                      });
                      if (item.qty > item.customizations.length) {
                        itemTotal += (item.qty - item.customizations.length) * menu.harga;
                      }
                    } else {
                      itemTotal = item.qty * menu.harga;
                    }

                    return (
                      <div key={menuId} className="list-group-item px-0 py-2 bg-transparent border-0 border-bottom d-flex justify-content-between align-items-start">
                        <div style={{ flexGrow: 1, minWidth: 0 }}>
                          <span className="fw-semibold d-block text-truncate text-white">{menu.nama_menu}</span>
                          <small className="text-muted d-block">{item.qty}x @ Rp {formatRupiah(menu.harga)}</small>
                          {item.customizations.length > 0 && (
                            <div className="mt-1 small text-muted font-monospace" style={{ fontSize: '0.72rem', lineHeight: 1.3 }}>
                              {item.customizations.map((cust, i) => {
                                const linePrice = calculatePortionPrice(menu.nama_menu, menu.harga, cust);
                                const lauk = cust.lauk.length > 0 ? cust.lauk.join(', ') : 'Tidak ada';
                                const ekstra = cust.ekstra.length > 0 ? cust.ekstra.join(', ') : 'Tidak ada';
                                const text = `Porsi #${i+1} [Lauk: ${lauk} | Sambal: ${cust.sambal} | Ekstra: ${ekstra}]`;
                                return (
                                  <div key={i} className="text-truncate text-wrap mb-1" title={text}>
                                    <i className="bi bi-gear-wide-connected me-1 text-primary"></i> {text} <span className="text-success fw-bold">(Rp {formatRupiah(linePrice)})</span>
                                  </div>
                                );
                              })}
                            </div>
                          )}
                        </div>
                        <strong className="text-primary ms-2">Rp {formatRupiah(itemTotal)}</strong>
                      </div>
                    )
                  })}
                </div>

                <form onSubmit={submitOrder} id="finalOrderForm">
                  <div className="mb-3">
                    <label className="form-label fw-bold text-white">Atas Nama <span className="text-danger">*</span></label>
                    <input 
                      type="text" 
                      className="form-control" 
                      placeholder="Masukkan nama pemesan"
                      value={orderForm.atas_nama}
                      onChange={e => setOrderForm({...orderForm, atas_nama: e.target.value})}
                      required 
                    />
                  </div>
                  <div className="mb-3">
                    <label className="form-label fw-bold text-white">Tipe Pesanan <span className="text-danger">*</span></label>
                    <div className="d-flex gap-3">
                      <div className="form-check">
                        <input className="form-check-input" type="radio" name="tipe" id="dine_in" checked={orderForm.tipe_pesanan === 'dine_in'} onChange={() => setOrderForm({...orderForm, tipe_pesanan: 'dine_in'})} />
                        <label className="form-check-label" htmlFor="dine_in">
                          <i className="bi bi-shop me-1"></i> Dine In <small className="text-muted">(Makan di Tempat)</small>
                        </label>
                      </div>
                      <div className="form-check">
                        <input className="form-check-input" type="radio" name="tipe" id="take_away" checked={orderForm.tipe_pesanan === 'take_away'} onChange={() => setOrderForm({...orderForm, tipe_pesanan: 'take_away', nomor_meja: ''})} />
                        <label className="form-check-label" htmlFor="take_away">
                          <i className="bi bi-bag me-1"></i> Take Away <small className="text-muted">(Bungkus)</small>
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  {(!nomorMeja && orderForm.tipe_pesanan === 'dine_in') && (
                    <div className="mb-3">
                      <label className="form-label fw-bold text-white">Nomor Meja Anda <span className="text-danger">*</span></label>
                      <input 
                        type="text" 
                        className="form-control" 
                        placeholder="Contoh: 05"
                        value={orderForm.nomor_meja}
                        onChange={e => setOrderForm({...orderForm, nomor_meja: e.target.value})}
                        required 
                      />
                    </div>
                  )}

                  {(nomorMeja && orderForm.tipe_pesanan === 'dine_in') && (
                    <div className="mb-3">
                      <div className="alert alert-success d-flex align-items-center mb-0 py-2">
                        <i className="bi bi-qr-code-scan fs-4 me-3"></i>
                        <div>
                          <span className="d-block small">Nomor Meja Otomatis:</span>
                          <strong className="fs-5">{nomorMeja}</strong>
                        </div>
                      </div>
                    </div>
                  )}
                  
                  <div className="d-flex justify-content-between align-items-center pt-3 border-top mt-4" style={{ borderColor: 'var(--border-color)' }}>
                    <span className="text-muted">Total Pembayaran:</span>
                    <h4 className="text-primary fw-bold mb-0">Rp {formatRupiah(totalPrice)}</h4>
                  </div>
                </form>
              </div>
              <div className="modal-footer border-top" style={{ borderColor: 'var(--border-color)' }}>
                <button type="button" className="btn btn-outline-secondary" onClick={() => setShowOrderModal(false)}>Batal</button>
                <button type="submit" form="finalOrderForm" className="btn btn-success" id="btnConfirmOrderSubmit">Konfirmasi & Pesan</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
