# Demo Checklist BNSP

Gunakan checklist ini 30-60 menit sebelum presentasi.

## 1. Persiapan Command

Jalankan dari root project:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan test
php artisan route:list
php artisan serve
```

Jika memakai PowerShell dan `npm` bermasalah:

```bash
npm.cmd run build
```

Jika demo import CSV:

```bash
php artisan queue:work
```

Checklist:

- [ ] `php artisan migrate:fresh --seed` berhasil.
- [ ] `npm run build` atau `npm.cmd run build` berhasil.
- [ ] `php artisan test` berhasil.
- [ ] `php artisan route:list` menampilkan route.
- [ ] `php artisan serve` berjalan.
- [ ] `php artisan queue:work` berjalan jika akan demo import CSV.

## 2. Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | `admin@klinik.test` | `password123` |
| Pasien | `pasien@klinik.test` | `password123` |
| Apoteker | `apoteker@klinik.test` | `password123` |
| Kasir | `kasir@klinik.test` | `password123` |

Checklist:

- [ ] Admin bisa login.
- [ ] Pasien bisa login.
- [ ] Apoteker bisa login.
- [ ] Kasir bisa login.

## 3. Halaman yang Harus Dicek

Admin:

- [ ] `/admin/dashboard`
- [ ] `/admin/medicines`
- [ ] `/admin/categories`
- [ ] `/admin/suppliers`
- [ ] `/admin/medicine-batches`
- [ ] `/admin/reports`
- [ ] `/admin/imports`
- [ ] `/admin/monitoring`
- [ ] `/admin/audit-logs`
- [ ] `/admin/error-logs`
- [ ] `/admin/simulations`

Pasien:

- [ ] `/catalog`
- [ ] Detail obat dari katalog.
- [ ] `/cart`
- [ ] `/checkout`
- [ ] `/orders`

Apoteker:

- [ ] `/apoteker/dashboard`
- [ ] `/apoteker/prescriptions`
- [ ] `/apoteker/stock-alerts`

Kasir:

- [ ] `/kasir/dashboard`
- [ ] `/kasir/sales/create`

## 4. Skenario Demo Utama

- [ ] Admin melihat dashboard dan chart.
- [ ] Admin menambah/edit kategori, supplier, obat, atau batch.
- [ ] Admin export PDF laporan.
- [ ] Admin upload CSV import.
- [ ] Pasien search/filter obat.
- [ ] Pasien checkout obat bebas.
- [ ] Pasien checkout obat resep dengan upload resep.
- [ ] Apoteker approve/reject resep.
- [ ] Kasir membuat transaksi offline.
- [ ] Admin melihat audit log, error log, monitoring, dan simulasi alert.

## 5. Jika Queue Belum Jalan

Gejala:

- Import CSV tetap `pending`.
- Data obat dari CSV belum muncul.

Yang dilakukan:

```bash
php artisan queue:work
```

Lalu refresh `/admin/imports`.

Narasi ke asesor:

"Import diproses menggunakan queue Laravel. Jika worker belum berjalan, job menunggu di antrean. Setelah worker dijalankan, status berubah dan data diproses."

## 6. Jika PDF Gagal

Gejala:

- Klik `Export PDF` error atau file tidak terunduh.

Yang dicek:

- `composer install` sudah selesai.
- Ekstensi PHP `dom`, `gd`, `mbstring` aktif.
- Route `/admin/reports/pdf` bisa dibuka oleh admin.

Narasi ke asesor:

"PDF menggunakan Dompdf. Jika environment PHP belum lengkap, export bisa gagal walaupun data laporan tetap tersedia di halaman laporan."

## 7. Jika Gambar Tidak Tampil

Gejala:

- Gambar obat atau resep tidak muncul.

Yang dilakukan:

```bash
php artisan storage:link
```

Pastikan file ada di:

- `storage/app/public`
- link `public/storage`

Narasi ke asesor:

"File upload disimpan di disk public Laravel. Agar bisa diakses browser, Laravel memerlukan storage link."

## 8. Jika Chart atau Polling Terlihat Bermasalah

Yang dicek:

- Hard refresh browser.
- Pastikan asset terbaru sudah dibuild: `npm run build`.
- Buka DevTools Console, pastikan tidak ada error JavaScript.
- Buka Network tab, notifikasi seharusnya polling sekitar 15 detik.
- Monitoring resource seharusnya polling sekitar 10 detik hanya di halaman `/admin/monitoring`.

Narasi ke asesor:

"Chart memakai registry instance supaya tidak dibuat berulang. Polling dibuat ringan dan hanya berjalan pada halaman yang membutuhkan."

## 9. Limitasi yang Boleh Dijelaskan

- Pembayaran masih simulasi.
- Notifikasi masih polling, belum WebSocket.
- Monitoring resource masih prototype.
- Import CSV membutuhkan queue worker.
- Belum ada browser E2E otomatis; regression utama dicakup Feature Test Laravel.

## 10. Kondisi Siap Presentasi

- [ ] Semua command validasi berhasil.
- [ ] Semua akun demo bisa login.
- [ ] Alur admin, pasien, apoteker, kasir berhasil dry-run.
- [ ] Queue worker siap jika demo import CSV.
- [ ] File resep/gambar demo tersedia.
- [ ] Browser sudah dibuka ke halaman login.
