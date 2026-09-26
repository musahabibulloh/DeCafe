import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import supabase from '../lib/supabase';

export default function Login() {
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');
  const navigate = useNavigate();

  const handleLogin = async (e) => {
    e.preventDefault();
    setErrorMsg('');
    setLoading(true);

    const email = e.target.email.value;
    const password = e.target.password.value;

    try {
      const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password,
      });

      if (error) {
        throw error;
      }

      // Fetch user role from public.users (without .single() to avoid PGRST116 if empty)
      const { data: userData, error: userError } = await supabase
        .from('users')
        .select('role')
        .eq('id', data.user.id);

      if (userError) {
        throw new Error('Database error: ' + userError.message);
      }

      let role = 'admin'; // fallback default if somehow not in public.users
      if (userData && userData.length > 0) {
        role = userData[0].role;
      } else {
        console.warn('User not found in public.users, defaulting to admin for fallback.');
      }
      
      if (role === 'admin') {
        navigate('/dashboard');
      } else if (role === 'kasir') {
        navigate('/kasir/orders');
      } else if (role === 'pelayan') {
        navigate('/pelayan/orders');
      } else if (role === 'dapur') {
        navigate('/dapur/orders');
      } else {
        // Fallback or customer (customer usually logs in via QR, not here)
        navigate('/customer/dashboard');
      }
    } catch (err) {
      console.error(err);
      if (err.message.includes('Invalid login credentials')) {
        setErrorMsg('Email atau password salah.');
      } else {
        setErrorMsg('Gagal login: ' + err.message);
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div 
      className="d-flex align-items-center justify-content-center" 
      style={{ 
        minHeight: '100vh',
        background: 'linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.85)), url("/background.avif") no-repeat center center fixed',
        backgroundSize: 'cover'
      }}
    >
      <style>
        {`
          .login-card {
              max-width: 440px;
              width: 100%;
              background-color: rgba(16, 15, 22, 0.9) !important;
              backdrop-filter: blur(20px);
              border: 1px solid var(--border-color) !important;
              border-radius: 20px !important;
              box-shadow: 0 15px 45px rgba(0, 0, 0, 0.6) !important;
              margin: 20px;
          }
          .brand-title {
              font-size: 2.2rem;
              font-weight: 800;
              background: var(--primary-gradient);
              -webkit-background-clip: text;
              -webkit-text-fill-color: transparent;
              letter-spacing: 1.5px;
          }
          .login-form-control {
              background-color: rgba(255, 255, 255, 0.1) !important;
              border: 1px solid var(--border-color) !important;
              color: #fff !important;
              border-radius: 10px !important;
              padding: 0.7rem 1.1rem !important;
              transition: all 0.25s ease !important;
          }
          .login-form-control:focus {
              background-color: rgba(255, 255, 255, 0.14) !important;
              border-color: var(--primary-color) !important;
              box-shadow: 0 0 0 3px rgba(224, 158, 57, 0.25) !important;
              color: #fff !important;
          }
          .login-btn-primary {
              background: var(--primary-gradient) !important;
              border: none !important;
              color: #fff !important;
              padding: 0.7rem 1.5rem !important;
              border-radius: 10px !important;
              font-weight: 600 !important;
              box-shadow: 0 4px 15px rgba(224, 158, 57, 0.3) !important;
              transition: all 0.25s ease !important;
          }
          .login-btn-primary:hover {
              opacity: 0.95;
              transform: translateY(-1px);
              box-shadow: 0 6px 20px rgba(224, 158, 57, 0.5) !important;
          }
        `}
      </style>
      <div className="card login-card shadow">
        <div className="card-body p-5">
          <div className="text-center mb-4">
            <span className="brand-title">Nasi Bakar Cak Win</span>
            <p className="text-muted mt-1 mb-0">Silakan login untuk masuk ke aplikasi</p>
          </div>
          
          {errorMsg && (
            <div className="alert alert-danger py-2 small border-0" style={{ backgroundColor: 'rgba(220, 53, 69, 0.1)', color: '#ea868f' }}>
              <i className="bi bi-exclamation-triangle-fill me-2"></i>{errorMsg}
            </div>
          )}
          
          <form onSubmit={handleLogin}>
            <div className="mb-3 text-start">
              <label htmlFor="email" className="form-label" style={{ color: 'var(--text-main)', fontWeight: 500, fontSize: '0.9rem', marginBottom: '0.4rem' }}>Email</label>
              <input 
                type="email" 
                name="email" 
                id="email" 
                className="form-control login-form-control" 
                required 
                autoFocus 
              />
            </div>
            
            <div className="mb-3 text-start">
              <label htmlFor="password" className="form-label" style={{ color: 'var(--text-main)', fontWeight: 500, fontSize: '0.9rem', marginBottom: '0.4rem' }}>Password</label>
              <div className="input-group">
                <input 
                  type={showPassword ? "text" : "password"} 
                  name="password" 
                  id="password" 
                  className="form-control login-form-control" 
                  style={{ borderTopRightRadius: 0, borderBottomRightRadius: 0 }}
                  required 
                />
                <button 
                  className="btn btn-outline-secondary" 
                  type="button" 
                  onClick={() => setShowPassword(!showPassword)}
                  style={{ 
                    border: '1px solid var(--border-color)', 
                    borderLeft: 'none', 
                    backgroundColor: 'rgba(255, 255, 255, 0.1)', 
                    color: 'var(--text-muted)', 
                    borderRadius: '0 10px 10px 0', 
                    padding: '0.7rem 1.1rem', 
                    transition: 'all 0.2s ease' 
                  }}
                >
                  <i className={`bi ${showPassword ? 'bi-eye' : 'bi-eye-slash'}`}></i>
                </button>
              </div>
            </div>
            
            <button type="submit" className="btn login-btn-primary w-100 mt-2" disabled={loading}>
              {loading ? 'Memeriksa...' : 'Login'}
            </button>

            <div className="d-flex align-items-center my-4">
              <hr className="flex-grow-1" style={{ borderColor: 'var(--border-color)', opacity: 0.2 }} />
              <span className="mx-3 text-muted small" style={{ opacity: 0.7 }}>atau</span>
              <hr className="flex-grow-1" style={{ borderColor: 'var(--border-color)', opacity: 0.2 }} />
            </div>

            <button 
              type="button" 
              className="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2" 
              style={{ 
                border: '1px solid var(--border-color)', 
                borderRadius: '10px', 
                backgroundColor: 'rgba(255, 255, 255, 0.08)', 
                color: 'var(--text-main)', 
                fontWeight: 500, 
                transition: 'background-color 0.2s ease' 
              }}
              onMouseOver={(e) => e.currentTarget.style.backgroundColor = 'rgba(255, 255, 255, 0.15)'}
              onMouseOut={(e) => e.currentTarget.style.backgroundColor = 'rgba(255, 255, 255, 0.08)'}
            >
              <svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
                <g transform="matrix(1, 0, 0, 1, 0, 0)">
                  <path d="M21.35,11.1H12v2.7h5.38c-0.24,1.28 -0.96,2.37 -2.05,3.1v2.58h3.31c1.94,-1.78 3.06,-4.41 3.06,-7.48c0,-0.6 -0.05,-1.18 -0.15,-1.7H21.35z" fill="#4285F4" />
                  <path d="M12,20.6c2.43,0 4.47,-0.8 5.96,-2.2l-2.91,-2.26c-0.8,0.54 -1.84,0.87 -3.05,0.87c-2.35,0 -4.33,-1.58 -5.04,-3.72H3.54v2.66C5.03,18.94 8.27,20.6 12,20.6z" fill="#34A853" />
                  <path d="M6.96,13.29c-0.18,-0.54 -0.28,-1.11 -0.28,-1.7c0,-0.59 0.1,-1.16 0.28,-1.7V7.23H3.54C2.93,8.45 2.58,9.83 2.58,11.3c0,1.47 0.35,2.85 0.96,4.07l2.67,-2.08H6.96z" fill="#FBBC05" />
                  <path d="M12,5.39c1.32,0 2.51,0.45 3.44,1.34l2.58,-2.58C16.47,2.77 14.43,1.99 12,1.99c-3.73,0 -6.97,1.66 -8.46,4.58l3.42,2.66c0.71,-2.14 2.69,-3.72 5.04,-3.72z" fill="#EA4335" />
                </g>
              </svg>
              Masuk dengan Google
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
