# BOBO Klinik & Pet Shop

Website homepage dan sistem basis data administrasi untuk klinik hewan, dibuat berdasarkan nota administrasi asli sebuah klinik hewan di Kota Wisata, Cibubur.

> **Catatan:** Proyek ini dibuat untuk portofolio. Semua data di dalamnya (pemilik, hewan, dokter, nota) adalah **data fiktif**.

## Fitur

**Homepage**
- Profil klinik, daftar layanan, jenis hewan yang dilayani, serta kontak dan lokasi
- Tampilan responsif (HP dan desktop), dengan tema putih dan hitam
- Ikon grafis SVG tanpa library tambahan

**Basis Data (`/db`)**
- **Login**: semua halaman dilindungi sesi dan token CSRF
- **Dashboard**: jumlah nota dan pendapatan bulan ini, total pemilik dan hewan, pendapatan per layanan, serta 5 nota terakhir
- **CRUD** untuk Nota, Pemilik, Hewan, Layanan, dan Dokter
- **Nota**: rincian layanan dinamis, total terhitung otomatis, dan nomor nota otomatis (`BK-TAHUN-00001`)
- **Cetak**: nota siap cetak dengan terbilang otomatis, serta laporan nota per periode

## Teknologi

- HTML dan CSS (tanpa framework)
- PHP 8 (PDO, prepared statement)
- MySQL / MariaDB
- XAMPP

## Struktur Basis Data

| Tabel | Keterangan |
|---|---|
| `users` | akun login |
| `pemilik` | data pemilik hewan |
| `hewan` | data hewan (terhubung ke pemilik) |
| `dokter` | data dokter hewan |
| `layanan` | master layanan (Konsultasi, Vaksinasi, Operasi, dll.) |
| `nota` | header nota (tanggal, pemilik, hewan, dokter, total) |
| `nota_detail` | rincian layanan, keterangan, dan harga per nota |

Relasi: `pemilik` → `hewan` → `nota` → `nota_detail` → `layanan`, dan `nota` → `dokter`.

## Cara Menjalankan

1. Pasang [XAMPP](https://www.apachefriends.org/) dan jalankan **Apache** serta **MySQL**.
2. Salin folder proyek ke `C:\xampp\htdocs\bobo`.
3. Buka `http://localhost/phpmyadmin`, lalu **Import** file `db/bobo_klinik.sql`.
4. Buka `http://localhost/bobo/`, klik tombol **Basis Data**.
5. Login dengan akun demo di bawah.

### Akun Demo

| Username | Password |
|---|---|
| `admin` | `admin123` |

Pengaturan koneksi database ada di `db/config.php` (bawaan XAMPP: user `root`, tanpa password).

## Struktur Folder

```
bobo/
├── index.html          # homepage
└── db/
    ├── bobo_klinik.sql # skema dan data contoh
    ├── config.php      # koneksi dan fungsi bantu
    ├── login.php
    ├── logout.php
    ├── dashboard.php
    ├── nota.php        # CRUD nota dan rincian
    ├── crud.php        # CRUD pemilik, hewan, layanan, dokter
    └── cetak.php       # cetak nota dan laporan
```

## Keamanan

- Query memakai *prepared statement* (anti SQL injection)
- Output di-escape (anti XSS)
- Token CSRF pada semua form
- Password disimpan dengan `password_hash`

Aplikasi ini ditujukan untuk demo lokal. Sebelum dipakai di server sungguhan, ganti password akun demo dan atur kredensial database di `db/config.php`.

## Lisensi

Proyek portofolio. Silakan gunakan sebagai referensi belajar.
