# Sistem E-Commerce Penjualan Obat Klinik Makmur Jaya

Prototype full-stack Laravel untuk uji kompetensi BNSP skema Web Developer. Sistem ini mendukung penjualan obat online dan offline, role admin/apoteker/kasir/pasien, stok batch FIFO, resep dokter, laporan SQL, PDF, queue import CSV, audit log, notifikasi, dan monitoring resource prototype.

## Tech Stack

- Laravel 13.14.0, PHP 8.3.16, Composer 2.8.8.
- Blade, Tailwind CSS 4, Vite 8, Chart.js.
- Database default lokal: SQLite. Migration tetap memakai tipe Laravel portable untuk MySQL/PostgreSQL.
- Queue database driver.
- PDF export: dompdf/dompdf 3.1.

## Fitur Utama

- Login, logout, register pasien, session timeout 30 menit, role middleware.
- Dashboard admin dengan statistik, chart, notifikasi, dan monitoring.
- CRUD kategori, supplier, obat, dan batch stok.
- Upload gambar obat dan upload/preview resep.
- Katalog, search/filter/sorting, autocomplete, fuzzy suggestion sederhana.
- Cart, checkout online, validasi stok, resep wajib untuk obat resep.
- Apoteker approve/reject resep dan stok dikurangi FIFO saat approve.
- Kasir membuat transaksi offline/counter.
- Laporan penjualan raw SQL aman dengan binding dan export PDF.
- Import CSV obat melalui queue database.
- Simulasi low stock, expired medicine, application error, audit log, error log.

## Instalasi dari Nol

```bash
composer install
npm install --ignore-scripts
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Jika memakai SQLite, pastikan `DB_CONNECTION=sqlite` dan file `database/database.sqlite` tersedia. Jika memakai PostgreSQL/MySQL, isi konfigurasi database di `.env` tanpa menyimpan credential produksi ke repository.

## Akun Demo

- Admin: `admin@klinik.test` / `password123`
- Apoteker: `apoteker@klinik.test` / `password123`
- Kasir: `kasir@klinik.test` / `password123`
- Pasien: `pasien@klinik.test` / `password123`

## Queue, Scheduler, Import, PDF

Jalankan queue:

```bash
php artisan queue:work
```

Collect metrics dan alert:

```bash
php artisan app:collect-resource-metrics --alerts
```

Import CSV:

1. Login admin.
2. Buka `/admin/imports`.
3. Upload CSV sesuai contoh `storage/app/examples/sample_medicines.csv` atau `docs/sample_medicines.csv`.
4. Jalankan `php artisan queue:work`.

Export PDF:

1. Login admin.
2. Buka `/admin/reports`.
3. Pilih periode.
4. Klik `Export PDF`.

## Test dan Formatting

```bash
php artisan test
vendor/bin/pint
npm run build
php artisan route:list
```

## Simulasi Demo untuk Asesor

1. Login admin, lihat dashboard, chart, notifikasi, monitoring.
2. CRUD obat, upload gambar, tambah batch stok.
3. Buka laporan, tunjukkan raw SQL report dan export PDF.
4. Upload CSV import lalu jalankan queue.
5. Login pasien, search/filter obat, tambah cart, checkout obat bebas.
6. Checkout obat resep dengan upload resep.
7. Login apoteker, approve/reject resep, jelaskan stok FIFO.
8. Login kasir, buat transaksi offline dan preview struk.
9. Tunjukkan error log, audit log, simulasi alert, dan dokumen cutover/UAT/debugging.

## Troubleshooting Singkat

- Storage image tidak muncul: jalankan `php artisan storage:link`.
- Queue import tidak bergerak: jalankan `php artisan queue:work`.
- PDF gagal: pastikan `composer install` selesai dan ekstensi PHP `dom`, `gd`, `mbstring` aktif.
- Database kosong: jalankan `php artisan migrate:fresh --seed`.
- Npm PowerShell error: gunakan `npm.cmd install` atau terminal yang mengizinkan script.

## Limitasi Prototype

Pembayaran masih simulasi, notifikasi memakai polling, monitoring resource masih sederhana, dan email memakai log driver jika mail belum dikonfigurasi. Untuk produksi, pertimbangkan Laravel Pulse/Telescope, Sentry, Prometheus/Grafana, backup database terjadwal, dan payment gateway resmi.
