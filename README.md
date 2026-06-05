# Sistem E-Commerce Penjualan Obat Klinik Makmur Jaya

Prototype full-stack Laravel untuk uji kompetensi BNSP skema Web Developer. Aplikasi ini dibuat sebagai demo sistem e-commerce penjualan obat klinik dengan alur online dan offline, role admin/apoteker/kasir/pasien, stok batch FIFO, resep dokter, laporan, PDF, import CSV, audit log, notifikasi, monitoring resource, dan simulasi alert.

## Tech Stack

- Laravel 13, PHP 8.3, Composer.
- Blade, Tailwind CSS 4, Vite 8, JavaScript ringan.
- Chart.js untuk grafik dashboard admin.
- SQLite untuk demo lokal; migration tetap portable untuk MySQL/PostgreSQL.
- Queue Laravel untuk import CSV.
- Dompdf untuk export PDF.

## Fitur Demo

- Auth dan role access: admin, apoteker, kasir, pasien.
- Dashboard admin dengan statistik, chart stabil, notifikasi, dan monitoring ringkas.
- CRUD kategori, supplier, obat, dan batch stok.
- Upload gambar obat dan upload resep.
- Katalog obat dengan search, filter, sorting, autocomplete, dan suggestion sederhana.
- Cart dan checkout online.
- Obat resep wajib upload resep, lalu diverifikasi apoteker.
- Pengurangan stok FIFO dari batch expiry terdekat.
- Transaksi offline oleh kasir dan halaman struk.
- Laporan penjualan, export PDF, import CSV via queue.
- Audit log, error log, monitoring resource, dan simulasi alert.

## Instalasi

```bash
composer install
npm install --ignore-scripts
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
npm run build
```

Untuk Windows PowerShell, jika `npm` terkena policy script, gunakan:

```bash
npm.cmd install --ignore-scripts
npm.cmd run build
```

Jika memakai SQLite, pastikan `.env` berisi:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Jika file SQLite belum ada:

```bash
type nul > database/database.sqlite
```

## Menjalankan Aplikasi

Terminal 1:

```bash
php artisan serve
```

Terminal 2, hanya jika ingin import CSV diproses queue:

```bash
php artisan queue:work
```

Untuk mode development asset:

```bash
npm run dev
```

Untuk presentasi/demo, cukup gunakan asset hasil:

```bash
npm run build
php artisan serve
```

## Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | `admin@klinik.test` | `password123` |
| Apoteker | `apoteker@klinik.test` | `password123` |
| Kasir | `kasir@klinik.test` | `password123` |
| Pasien | `pasien@klinik.test` | `password123` |

## Queue dan Import CSV

1. Login sebagai admin.
2. Buka `/admin/imports`.
3. Upload CSV contoh dari `storage/app/examples/sample_medicines.csv` atau `docs/sample_medicines.csv`.
4. Pastikan queue worker berjalan:

```bash
php artisan queue:work
```

5. Refresh halaman import untuk melihat status import.

Jika queue belum berjalan, status import bisa tetap `pending`. Jalankan `php artisan queue:work`, lalu refresh.

## Export PDF

1. Login sebagai admin.
2. Buka `/admin/reports`.
3. Pilih periode jika perlu.
4. Klik `Export PDF`.

Jika PDF gagal, pastikan `composer install` sudah selesai dan ekstensi PHP `dom`, `gd`, serta `mbstring` aktif.

## Simulasi Alert

1. Login sebagai admin.
2. Buka `/admin/simulations`.
3. Klik:
   - `Low Stock Alert` untuk notifikasi stok rendah.
   - `Expired Alert` untuk notifikasi obat hampir kedaluwarsa.
   - `Application Error` untuk membuat error log critical.
4. Cek hasil di notifikasi, audit log, dan error log.

## Validasi Sebelum Presentasi

```bash
php artisan migrate:fresh --seed
npm run build
php artisan test
php artisan route:list
```

Hasil validasi terakhir tahap final:

- `php artisan migrate:fresh --seed`: berhasil.
- `npm.cmd run build`: berhasil.
- `php artisan test`: 16 test, 76 assertion, semua passed.
- `php artisan route:list`: 75 route.

## Alur Demo Singkat

1. Admin: login, dashboard, CRUD master data, batch stok, laporan PDF, import CSV, simulasi alert.
2. Pasien: login, katalog, search/filter, tambah cart, checkout obat bebas, checkout obat resep dengan upload resep.
3. Apoteker: login, buka verifikasi resep, approve/reject, jelaskan stok FIFO.
4. Kasir: login, buat transaksi offline, lihat struk.
5. Admin lagi: cek audit log, error log, monitoring, dan notifikasi.

Detail urutan demo ada di `docs/19_demo_script.md`.

## Troubleshooting

| Masalah | Solusi |
|---|---|
| Tidak bisa login | Jalankan `php artisan migrate:fresh --seed`, lalu gunakan akun demo. |
| Gambar obat/resep tidak tampil | Jalankan `php artisan storage:link`; pastikan file ada di `storage/app/public`. |
| Import CSV tidak berubah dari pending | Jalankan `php artisan queue:work`. |
| PDF gagal diunduh | Cek dependency Composer dan ekstensi PHP `dom`, `gd`, `mbstring`. |
| Chart terasa berat | Gunakan build terbaru; chart sudah dimatikan animasinya dan memakai instance registry. |
| Notifikasi tidak update | Pastikan user login dan endpoint `/notifications/unread-count` bisa diakses. |
| Checkout gagal | Pastikan cart tidak kosong, stok cukup, dan upload resep untuk obat resep. |
| Route error setelah perubahan | Jalankan `php artisan route:list` dan `php artisan test`. |

## Limitasi Prototype

- Pembayaran masih simulasi, belum terhubung payment gateway.
- Notifikasi memakai polling ringan, belum memakai broadcasting/WebSocket.
- Monitoring resource masih sederhana, bukan observability produksi.
- Import CSV memakai queue Laravel; worker harus dijalankan untuk memproses job.
- Belum ada browser E2E otomatis karena validasi utama saat ini memakai feature test Laravel dan smoke HTTP.
- Untuk produksi, perlu hardening tambahan: backup database, audit keamanan, rate limit, payment gateway resmi, Sentry/Pulse/Telescope/Prometheus, dan deployment pipeline.
