import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import Login from './pages/Login';
import DashboardLayout from './layouts/DashboardLayout';
import CustomerLayout from './layouts/CustomerLayout';
import AdminDashboard from './pages/admin/Dashboard';
import UserIndex from './pages/admin/users/Index';
import UserCreate from './pages/admin/users/Create';
import UserEdit from './pages/admin/users/Edit';
import MenuIndex from './pages/admin/menus/Index';
import MenuCreate from './pages/admin/menus/Create';
import MenuEdit from './pages/admin/menus/Edit';
import MejaIndex from './pages/admin/meja/Index';
import ReportsIndex from './pages/admin/reports/Index';

import KasirOrdersIndex from './pages/kasir/orders/Index';
import KasirOrdersShow from './pages/kasir/orders/Show';
import KasirPaymentsCreate from './pages/kasir/payments/Create';
import KasirPaymentsReceipt from './pages/kasir/payments/Receipt';

import PelayanOrdersIndex from './pages/pelayan/orders/Index';
import PelayanOrdersShow from './pages/pelayan/orders/Show';
import PelayanOrdersCreate from './pages/pelayan/orders/Create';
import PelayanOrdersEdit from './pages/pelayan/orders/Edit';

import DapurOrdersIndex from './pages/dapur/orders/Index';
import DapurOrdersShow from './pages/dapur/orders/Show';

import CustomerDashboard from './pages/customer/Dashboard';
import CustomerMenus from './pages/customer/Menus';
import CustomerOrdersIndex from './pages/customer/orders/Index';
import CustomerOrdersShow from './pages/customer/orders/Show';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Navigate to="/login" replace />} />
        <Route path="/login" element={<Login />} />
        
        {/* Dashboard Routes with Layout */}
        <Route element={<DashboardLayout />}>
          <Route path="/dashboard" element={<AdminDashboard />} />
          <Route path="/admin/users" element={<UserIndex />} />
          <Route path="/admin/users/create" element={<UserCreate />} />
          <Route path="/admin/users/:id/edit" element={<UserEdit />} />
          <Route path="/admin/menus" element={<MenuIndex />} />
          <Route path="/admin/menus/create" element={<MenuCreate />} />
          <Route path="/admin/menus/:id/edit" element={<MenuEdit />} />
          <Route path="/admin/meja" element={<MejaIndex />} />
          <Route path="/admin/reports" element={<ReportsIndex />} />

          {/* Kasir Routes */}
          <Route path="/kasir" element={<Navigate to="/kasir/orders" replace />} />
          <Route path="/kasir/orders" element={<KasirOrdersIndex />} />
          <Route path="/kasir/orders/:id" element={<KasirOrdersShow />} />
          <Route path="/kasir/orders/:id/payment" element={<KasirPaymentsCreate />} />
          <Route path="/kasir/orders/:id/receipt" element={<KasirPaymentsReceipt />} />

          {/* Pelayan Routes */}
          <Route path="/pelayan" element={<Navigate to="/pelayan/orders" replace />} />
          <Route path="/pelayan/orders" element={<PelayanOrdersIndex />} />
          <Route path="/pelayan/orders/create" element={<PelayanOrdersCreate />} />
          <Route path="/pelayan/orders/:id" element={<PelayanOrdersShow />} />
          <Route path="/pelayan/orders/:id/edit" element={<PelayanOrdersEdit />} />

          {/* Dapur Routes */}
          <Route path="/dapur" element={<Navigate to="/dapur/orders" replace />} />
          <Route path="/dapur/orders" element={<DapurOrdersIndex />} />
          <Route path="/dapur/orders/:id" element={<DapurOrdersShow />} />
        </Route>

        {/* Customer Routes with Customer Layout */}
        <Route element={<CustomerLayout />}>
          <Route path="/customer/dashboard" element={<CustomerDashboard />} />
          <Route path="/customer/menus" element={<CustomerMenus />} />
          <Route path="/customer/orders" element={<CustomerOrdersIndex />} />
          <Route path="/customer/orders/:id" element={<CustomerOrdersShow />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
