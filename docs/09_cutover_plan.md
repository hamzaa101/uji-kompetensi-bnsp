# Cutover Plan

## Timeline

- H-3: backup data manual, finalisasi CSV, training user.
- H-1: dry run import dan verifikasi stok.
- H: freeze update spreadsheet, import final, validasi, go-live.
- H+1: monitoring transaksi dan stok.

## Checklist Pra-Cutover

Database siap, akun user aktif, queue worker berjalan, storage link aktif, backup tersedia, dan kasir/apoteker sudah mencoba UAT.

## Langkah Cutover

1. Stop update spreadsheet manual.
2. Backup data lama.
3. Import master obat dan batch.
4. Verifikasi stok sampling.
5. Aktifkan transaksi online/offline.

## Verifikasi Pasca-Cutover

Login role, search obat, checkout, verifikasi resep, transaksi kasir, laporan, dan alert.

## PIC

Admin klinik, apoteker senior, kasir lead, dan IT support.

## Downtime Plan

Jika sistem gagal lebih dari 30 menit, kembali ke transaksi manual sementara dan input ulang setelah pulih.
