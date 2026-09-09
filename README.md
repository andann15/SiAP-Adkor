<div align="center">

# SIAP
**Sistem Informasi Aset & Pelayanan**

> Aplikasi web manajemen aset dan tiket keluhan berbasis web untuk lingkungan korporat — dikembangkan untuk **PT Pupuk Kaltim - Departemen Administrasi Korporat (AdKor)**.

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![TiDB Cloud](https://img.shields.io/badge/TiDB_Cloud-Serverless-EF4444?logo=tidb&logoColor=white)](https://tidbcloud.com/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?logo=vite&logoColor=white)](https://vitejs.dev/)
[![Cloudinary](https://img.shields.io/badge/Cloudinary-Storage-3448C5?logo=cloudinary&logoColor=white)](https://cloudinary.com/)
[![Vercel](https://img.shields.io/badge/Vercel-Deployed-000000?logo=vercel&logoColor=white)](https://vercel.com/)

🌐 **Live Demo:** [siap-web.vercel.app](https://siap-web.vercel.app)

</div>

---

## 📋 Deskripsi Proyek

**SIAP** adalah aplikasi web manajemen aset dan pemeliharaan internal yang dirancang untuk mempermudah proses pelaporan, pengelolaan, dan pemantauan kerusakan aset kantor. Sistem ini memungkinkan:

- **Karyawan** memilih aset yang sedang mereka gunakan dan melaporkan kerusakan secara langsung.
- **Admin** mengelola data aset, pengguna, dan menyetujui/menolak tiket keluhan.
- **Operator/Teknisi** melihat dan mengerjakan tiket yang telah disetujui.

---

## 📖 Buku Panduan (User Manual)

Buku panduan lengkap (HTML/PDF) untuk mempelajari cara penggunaan aplikasi ini dari sisi Admin, Operator, dan User telah disertakan di dalam *repository* ini.

Anda dapat mengakses buku panduan dengan cara:
1. Membuka URL berikut di browser Anda (Live): [siap-web.vercel.app/panduan.html](https://siap-web.vercel.app/panduan.html)
2. Atau membuka file `public/panduan.html` di komputer Anda secara lokal.
3. Anda dapat langsung menekan **Ctrl + P** di browser untuk menyimpannya sebagai file **PDF**.

---

## ✨ Fitur Utama

| Fitur | Keterangan |
|---|---|
| 🔐 Autentikasi & Role | Login dengan 3 peran: Admin, Operator, Karyawan |
| 🗃️ Manajemen Aset | Admin menambah, mengedit, dan menetapkan aset ke karyawan |
| 📋 Aset Saya | Karyawan dapat mengklaim dan melihat daftar aset yang digunakan |
| 🎫 Sistem Tiket | Karyawan melaporkan kerusakan dengan foto (upload atau kamera langsung) |
| 📸 Kamera Real-time | Form laporan mendukung pengambilan foto langsung dari kamera HP/webcam |
| ✅ Alur Persetujuan | Admin setujui → Operator ditugaskan → Selesai → Ditutup Karyawan |
| 🗂️ Tab Status Tiket | Tiket dikelompokkan: Semua, Menunggu, Diproses, Selesai, Batal |
| 🔍 Pencarian Tiket | Fitur pencarian global di semua halaman tiket |
| 📊 Dashboard | Ringkasan status tiket per peran masing-masing |
| 📄 Ekspor PDF | Admin dapat mengekspor laporan aset & tiket ke PDF |
| ☁️ Upload Foto Cloud | Foto tiket disimpan di Cloudinary (production) |

---

## 🏗️ Arsitektur Sistem

```
Browser / HP
    │
    ▼
Vercel (vercel-php@0.6.2 / PHP 8.2)
    │
    ▼
api/index.php  →  public/index.php
    │
    ▼
Routes (routes/web.php)
    │
    ▼
Controllers (app/Http/Controllers/)
    │
    ▼
Models (app/Models/) ←——→ TiDB Cloud (MySQL-compatible)
    │
    ▼
Views / Blade Templates (resources/views/)
    │
    ▼
Browser menampilkan halaman
```

**Stack Teknologi:**

| Layer | Teknologi | Catatan |
|---|---|---|
| Backend | PHP 8.2 + Laravel 12 | Framework utama |
| Frontend | Blade + TailwindCSS + Alpine.js | Rendering server-side |
| Database (Production) | TiDB Cloud Serverless | MySQL-compatible, gratis |
| Database (Localhost) | MySQL via XAMPP | Development lokal |
| Penyimpanan Foto | Cloudinary | Upload foto tiket |
| Hosting | Vercel (vercel-php@0.6.2) | Serverless PHP |
| Auth & Permission | Laravel Breeze + Spatie Permission | RBAC |
| Build Tool | Vite 7 | Bundling CSS/JS |

---

## 📁 Struktur Folder Penting

```
siap/
├── api/
│   └── index.php               # Entry point Vercel (bridge ke Laravel)
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Logika bisnis tiap fitur
│   │   └── Requests/           # Form Request & validasi
│   ├── Models/                 # Model Eloquent (Asset, Ticket, User, dll.)
│   ├── Policies/               # Otorisasi berbasis kebijakan
│   └── Services/               # Service class (TicketStateMachine)
├── config/
│   ├── database.php            # Konfigurasi database + SSL TiDB
│   └── filesystems.php         # Konfigurasi Cloudinary storage
├── database/
│   ├── migrations/             # Skema tabel database
│   └── seeders/                # Data awal (roles, permissions, user admin)
├── public/
│   ├── build/                  # Hasil build Vite (CSS/JS — di-commit untuk Vercel)
│   └── index.php               # Entry point Laravel
├── resources/
│   ├── views/
│   │   ├── admin/              # Halaman Admin
│   │   ├── tickets/            # Halaman Tiket (buat, lihat, daftar)
│   │   ├── user/               # Dashboard Karyawan
│   │   ├── layouts/            # Template dasar
│   │   └── components/         # Komponen reusable (sidebar, navbar, dll.)
│   └── css/                    # File CSS utama
├── routes/
│   ├── web.php                 # Definisi semua rute web
│   └── auth.php                # Rute autentikasi
├── vercel.json                 # Konfigurasi deployment Vercel
├── .env                        # Konfigurasi lingkungan (tidak di-commit ke Git)
└── .env.example                # Template konfigurasi lingkungan
```

---

## 🔄 Alur Sistem Tiket

```
Karyawan Buat Tiket
        │
        ▼
[waiting_approval] ──── Admin Tolak ────► [rejected]
        │
  Admin Setujui + Tugaskan Operator
        │
        ▼
    [assigned]
        │
  Operator Mulai Kerjakan
        │
        ▼
   [checking] ◄── Operator upload foto bukti
        │
  Operator Tandai Selesai
        │
        ▼
   [completed]
        │
  Karyawan Konfirmasi Selesai
        │
        ▼
    [closed] ✅
```

> Karyawan juga dapat **membatalkan** tiket selama masih dalam status `waiting_approval`.

---

## 🛡️ Peran & Hak Akses

| Aksi | Admin | Operator | Karyawan |
|---|:---:|:---:|:---:|
| Kelola Master Data (Aset, Divisi, dll.) | ✅ | ❌ | ❌ |
| Lihat semua tiket | ✅ | ✅* | ❌ |
| Setujui / Tolak tiket | ✅ | ❌ | ❌ |
| Kerjakan tiket | ❌ | ✅ | ❌ |
| Buat tiket keluhan | ❌ | ❌ | ✅ |
| Klaim & lihat Aset Saya | ❌ | ❌ | ✅ |
| Tutup tiket (konfirmasi selesai) | ❌ | ❌ | ✅ |
| Manajemen Pengguna | ✅ | ❌ | ❌ |
| Ekspor PDF | ✅ | ❌ | ❌ |

> *Operator tidak melihat tiket berstatus `rejected` atau `cancelled`.

---

## 👤 Akun Default (Setelah Seeding)

| Role | Email | Password |
|---|---|---|
| Admin | admin@siap.com | password |
| Operator | operator@siap.com | password |
| Karyawan | user@siap.com | password |

> ⚠️ **Penting:** Segera ganti password setelah pertama kali login di lingkungan produksi!

---

## 🌐 Deployment ke Vercel (Production)

### Prasyarat

- Akun [Vercel](https://vercel.com) (gratis)
- Akun [TiDB Cloud](https://tidbcloud.com) (gratis, untuk database)
- Akun [Cloudinary](https://cloudinary.com) (gratis, untuk penyimpanan foto)
- Repository sudah di-push ke GitHub

### Langkah Deployment

**1. Fork / Push repository ke GitHub**

**2. Import project ke Vercel**
- Buka [vercel.com/new](https://vercel.com/new)
- Klik **Import** pada repository Anda
- Framework Preset biarkan **Other**

**3. Siapkan TiDB Cloud Database**
- Daftar di [tidbcloud.com](https://tidbcloud.com)
- Buat cluster **Serverless** (gratis)
- Buka tab **Connect** dan catat kredensial koneksi

**4. Siapkan Cloudinary**
- Daftar di [cloudinary.com](https://cloudinary.com)
- Dari Dashboard, catat: **Cloud Name**, **API Key**, **API Secret**

**5. Konfigurasi Environment Variables di Vercel**

Buka **Settings → Environment Variables** di project Vercel Anda, lalu tambahkan:

| Key | Value | Keterangan |
|---|---|---|
| `APP_NAME` | `SIAP` | Nama aplikasi |
| `APP_ENV` | `production` | Mode produksi |
| `APP_KEY` | `base64:...` | Generate dengan `php artisan key:generate` |
| `APP_DEBUG` | `false` | Nonaktifkan debug di produksi |
| `APP_URL` | `https://siap-web.vercel.app` | URL aplikasi Anda |
| `DB_CONNECTION` | `mysql` | Driver database |
| `DB_HOST` | `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` | Host TiDB Cloud |
| `DB_PORT` | `4000` | Port TiDB Cloud |
| `DB_DATABASE` | `nama_database` | Nama database di TiDB |
| `DB_USERNAME` | `username.root` | Username TiDB |
| `DB_PASSWORD` | `password` | Password TiDB |
| `CLOUDINARY_CLOUD_NAME` | `your_cloud_name` | Dari Cloudinary Dashboard |
| `CLOUDINARY_API_KEY` | `your_api_key` | Dari Cloudinary Dashboard |
| `CLOUDINARY_API_SECRET` | `your_api_secret` | Dari Cloudinary Dashboard |
| `SESSION_DRIVER` | `cookie` | Session berbasis cookie (Vercel stateless) |
| `CACHE_STORE` | `array` | Cache di memory (Vercel stateless) |
| `FILESYSTEM_DISK` | `cloudinary` | Gunakan Cloudinary untuk upload |

> ⚠️ **Jangan** gunakan `CLOUDINARY_URL` — Vercel akan mengubah format URL-nya. Gunakan 3 variabel terpisah di atas.

**6. Deploy**
- Klik **Deploy** dan tunggu proses selesai (~2-3 menit)
- Setelah selesai, jalankan migrasi database melalui Vercel CLI atau melalui localhost:

```bash
# Jalankan migration dari lokal dengan env production
php artisan migrate --env=production
# atau
php artisan migrate
```

**7. Verifikasi**
- Buka URL Vercel Anda
- Coba login dengan akun default

### Catatan Teknis Vercel

| Hal | Detail |
|---|---|
| PHP Runtime | `vercel-php@0.6.2` (PHP 8.2) |
| Entry Point | `api/index.php` |
| Cache Dir | `/tmp/bootstrap/cache` (auto-generated saat runtime) |
| File Upload | Tidak bisa simpan ke disk lokal — **wajib** gunakan Cloudinary |
| Session | Harus menggunakan `cookie` atau database driver |

---

## 💻 Menjalankan di Localhost (Untuk Penyerahan PT Pupuk Kaltim)

Bagian ini ditujukan bagi tim IT internal PT Pupuk Kaltim yang ingin menjalankan aplikasi secara lokal di server *on-premise* perusahaan atau PC *development*.

### Prasyarat Sistem

Pastikan server/PC Anda sudah terinstall:
- [XAMPP](https://www.apachefriends.org/) v8.2+ (dengan modul Apache dan MySQL aktif)
- [Composer](https://getcomposer.org/) (Package manager PHP)
- [Node.js 18+](https://nodejs.org/) & NPM (Untuk *bundling* aset *frontend*)

### Langkah Instalasi & Konfigurasi

**1. Unduh (Clone) Repository**
```bash
git clone https://github.com/andann15/SiAP-Adkor.git
cd SiAP-Adkor
```

**2. Install Dependensi Backend (PHP)**
```bash
composer install
```

**3. Install & Build Dependensi Frontend (JavaScript/CSS)**
```bash
npm install
npm run build
```
> *Catatan: `npm run build` akan mengompilasi semua *file* Tailwind CSS dan JavaScript ke dalam folder `public/build`. Langkah ini **wajib** dilakukan agar tampilan web muncul dengan benar (tidak berantakan) saat dijalankan.*

**4. Siapkan File Konfigurasi Lingkungan (.env)**
```bash
# Windows
copy .env.example .env

# Mac/Linux
cp .env.example .env
```

**5. Hasilkan Kunci Keamanan (Application Key)**
```bash
php artisan key:generate
```

**6. Konfigurasi Database Lokal di `.env`**
Buka file `.env` menggunakan *text editor* (Notepad/VSCode), lalu sesuaikan bagian database:
```env
APP_NAME=SIAP
APP_ENV=local
APP_KEY=        # (Sudah terisi otomatis dari langkah 5)
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siap_lokal
DB_USERNAME=root
DB_PASSWORD=
```

**7. Buat Database MySQL**
- Pastikan modul **Apache** dan **MySQL** di XAMPP berstatus aktif (berwarna hijau).
- Buka browser dan akses `http://localhost/phpmyadmin`.
- Buat database baru dengan nama `siap_lokal`.

**8. Migrasi Tabel & Masukkan Data Awal (Seeder)**
Langkah ini akan membangun seluruh struktur tabel dan membuat akun *default* (Admin, Operator, User).
```bash
php artisan migrate --seed
```

**9. Konfigurasi Penyimpanan File (Foto/PDF)**
Agar foto bukti kerusakan atau file PDF laporan dapat disimpan di hardisk komputer/server lokal, jalankan perintah ini untuk membuat *symbolic link*:
```bash
php artisan storage:link
```
*(Pastikan di `.env`, nilai `FILESYSTEM_DISK=local`)*

**10. Jalankan Aplikasi**
Buka terminal dan jalankan server lokal bawaan Laravel:
```bash
php artisan serve
```

Aplikasi sekarang sudah berjalan! Silakan akses melalui browser di alamat:
**[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

### Upload Foto di Localhost

Secara default di localhost, foto akan disimpan di folder `storage/app/public/`. Pastikan sudah menjalankan:

```bash
php artisan storage:link
```

Jika ingin menggunakan Cloudinary juga di localhost, tambahkan ke `.env`:
```env
FILESYSTEM_DISK=cloudinary
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

---

## 🔧 Perintah Artisan yang Sering Digunakan

```bash
# Jalankan migrasi ulang dari awal (HAPUS semua data!)
php artisan migrate:fresh --seed

# Bersihkan cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Lihat semua rute
php artisan route:list

# Buat symbolic link storage
php artisan storage:link
```

---

## ❓ Troubleshooting

| Masalah | Solusi |
|---|---|
| `Class not found` setelah pull | Jalankan `composer dump-autoload` |
| Halaman blank / error 500 | Cek file `.env` sudah ada & `APP_KEY` sudah diisi |
| CSS tidak ter-update | Jalankan `npm run dev` atau `npm run build` |
| Upload foto gagal | Pastikan `storage:link` sudah dijalankan (localhost) atau env Cloudinary sudah diisi (production) |
| Vercel: `FUNCTION_INVOCATION_FAILED` | Pastikan menggunakan `vercel-php@0.6.2` di `vercel.json`, bukan versi lebih baru |
| Database error di Vercel | Pastikan tidak menggunakan InfinityFree — gunakan TiDB Cloud |

---

## 📄 Lisensi

Proyek ini dikembangkan secara internal untuk keperluan **PT Pupuk Kaltim - Departemen Administrasi Korporat (AdKor)**. Seluruh hak cipta dilindungi.

---

<div align="center">
  <sub>Dibangun dengan ❤️ menggunakan Laravel 12, TailwindCSS, Vercel & TiDB Cloud</sub>
</div>
