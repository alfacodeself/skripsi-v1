# 🚀 Sistem Pembayaran Provider Internet

<div align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  <br>
  <img src="https://filamentphp.com/images/og-image.png" width="200" alt="Filament Logo">
  <img src="https://midtrans.com/assets/images/midtrans-logo.png" width="200" alt="Midtrans Logo">
</div>

## 📋 Tentang Aplikasi

Aplikasi ini merupakan sistem pembayaran provider internet yang dikembangkan sebagai proyek skripsi. Dibangun menggunakan teknologi modern:

### 🛠️ Teknologi Utama
- **Laravel 11** - Framework PHP modern untuk backend
- **Filament 3** - Admin panel yang powerful dan mudah digunakan
- **Midtrans** - Payment gateway terpercaya di Indonesia
- **MySQL** - Database relasional yang handal

### ✨ Fitur Utama
- 📱 Manajemen pelanggan dan paket internet
- 💰 Pembuatan tagihan otomatis
- 💳 Integrasi pembayaran dengan Midtrans
- 📊 Laporan keuangan
- 📈 Dashboard admin

## ⚙️ Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Composer (Package Manager PHP)
- MySQL 5.7 atau lebih tinggi
- Node.js & NPM
- Git

## 📥 Cara Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/repository.git
   cd nama-folder-project
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit file `.env` dan sesuaikan dengan konfigurasi database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username
   DB_PASSWORD=password
   ```

4. **Migrasi Database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Konfigurasi Midtrans**
   Tambahkan konfigurasi Midtrans di file `.env`:
   ```env
   MIDTRANS_SERVER_KEY=your-server-key
   MIDTRANS_CLIENT_KEY=your-client-key
   MIDTRANS_IS_PRODUCTION=false
   ```

6. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   npm run dev
   ```

7. **Akses Aplikasi**
   Buka browser dan akses:
   ```
   http://localhost:8000
   ```

## 🔐 Login Default
- Email: superadmin@billing.app
- Password: password
