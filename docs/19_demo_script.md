# Demo Script 10-15 Menit

Dokumen ini dipakai sebagai panduan bicara saat presentasi uji kompetensi BNSP. Urutan demo disusun berdasarkan role: admin -> pasien -> apoteker -> kasir -> admin lagi.

## Persiapan 1 Menit

Command sebelum demo:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan test
php artisan serve
```

Jika akan demo import CSV, jalankan terminal kedua:

```bash
php artisan queue:work
```

Akun demo:

- Admin: `admin@klinik.test` / `password123`
- Pasien: `pasien@klinik.test` / `password123`
- Apoteker: `apoteker@klinik.test` / `password123`
- Kasir: `kasir@klinik.test` / `password123`

## 1. Admin: Dashboard dan Master Data

Durasi: 3-4 menit.

Klik:

1. Buka `/login`.
2. Login admin.
3. Buka `/admin/dashboard`.
4. Tunjukkan statistik, grafik penjualan harian, grafik status order, notifikasi, dan monitoring resource.
5. Buka `/admin/categories`, `/admin/suppliers`, dan `/admin/medicines`.
6. Klik tambah/edit salah satu data jika waktu cukup.
7. Buka `/admin/medicine-batches`.

Narasi singkat:

"Di role admin, sistem menyediakan dashboard operasional klinik. Chart sudah dibuat stabil dengan Chart.js dan tidak dibuat ulang berulang. Admin bisa mengelola master kategori, supplier, obat, dan batch stok. Batch memiliki tanggal kedaluwarsa sehingga pengurangan stok dapat mengikuti FIFO dari batch expiry terdekat."

## 2. Admin: Laporan, PDF, Import, Simulasi

Durasi: 2-3 menit.

Klik:

1. Buka `/admin/reports`.
2. Pilih periode atau gunakan default.
3. Klik `Export PDF`.
4. Buka `/admin/imports`.
5. Upload CSV contoh dari `docs/sample_medicines.csv` atau `storage/app/examples/sample_medicines.csv`.
6. Buka `/admin/simulations`.
7. Klik `Low Stock Alert` atau `Application Error`.

Narasi singkat:

"Bagian laporan menampilkan rekap transaksi dan stok, lalu bisa diekspor ke PDF. Import CSV diproses melalui queue Laravel supaya proses tidak membebani request utama. Untuk demo operasional, saya juga menyediakan simulasi alert agar asesor bisa melihat notifikasi, audit log, dan error log."

Catatan jika queue belum jalan:

"Jika status import masih pending, itu karena worker belum berjalan. Solusinya menjalankan `php artisan queue:work`, lalu refresh halaman import."

## 3. Pasien: Katalog, Cart, Checkout Obat Bebas

Durasi: 2-3 menit.

Klik:

1. Logout admin.
2. Login pasien.
3. Buka `/catalog`.
4. Cari `Paracetamol`.
5. Gunakan filter tipe `obat_bebas` jika perlu.
6. Klik detail atau langsung `Tambah Cart`.
7. Buka `/cart`.
8. Klik `Checkout`.
9. Pilih metode pembayaran, misalnya `Transfer`.
10. Klik `Buat Order`.

Narasi singkat:

"Pasien bisa mencari obat di katalog dengan search, filter, dan autocomplete. Untuk obat bebas, checkout langsung selesai dan stok batch dikurangi. Pengurangan stok dilakukan oleh service, bukan hanya dari tampilan."

## 4. Pasien: Checkout Obat Resep

Durasi: 1-2 menit.

Klik:

1. Kembali ke `/catalog`.
2. Cari `Amoxicillin`.
3. Tambahkan ke cart.
4. Checkout.
5. Upload gambar resep demo.
6. Klik `Buat Order`.
7. Tunjukkan status order menunggu verifikasi resep.

Narasi singkat:

"Untuk obat resep, sistem mewajibkan upload resep. Order tidak langsung mengurangi stok sampai apoteker melakukan verifikasi. Ini memisahkan alur obat bebas dan obat resep sesuai kebutuhan klinik."

## 5. Apoteker: Approve atau Reject Resep

Durasi: 2 menit.

Klik:

1. Logout pasien.
2. Login apoteker.
3. Buka `/apoteker/prescriptions`.
4. Klik detail order resep.
5. Lihat preview resep.
6. Klik `Approve Resep` atau `Reject Resep`.
7. Buka `/apoteker/stock-alerts` jika ingin menunjukkan stok kritis/expired.

Narasi singkat:

"Role apoteker bertugas memverifikasi resep. Saat resep disetujui, status order berubah menjadi processing dan stok dikurangi FIFO. Jika resep ditolak, status order berubah rejected dan pasien menerima notifikasi."

## 6. Kasir: Transaksi Offline

Durasi: 1-2 menit.

Klik:

1. Logout apoteker.
2. Login kasir.
3. Buka `/kasir/dashboard`.
4. Klik `Transaksi Baru`.
5. Pilih obat bebas.
6. Isi quantity.
7. Pilih metode pembayaran `Cash`.
8. Klik checkout.
9. Tunjukkan halaman struk.

Narasi singkat:

"Selain penjualan online, sistem juga mendukung transaksi offline di counter. Kasir memilih obat dan quantity, lalu sistem membuat order offline, status pembayaran paid, dan stok tetap dikurangi FIFO."

## 7. Admin Lagi: Audit, Error, Monitoring

Durasi: 1-2 menit.

Klik:

1. Logout kasir.
2. Login admin.
3. Buka `/admin/audit-logs`.
4. Buka `/admin/error-logs`.
5. Buka `/admin/monitoring`.
6. Buka notifikasi di navbar.

Narasi singkat:

"Setiap aksi penting tercatat di audit log, error simulasi masuk ke error log, dan monitoring resource memakai polling ringan 10 detik hanya di halaman monitoring. Notifikasi unread juga dipolling ringan setiap 15 detik saat user login."

## Penutup

Narasi singkat:

"Prototype ini belum dimaksudkan sebagai sistem produksi penuh. Pembayaran masih simulasi, notifikasi masih polling, dan monitoring masih sederhana. Namun alur utama untuk uji kompetensi sudah berjalan: master data, katalog, checkout, resep, FIFO, transaksi kasir, laporan, import CSV, PDF, audit, error log, dan test regression."
