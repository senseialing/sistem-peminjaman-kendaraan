# 🚗 SPVMB Asset Management — Sistem Peminjaman Kendaraan Dinas

<div align="center">

**Sistem Informasi Pengelolaan Aset Kendaraan Dinas & Sirkulasi Kendaraan**

Aplikasi web berbasis **Laravel 9** untuk mengelola alur peminjaman kendaraan dinas di
**PT. Stainless Steel Primavalve Majubersama (SPVMB)** — mulai dari pengajuan, otorisasi manajer,
serah-terima kunci berbasis **QR Code**, hingga pelaporan & audit sirkulasi.

*Think Flow, Think SPVMB*

</div>

---

## 📑 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Peran & Hak Akses (Role)](#-peran--hak-akses-role)
- [Alur Kerja Sistem](#-alur-kerja-sistem)
- [Struktur Database](#-struktur-database)
- [Fitur Unggulan](#-fitur-unggulan)
- [Struktur Folder](#-struktur-folder)
- [Instalasi & Konfigurasi](#-instalasi--konfigurasi)
- [Akun Default (Seeder)](#-akun-default-seeder)
- [Daftar Route / Endpoint](#-daftar-route--endpoint)
- [Konvensi Kode](#-konvensi-kode)

---

## 📖 Tentang Proyek

Sistem ini dirancang untuk mendigitalisasi proses peminjaman kendaraan operasional perusahaan
sehingga seluruh tahapan tercatat rapi, transparan, dan dapat diaudit. Setiap kendaraan memiliki
**QR Code inventaris** unik untuk mempercepat verifikasi serah-terima dan pengembalian di lapangan.

**Tiga aktor utama** berinteraksi dalam satu alur kerja:
👷 **Karyawan** (peminjam) → 🛡️ **Manajer** (pemberi izin) → 🧑‍💼 **Staf Admin** (pengelola armada & laporan).

---

## 🛠 Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| **Framework** | Laravel 9 (PHP ^8.0) |
| **Database** | SQLite (default, via `DB_CONNECTION=sqlite`) |
| **Template Engine** | Blade |
| **Frontend** | Blade + CSS kustom (`public/css/app.css`) |
| **Autentikasi** | Laravel Auth + Hash (bcrypt) |
| **QR Code** | Generator SVG buatan sendiri (`App\Services\QrCodeService`) — tanpa dependensi eksternal |
| **Build Asset** | Laravel Mix (Webpack) |
| **Testing** | PHPUnit |

---

## 👥 Peran & Hak Akses (Role)

Role disimpan pada kolom `hak_akses` di tabel `users` dan divalidasi oleh middleware
`App\Http\Middleware\CheckRole` (alias `role`).

| Role | Hak Akses Utama |
|------|-----------------|
| 🧑‍🔧 **Karyawan** | Mengajukan peminjaman, melihat riwayat & tiket sendiri, mengajukan pengembalian |
| 🛡️ **Manajer** | Mengotorisasi (Approve/Reject) permohonan, melihat dashboard antrean & statistik |
| 🧑‍💼 **Staf Admin** | CRUD data kendaraan + QR, proses serah-terima & pengembalian kunci, verifikasi/scan, cetak laporan |

> Helper role tersedia di model `User`: `isKaryawan()`, `isManajer()`, `isStafAdmin()`.

---

## 🔄 Alur Kerja Sistem

```
┌──────────┐   1. Ajukan Peminjaman   ┌──────────────┐
│ Karyawan │ ───────────────────────► │ status:      │
│          │                          │ Pending      │
└──────────┘                          └──────┬───────┘
                                             │ 2. Otorisasi
                                             ▼
                                     ┌──────────────┐   Reject   ┌─────────┐
                                     │   Manajer    │ ─────────► │ Rejected│
                                     │ Approve?     │            └─────────┘
                                     └──────┬───────┘
                                        Approved
                                             │ 3. Serah-terima kunci (QR)
                                             ▼
┌───────────────┐  4. Pengembalian  ┌──────────────┐
│  Staf Admin   │ ◄──────────────── │ Sedang       │
│ Verifikasi QR │   verifikasi      │ Dipinjam     │
└───────┬───────┘                   └──────────────┘
        │ 5. Status armada → "Tersedia", catat ke Laporan
        ▼
   📈 Laporan & Audit Log
```

**Detail tahapan:**

1. **Pengajuan** — Karyawan mengisi form (kendaraan, tanggal, tujuan). Sistem membuat **Nomor Tiket**
   berformat `TRX-YYYYMM-XXXX` dengan status `Pending`.
2. **Otorisasi** — Manajer meninjau antrean lalu **Approve** (dengan/atau tanpa catatan) atau **Reject**
   (wajib dengan catatan alasan).
3. **Serah-terima** — Staf Admin memproses penyerahan kunci. Mencatat `timestamp_serah_terima` dan
   mengubah status kendaraan menjadi **"Sedang Dipinjam"**.
4. **Pengembalian** — Karyawan mengajukan pengembalian; Staf Admin memverifikasi, mencatat
   `timestamp_kembali`, dan mengubah status kendaraan kembali menjadi **"Tersedia"**.
5. **Pelaporan** — Staf Admin menyaring transaksi per periode, melihat audit log, dan mencetak laporan
   (mencatat otomatis ke tabel `laporan_sirkulasi` sebagai jejak audit).

---
## 🗄 Struktur Database

Semua tabel memakai **Primary Key string** (bukan auto-increment) dengan format kode khusus.

### 1. `users` — Data Pengguna
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_pengguna` | string (PK) | Kode pengguna (`ADM-`, `MGR-`, `EMP-`) |
| `nama` | string | Nama lengkap |
| `username` | string (unique) | Username login |
| `password` | string | Password ter-hash (bcrypt) |
| `hak_akses` | enum | `Karyawan` \| `Staf Admin` \| `Manajer` |

### 2. `kendaraan` — Data Armada
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_kendaraan` | string (PK) | Kode unit (`KND-001`) |
| `nama_kendaraan` | string | Nama/tipe kendaraan |
| `plat_nomor` | string | Nomor plat |
| `foto_kendaraan` | text (nullable) | Path foto |
| `kapasitas` | integer | Jumlah penumpang |
| `status_ketersediaan` | enum | `Tersedia` \| `Sedang Dipinjam` |

### 3. `qr_code` — QR Inventaris (One-to-One dengan Kendaraan)
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_qrcode` | string (PK) | Kode QR (`QR-001`) |
| `id_kendaraan` | string (FK) | Relasi ke `kendaraan` (cascade delete) |
| `data_matriks` | text | Data payload QR |

### 4. `transaksi_peminjaman` — Transaksi Peminjaman (tabel inti)
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_transaksi` | string (PK) | Nomor Tiket (`TRX-YYYYMM-XXXX`) |
| `id_pengguna` | string (FK) | Relasi ke `users` |
| `id_kendaraan` | string (FK) | Relasi ke `kendaraan` |
| `tanggal_pengajuan` | date | Tanggal mulai |
| `tanggal_selesai` | date | Tanggal selesai (rencana) |
| `tujuan_operasional` | string | Keperluan dinas |
| `status_otorisasi` | enum | `Pending` \| `Approved` \| `Rejected` |
| `status_sirkulasi` | enum | `Tersedia` \| `Menunggu ACC Pengembalian` |
| `timestamp_serah_terima` | datetime (nullable) | Waktu serah kunci |
| `timestamp_kembali` | datetime (nullable) | Waktu kembali |
| `catatan_manajer` | text (nullable) | Catatan persetujuan/penolakan |

### 5. `laporan_sirkulasi` — Audit Log Laporan
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_laporan` | string (PK) | Kode laporan (`LAP-YYYYMM-XXX`) |
| `rentang_periode_mulai` | date | Awal periode |
| `rentang_periode_akhir` | date | Akhir periode |
| `jenis_laporan` | string | Mis. `Bulanan`, `Tahunan` |
| `total_transaksi` | integer | Jumlah transaksi pada periode |
| `id_staf_admin` | string (FK, nullable) | Pembuat laporan |

### Diagram Relasi (ERD sederhana)

```
users (1) ────< transaksi_peminjaman >──── (1) kendaraan (1) ──── (1) qr_code
                                              │
laporan_sirkulasi (N) >──── users [id_staf_admin]
```

---

## ✨ Fitur Unggulan

- **🔐 Autentikasi & Role-Based Access Control** — Middleware `CheckRole` membatasi tiap route
  berdasarkan `hak_akses`.
- **📝 Pengajuan Peminjaman** — Validasi tanggal, tujuan, dan ketersediaan kendaraan secara real-time.
- **🛡 Otorisasi Berjenjang** — Manajer menyetujui/menolak dengan catatan wajib saat menolak.
- **📱 QR Code SVG Tanpa Dependensi** — `QrCodeService` menghasilkan QR unik (matriks 25×25
  deterministik) sebagai SVG langsung di server.
- **🔍 Verifikasi / Scan Lapangan** — Cari transaksi via Nomor Tiket, ID unit, atau plat nomor.
- **🔄 Sirkulasi Kunci** — Pencatatan timestamp serah-terima & pengembalian yang akurat.
- **📈 Laporan & Audit** — Filter periode, statistik, cetak laporan, dan audit log otomatis.
- **🎨 Dashboard Spesifik per Role** — Tiap peran mendapat ringkasan data yang relevan.
- **🌙 Tema Gelap (Dark Theme)** — UI modern dengan palet warna khas SPVMB (merah `#E11D2D`).

---

## 📁 Struktur Folder

```
sistem-kendaraan/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Auth, Dashboard, Peminjaman, Otorisasi,
│   │   │                       # Kendaraan, Sirkulasi, Laporan
│   │   └── Middleware/
│   │       └── CheckRole.php   # Middleware otorisasi role
│   ├── Models/                 # User, Kendaraan, QrCode, TransaksiPeminjaman, LaporanSirkulasi
│   └── Services/
│       └── QrCodeService.php   # Generator QR Code SVG
├── database/
│   ├── migrations/             # Skema database
│   └── seeders/DatabaseSeeder.php  # Data awal (akun, armada, transaksi contoh)
├── resources/
│   └── views/                  # Template Blade (auth, dashboard, kendaraan,
│                               # peminjaman, otorisasi, sirkulasi, laporan, layouts)
├── routes/
│   └── web.php                 # Definisi route + middleware role
├── public/
│   ├── css/app.css             # Styling utama (tema gelap SPVMB)
│   └── images/                 # Logo & aset gambar
└── ...
```

---

## 🚀 Instalasi & Konfigurasi

### Prasyarat
- PHP >= 8.0
- Composer
- Node.js & NPM (untuk build aset)
- Ekstensi PHP: `sqlite3` (jika memakai SQLite)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <url-repo-anda>
cd sistem-kendaraan

# 2. Install dependensi PHP
composer install

# 3. Install dependensi Node.js
npm install

# 4. Salin file environment & generate key
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database (default: SQLite)
#    Pastikan DB_CONNECTION=sqlite di .env, lalu buat file database:
#    Windows (PowerShell):  New-Item -ItemType File database\database.sqlite

# 6. Jalankan migrasi + seeder data awal
php artisan migrate --seed

# 7. Build aset frontend (opsional untuk development)
npm run dev

# 8. Jalankan server development
php artisan serve
```

Akses aplikasi di **http://127.0.0.1:8000** — Anda akan diarahkan ke halaman **Login**.

> **Catatan:** File `resources/css/app.css` bersifat opsional. Styling utama yang aktif berada di
> `public/css/app.css`. Jika menjalankan `npm run dev/prod`, pastikan sumber CSS juga diperbarui.

---

## 🔑 Akun Default (Seeder)

Setelah `php artisan migrate --seed`, akun berikut tersedia (password untuk semua: **`password123`**):

| Username | Password | Role | ID Pengguna | Nama |
|----------|----------|------|-------------|------|
| `admin` | `password123` | Staf Admin | `ADM-001` | Budi Santoso |
| `manajer` | `password123` | Manajer | `MGR-001` | Hendra Wijaya, S.T. |
| `karyawan` | `password123` | Karyawan | `EMP-001` | Siti Rahmawati |
| `ahmad` | `password123` | Karyawan | `EMP-002` | Ahmad Pratama Putra |

Seeder juga menyiapkan **5 kendaraan contoh** beserta QR Code dan beberapa transaksi demo
(sedang dipinjam, pending, dan riwayat selesai).

---

## 🧭 Daftar Route / Endpoint

| Method | URI | Nama Route | Middleware Role | Keterangan |
|--------|-----|------------|-----------------|------------|
| GET | `/` | — | — | Redirect ke dashboard/login |
| GET/POST | `/login` | `login` | guest | Halaman & proses login |
| GET/POST | `/register` | `register` | guest | Registrasi akun Karyawan |
| POST | `/logout` | `logout` | — | Proses logout |
| GET | `/qr-image/{code}` | `qr.image` | — | Render QR Code SVG |
| GET | `/dashboard` | `dashboard` | auth | Dashboard per role |
| GET | `/peminjaman` | `peminjaman.index` | Karyawan, Staf Admin, Manajer | Riwayat peminjaman |
| GET | `/peminjaman/create` | `peminjaman.create` | Karyawan, Staf Admin, Manajer | Form pengajuan |
| POST | `/peminjaman` | `peminjaman.store` | Karyawan, Staf Admin, Manajer | Simpan pengajuan |
| GET | `/peminjaman/{id}` | `peminjaman.show` | Karyawan, Staf Admin, Manajer | Detail tiket + QR |
| POST | `/peminjaman/{id}/kembalikan` | `peminjaman.kembalikan` | Karyawan, Staf Admin, Manajer | Ajukan pengembalian |
| GET | `/otorisasi` | `otorisasi.index` | Manajer, Staf Admin | Antrean otorisasi |
| POST | `/otorisasi/{id}/approve` | `otorisasi.approve` | Manajer, Staf Admin | Setujui permohonan |
| POST | `/otorisasi/{id}/reject` | `otorisasi.reject` | Manajer, Staf Admin | Tolak permohonan |
| *Resource* | `/kendaraan` | `kendaraan.*` | Staf Admin | CRUD data armada |
| GET | `/sirkulasi` | `sirkulasi.index` | Staf Admin | Antrean sirkulasi kunci |
| POST | `/sirkulasi/{id}/serah-terima` | `sirkulasi.serah-terima` | Staf Admin | Proses serah kunci |
| POST | `/sirkulasi/{id}/pengembalian` | `sirkulasi.pengembalian` | Staf Admin | Proses pengembalian |
| GET | `/sirkulasi/scan` | `sirkulasi.scan` | Staf Admin | Verifikasi/scan tiket |
| GET | `/laporan` | `laporan.index` | Staf Admin | Laporan & audit log |
| GET | `/laporan/cetak` | `laporan.cetak` | Staf Admin | Cetak laporan periode |

---

## 📐 Konvensi Kode

- **Primary Key**: Selalu string dengan prefix kode (`EMP-`, `KND-`, `TRX-`, `QR-`, `LAP-`),
  di-generate otomatis di controller/seeder (bukan auto-increment).
- **Model Eloquent**: Menggunakan `$primaryKey`, `$incrementing = false`, `$keyType = 'string'`.
- **ID Generator**: Pola `PREFIX-{bulan/tahun}-{urutan}` dengan pengecekan duplikasi (`while ... find()`).
- **Relasi**: Didefinisikan eksplisit dengan foreign & owner key pada setiap model.
- **Middleware Role**: `role:RoleA,RoleB` — daftar role dipisah koma pada definisi route.
- **Bahasa**: Antarmuka & pesan sistem menggunakan **Bahasa Indonesia**.

---

<div align="center">

**SPVMB Asset Management** — *Think Flow, Think SPVMB*

&copy; PT. Stainless Steel Primavalve Majubersama

</div>

