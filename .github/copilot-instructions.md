# Copilot Instructions — DeCafe Laravel

## 1. Identitas Project

Nama project: **DeCafe**

Jenis aplikasi: **Website pemesanan makanan dan minuman berbasis Laravel**

Framework utama:
- Laravel
- Blade Template
- MySQL
- Laravel Migration
- Laravel Seeder
- Laravel Middleware
- Laravel Controller
- Laravel Eloquent Model

Frontend:
- Blade
- Bootstrap atau Tailwind CSS
- JavaScript ringan jika diperlukan

Jangan menggunakan:
- React
- Next.js
- Vue
- API mobile
- SPA penuh
- Framework frontend berat

Fokus project ini adalah **website Laravel fullstack sederhana, rapi, dan siap dikembangkan**.

---

## 2. Deskripsi Aplikasi

DeCafe adalah aplikasi website untuk membantu proses pemesanan makanan dan minuman pada cafe, restoran, rumah makan, atau usaha sejenis.

Aplikasi ini memiliki 4 role utama:

1. **Admin/Pemilik**
2. **Kasir**
3. **Pelayan**
4. **Dapur**

Setiap role memiliki dashboard dan fitur yang berbeda sesuai tanggung jawabnya.

---

## 3. Prinsip Utama untuk Copilot

Saat membuat kode Laravel, ikuti aturan berikut:

- Gunakan struktur Laravel standar.
- Jangan menaruh semua logic di route.
- Gunakan Controller untuk proses bisnis.
- Gunakan Model dan Eloquent Relationship.
- Gunakan Migration untuk struktur database.
- Gunakan Seeder untuk data awal.
- Gunakan Middleware untuk pembatasan role.
- Gunakan Blade layout agar tampilan tidak berulang.
- Gunakan validasi Laravel Request atau `$request->validate()`.
- Gunakan session flash message untuk notifikasi sukses/gagal.
- Gunakan transaksi database untuk proses pesanan dan pembayaran.
- Jangan percaya harga dari frontend.
- Total harga harus dihitung ulang di backend.
- Password wajib menggunakan Hash.
- Gunakan bahasa Indonesia pada tampilan aplikasi.
- Buat kode yang mudah dibaca mahasiswa dan mudah dijelaskan saat presentasi.

---

## 4. Struktur Folder yang Disarankan

Gunakan struktur seperti berikut:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── MenuController.php
│   │   │   ├── UserController.php
│   │   │   └── ReportController.php
│   │   ├── Pelayan/
│   │   │   ├── DashboardController.php
│   │   │   └── OrderController.php
│   │   ├── Dapur/
│   │   │   ├── DashboardController.php
│   │   │   └── KitchenOrderController.php
│   │   └── Kasir/
│   │       ├── DashboardController.php
│   │       ├── CashierOrderController.php
│   │       └── PaymentController.php
│   └── Middleware/
│       └── RoleMiddleware.php
│
├── Models/
│   ├── User.php
│   ├── Menu.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── Payment.php
```

View Blade:

```text
resources/views/
├── layouts/
│   ├── app.blade.php
│   ├── sidebar.blade.php
│   └── navbar.blade.php
│
├── auth/
│   └── login.blade.php
│
├── admin/
│   ├── dashboard.blade.php
│   ├── menus/
│   ├── users/
│   └── reports/
│
├── pelayan/
│   ├── dashboard.blade.php
│   └── orders/
│
├── dapur/
│   ├── dashboard.blade.php
│   └── orders/
│
└── kasir/
    ├── dashboard.blade.php
    ├── orders/
    └── payments/
```

---

## 5. Role dan Hak Akses

### A. Admin/Pemilik

Admin memiliki akses tertinggi.

Fitur admin:
- Melihat dashboard admin.
- Melihat laporan penjualan.
- Menambah menu.
- Mengubah menu.
- Menghapus menu.
- Mengatur harga menu.
- Mengatur status menu.
- Menambah user.
- Mengubah user.
- Menghapus user.
- Mengatur role user.

Admin dapat melihat:
- Total menu.
- Total user.
- Total pesanan hari ini.
- Total pendapatan hari ini.
- Total transaksi.
- Pesanan selesai.
- Pesanan belum dibayar.
- Menu terlaris.
- Laporan harian, mingguan, bulanan.

---

### B. Kasir

Kasir menangani pembayaran.

Fitur kasir:
- Melihat daftar pesanan.
- Melihat detail pesanan.
- Melihat total harga.
- Mengonfirmasi pembayaran.
- Memilih metode pembayaran.
- Input uang diterima.
- Menghitung kembalian.
- Membuat bukti pembayaran/struk.
- Melihat riwayat pembayaran.

Kasir tidak boleh:
- Mengelola menu.
- Mengelola user.
- Mengubah pesanan dapur.
- Menghapus pesanan.

---

### C. Pelayan

Pelayan membuat pesanan pelanggan.

Fitur pelayan:
- Melihat menu dan harga.
- Memilih menu.
- Menentukan jumlah pesanan.
- Melihat total harga.
- Membuat pesanan.
- Update pesanan jika masih `menunggu`.
- Batalkan pesanan jika masih `menunggu`.
- Melihat status pesanan.

Pelayan tidak boleh:
- Mengonfirmasi pembayaran.
- Mengelola user.
- Mengubah harga menu.
- Mengubah status dapur menjadi siap saji.
- Melihat laporan keuangan penuh.

---

### D. Dapur

Dapur memproses pesanan.

Fitur dapur:
- Melihat pesanan masuk.
- Melihat detail item pesanan.
- Konfirmasi pesanan diterima.
- Mengubah status menjadi `diproses`.
- Mengubah status menjadi `siap_saji`.

Dapur tidak boleh:
- Mengubah harga menu.
- Menghapus pesanan.
- Mengonfirmasi pembayaran.
- Mengelola user.
- Melihat laporan pendapatan.

---

## 6. Database Laravel

Buat migration untuk tabel berikut.

---

### A. users

Gunakan tabel users default Laravel, lalu tambahkan kolom:

```php
$table->enum('role', ['admin', 'kasir', 'pelayan', 'dapur'])->default('pelayan');
```

Kolom utama:
- id
- name
- email
- password
- role
- timestamps

---

### B. menus

Kolom:

```php
$table->id();
$table->string('nama_menu');
$table->enum('kategori', ['makanan', 'minuman']);
$table->text('deskripsi')->nullable();
$table->integer('harga');
$table->integer('stok')->default(0);
$table->enum('status', ['tersedia', 'habis', 'nonaktif'])->default('tersedia');
$table->string('gambar')->nullable();
$table->timestamps();
```

---

### C. orders

Kolom:

```php
$table->id();
$table->string('kode_pesanan')->unique();
$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
$table->string('nama_pelanggan')->nullable();
$table->string('nomor_meja');
$table->integer('total_harga')->default(0);
$table->enum('status_pesanan', [
    'menunggu',
    'diterima_dapur',
    'diproses',
    'siap_saji',
    'selesai',
    'dibatalkan'
])->default('menunggu');
$table->enum('status_pembayaran', [
    'belum_bayar',
    'lunas',
    'dibatalkan'
])->default('belum_bayar');
$table->text('catatan')->nullable();
$table->timestamps();
```

---

### D. order_items

Kolom:

```php
$table->id();
$table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
$table->foreignId('menu_id')->constrained('menus')->restrictOnDelete();
$table->string('nama_menu');
$table->integer('harga');
$table->integer('jumlah');
$table->integer('subtotal');
$table->text('catatan_item')->nullable();
$table->timestamps();
```

Catatan penting:
- Simpan `nama_menu` dan `harga` ke order_items saat transaksi.
- Jangan hanya bergantung pada tabel menus, karena harga menu bisa berubah di masa depan.

---

### E. payments

Kolom:

```php
$table->id();
$table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
$table->foreignId('kasir_id')->constrained('users')->cascadeOnDelete();
$table->string('kode_pembayaran')->unique();
$table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer', 'ewallet']);
$table->integer('total_bayar');
$table->integer('uang_diterima')->nullable();
$table->integer('kembalian')->default(0);
$table->enum('status', ['lunas', 'dibatalkan'])->default('lunas');
$table->timestamp('paid_at')->nullable();
$table->timestamps();
```

---

## 7. Relasi Model Laravel

Buat relasi Eloquent berikut.

### User.php

```php
public function orders()
{
    return $this->hasMany(Order::class);
}

public function payments()
{
    return $this->hasMany(Payment::class, 'kasir_id');
}
```

---

### Menu.php

```php
public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
```

---

### Order.php

```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function items()
{
    return $this->hasMany(OrderItem::class);
}

public function payment()
{
    return $this->hasOne(Payment::class);
}
```

---

### OrderItem.php

```php
public function order()
{
    return $this->belongsTo(Order::class);
}

public function menu()
{
    return $this->belongsTo(Menu::class);
}
```

---

### Payment.php

```php
public function order()
{
    return $this->belongsTo(Order::class);
}

public function kasir()
{
    return $this->belongsTo(User::class, 'kasir_id');
}
```

---

## 8. Middleware Role

Buat middleware:

```bash
php artisan make:middleware RoleMiddleware
```

Logic middleware:

```php
public function handle($request, Closure $next, ...$roles)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (!in_array(auth()->user()->role, $roles)) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    return $next($request);
}
```

Daftarkan middleware di Laravel sesuai versi yang digunakan.

Gunakan middleware pada route:

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // route admin
});
```

---

## 9. Route Laravel

Gunakan `routes/web.php`.

Contoh struktur route:

```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Pelayan\DashboardController as PelayanDashboardController;
use App\Http\Controllers\Pelayan\OrderController;
use App\Http\Controllers\Dapur\DashboardController as DapurDashboardController;
use App\Http\Controllers\Dapur\KitchenOrderController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\CashierOrderController;
use App\Http\Controllers\Kasir\PaymentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/menus', MenuController::class);
    Route::resource('/users', UserController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

Route::middleware(['auth', 'role:pelayan'])->prefix('pelayan')->name('pelayan.')->group(function () {
    Route::get('/dashboard', [PelayanDashboardController::class, 'index'])->name('dashboard');
    Route::resource('/orders', OrderController::class);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

Route::middleware(['auth', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {
    Route::get('/dashboard', [DapurDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [KitchenOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [KitchenOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [KitchenOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/process', [KitchenOrderController::class, 'process'])->name('orders.process');
    Route::post('/orders/{order}/ready', [KitchenOrderController::class, 'ready'])->name('orders.ready');
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [CashierOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [CashierOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
});
```

---

## 10. Authentication

Buat AuthController manual sederhana.

Fitur:
- Menampilkan halaman login.
- Proses login.
- Redirect berdasarkan role.
- Logout.

Redirect setelah login:
- admin ke `/admin/dashboard`
- kasir ke `/kasir/dashboard`
- pelayan ke `/pelayan/dashboard`
- dapur ke `/dapur/dashboard`

Gunakan:

```php
Auth::attempt()
Auth::logout()
$request->session()->regenerate()
$request->session()->invalidate()
$request->session()->regenerateToken()
```

---

## 11. Alur Pemesanan

1. Pelayan login.
2. Pelayan membuka dashboard.
3. Pelayan melihat daftar menu tersedia.
4. Pelayan memilih menu.
5. Pelayan mengisi jumlah.
6. Sistem menghitung subtotal.
7. Sistem menghitung total harga.
8. Pelayan mengisi nomor meja dan nama pelanggan.
9. Pelayan menyimpan pesanan.
10. Sistem menyimpan data ke tabel orders dan order_items.
11. Pesanan masuk ke dashboard dapur dengan status `menunggu`.

Saat menyimpan pesanan:
- Gunakan DB transaction.
- Validasi minimal ada 1 item.
- Ambil harga dari database menus.
- Hitung subtotal di backend.
- Hitung total harga di backend.
- Generate `kode_pesanan` otomatis.

Contoh format kode pesanan:

```text
ORD-20260521-0001
```

---

## 12. Alur Dapur

1. Dapur login.
2. Dapur melihat pesanan berstatus `menunggu`, `diterima_dapur`, dan `diproses`.
3. Dapur membuka detail pesanan.
4. Dapur klik `Terima Pesanan`.
5. Status berubah menjadi `diterima_dapur`.
6. Dapur klik `Proses Pesanan`.
7. Status berubah menjadi `diproses`.
8. Dapur klik `Siap Saji`.
9. Status berubah menjadi `siap_saji`.

Aturan:
- Jangan izinkan loncat status secara sembarangan.
- Jangan ubah pesanan yang sudah selesai.
- Jangan ubah pesanan yang dibatalkan.

---

## 13. Alur Pembayaran Kasir

1. Kasir login.
2. Kasir melihat daftar pesanan dengan status `siap_saji` atau belum lunas.
3. Kasir membuka detail pesanan.
4. Kasir melihat total harga.
5. Kasir memilih metode pembayaran.
6. Jika metode tunai, kasir input uang diterima.
7. Sistem menghitung kembalian.
8. Kasir mengonfirmasi pembayaran.
9. Sistem menyimpan data ke tabel payments.
10. Sistem mengubah status pembayaran menjadi `lunas`.
11. Sistem mengubah status pesanan menjadi `selesai`.
12. Sistem menampilkan bukti pembayaran/struk.

Saat pembayaran:
- Gunakan DB transaction.
- Cek apakah pesanan sudah lunas.
- Jangan boleh membayar pesanan yang sudah dibayar.
- Total bayar diambil dari `orders.total_harga`.
- Untuk tunai, uang diterima harus >= total harga.
- Kembalian = uang diterima - total harga.
- Generate `kode_pembayaran` otomatis.

Contoh format kode pembayaran:

```text
PAY-20260521-0001
```

---

## 14. Halaman Blade yang Wajib Ada

### Auth

```text
resources/views/auth/login.blade.php
```

---

### Layout

```text
resources/views/layouts/app.blade.php
resources/views/layouts/sidebar.blade.php
resources/views/layouts/navbar.blade.php
```

Layout harus:
- Menampilkan sidebar sesuai role.
- Menampilkan nama user login.
- Menampilkan tombol logout.
- Memiliki area `@yield('content')`.

---

### Admin

```text
resources/views/admin/dashboard.blade.php
resources/views/admin/menus/index.blade.php
resources/views/admin/menus/create.blade.php
resources/views/admin/menus/edit.blade.php
resources/views/admin/menus/show.blade.php

resources/views/admin/users/index.blade.php
resources/views/admin/users/create.blade.php
resources/views/admin/users/edit.blade.php
resources/views/admin/users/show.blade.php

resources/views/admin/reports/index.blade.php
```

---

### Pelayan

```text
resources/views/pelayan/dashboard.blade.php
resources/views/pelayan/orders/index.blade.php
resources/views/pelayan/orders/create.blade.php
resources/views/pelayan/orders/edit.blade.php
resources/views/pelayan/orders/show.blade.php
```

---

### Dapur

```text
resources/views/dapur/dashboard.blade.php
resources/views/dapur/orders/index.blade.php
resources/views/dapur/orders/show.blade.php
```

---

### Kasir

```text
resources/views/kasir/dashboard.blade.php
resources/views/kasir/orders/index.blade.php
resources/views/kasir/orders/show.blade.php
resources/views/kasir/payments/create.blade.php
resources/views/kasir/payments/receipt.blade.php
```

---

## 15. Tampilan UI

Gunakan gaya cafe sederhana.

Warna:
- Coklat kopi
- Cream
- Putih
- Abu gelap
- Hijau untuk sukses
- Merah untuk hapus/batal
- Kuning/oranye untuk proses

Komponen:
- Sidebar kiri
- Navbar atas
- Card dashboard
- Tabel data
- Badge status
- Tombol aksi
- Form input
- Modal konfirmasi jika diperlukan
- Tampilan struk print-friendly

Label tombol contoh:
- Simpan
- Tambah
- Ubah
- Hapus
- Batalkan
- Terima Pesanan
- Proses Pesanan
- Siap Saji
- Konfirmasi Pembayaran
- Cetak Struk

---

## 16. Seeder Data Awal

Buat seeder untuk user:

```text
Admin
email: admin@decafe.test
password: password
role: admin

Kasir
email: kasir@decafe.test
password: password
role: kasir

Pelayan
email: pelayan@decafe.test
password: password
role: pelayan

Dapur
email: dapur@decafe.test
password: password
role: dapur
```

Gunakan:

```php
Hash::make('password')
```

Seeder menu:

```text
Kopi Susu - minuman - 15000 - stok 50
Americano - minuman - 12000 - stok 50
Es Teh - minuman - 5000 - stok 50
Nasi Goreng - makanan - 18000 - stok 30
Mie Goreng - makanan - 16000 - stok 30
Kentang Goreng - makanan - 12000 - stok 30
```

---

## 17. Laporan Admin

Buat halaman laporan dengan fitur:

- Filter tanggal awal dan tanggal akhir.
- Total transaksi selesai.
- Total pendapatan.
- Daftar transaksi.
- Menu terlaris berdasarkan jumlah terjual.
- Total pesanan berdasarkan status.

Query laporan hanya mengambil pesanan:
- status_pesanan = `selesai`
- status_pembayaran = `lunas`

Jangan hitung pesanan batal sebagai pendapatan.

---

## 18. Bukti Pembayaran / Struk

Buat halaman struk yang bisa dicetak.

Isi struk:
- Nama aplikasi: DeCafe
- Kode pembayaran
- Kode pesanan
- Nama pelanggan
- Nomor meja
- Nama kasir
- Tanggal pembayaran
- Daftar item pesanan
- Harga item
- Jumlah
- Subtotal
- Total harga
- Metode pembayaran
- Uang diterima
- Kembalian

Tambahkan tombol:

```text
Cetak Struk
Kembali ke Kasir
```

Gunakan `window.print()` untuk cetak sederhana.

---

## 19. Validasi

Gunakan validasi Laravel.

### Validasi Menu

```php
'nama_menu' => 'required|string|max:255',
'kategori' => 'required|in:makanan,minuman',
'harga' => 'required|integer|min:0',
'stok' => 'required|integer|min:0',
'status' => 'required|in:tersedia,habis,nonaktif',
'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
```

### Validasi User

```php
'name' => 'required|string|max:255',
'email' => 'required|email|unique:users,email',
'password' => 'required|min:6',
'role' => 'required|in:admin,kasir,pelayan,dapur',
```

### Validasi Pesanan

```php
'nomor_meja' => 'required|string|max:50',
'nama_pelanggan' => 'nullable|string|max:255',
'items' => 'required|array|min:1',
'items.*.menu_id' => 'required|exists:menus,id',
'items.*.jumlah' => 'required|integer|min:1',
```

### Validasi Pembayaran

```php
'metode_pembayaran' => 'required|in:tunai,qris,transfer,ewallet',
'uang_diterima' => 'nullable|integer|min:0',
```

Jika metode pembayaran tunai, `uang_diterima` wajib dan harus lebih besar atau sama dengan total harga.

---

## 20. Aturan Bisnis Penting

- Menu dengan status `habis` atau `nonaktif` tidak boleh dipesan.
- Stok menu harus berkurang saat pesanan dibuat.
- Jika pesanan dibatalkan, stok dikembalikan.
- Pelayan hanya boleh membatalkan pesanan dengan status `menunggu`.
- Dapur tidak boleh memproses pesanan yang dibatalkan.
- Kasir tidak boleh membayar pesanan yang dibatalkan.
- Kasir tidak boleh membayar pesanan yang sudah lunas.
- Pembayaran membuat order menjadi `selesai`.
- Pesanan selesai tidak boleh diedit atau dihapus sembarangan.

---

## 21. Perintah Artisan yang Perlu Dibuat

Saat membangun project, gunakan perintah berikut jika belum ada:

```bash
php artisan make:model Menu -m
php artisan make:model Order -m
php artisan make:model OrderItem -m
php artisan make:model Payment -m

php artisan make:controller AuthController

php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/MenuController --resource
php artisan make:controller Admin/UserController --resource
php artisan make:controller Admin/ReportController

php artisan make:controller Pelayan/DashboardController
php artisan make:controller Pelayan/OrderController --resource

php artisan make:controller Dapur/DashboardController
php artisan make:controller Dapur/KitchenOrderController

php artisan make:controller Kasir/DashboardController
php artisan make:controller Kasir/CashierOrderController
php artisan make:controller Kasir/PaymentController

php artisan make:middleware RoleMiddleware

php artisan make:seeder UserSeeder
php artisan make:seeder MenuSeeder
```

---

## 22. Urutan Pengerjaan

Kerjakan secara bertahap:

1. Buat project Laravel.
2. Konfigurasi database `.env`.
3. Buat migration.
4. Buat model dan relasi.
5. Buat seeder user dan menu.
6. Buat login/logout.
7. Buat middleware role.
8. Buat layout Blade.
9. Buat dashboard tiap role.
10. Buat CRUD menu admin.
11. Buat CRUD user admin.
12. Buat fitur pelayan membuat pesanan.
13. Buat fitur dapur update status pesanan.
14. Buat fitur kasir pembayaran.
15. Buat struk pembayaran.
16. Buat laporan admin.
17. Rapikan tampilan.
18. Uji semua alur.

---

## 23. Checklist Testing Manual

Pastikan skenario berikut berhasil:

- Login admin berhasil.
- Login kasir berhasil.
- Login pelayan berhasil.
- Login dapur berhasil.
- User tidak bisa masuk dashboard role lain.
- Admin bisa tambah menu.
- Admin bisa edit menu.
- Admin bisa hapus menu.
- Admin bisa tambah user.
- Pelayan bisa membuat pesanan.
- Total harga pesanan benar.
- Pesanan masuk ke dapur.
- Dapur bisa terima pesanan.
- Dapur bisa ubah status menjadi diproses.
- Dapur bisa ubah status menjadi siap saji.
- Kasir bisa melihat pesanan siap saji.
- Kasir bisa konfirmasi pembayaran.
- Kembalian tunai benar.
- Struk pembayaran muncul.
- Laporan admin menghitung pendapatan dari pesanan lunas saja.
- Pesanan batal tidak masuk pendapatan.
- Stok berkurang saat pesanan dibuat.
- Stok kembali jika pesanan dibatalkan.

---

## 24. Catatan Penting untuk Agent

Jangan membuat aplikasi terlalu kompleks. Fokus pada aplikasi Laravel yang:

- Bisa login.
- Ada pembagian role.
- Bisa CRUD menu.
- Bisa CRUD user.
- Bisa membuat pesanan.
- Bisa memproses pesanan di dapur.
- Bisa membayar pesanan di kasir.
- Bisa mencetak struk.
- Bisa melihat laporan.

Utamakan alur utama berjalan lancar daripada fitur tambahan yang belum dibutuhkan.
