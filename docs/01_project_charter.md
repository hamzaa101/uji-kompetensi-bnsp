# Project Charter

## Nama Proyek

Sistem E-Commerce Penjualan Obat Berbasis Web pada Klinik Makmur Jaya.

## Latar Belakang

Klinik melayani 150-200 pasien per hari dan memiliki lebih dari 2.000 jenis obat. Proses penjualan, stok, laporan, dan verifikasi resep masih manual sehingga rawan selisih stok, keterlambatan laporan, dan kesalahan verifikasi.

## Tujuan

Menyediakan prototype Laravel yang mendukung penjualan obat online/offline, monitoring stok real-time-like, verifikasi resep, laporan, audit, dan dokumentasi asesmen.

## Stakeholder

Admin klinik, apoteker, kasir, pasien, manajemen klinik, asesor BNSP, dan tim IT.

## Scope

Auth multi-role, master data obat, batch stok FIFO, katalog, cart, checkout, resep, kasir offline, laporan SQL, PDF, CSV import queue, notifikasi, monitoring, audit log, error log, dan dokumen operasional.

## Out of Scope

Payment gateway produksi, integrasi BPJS/asuransi nyata, e-prescription resmi, integrasi gudang eksternal, dan deployment cloud produksi.

## Deliverables

Aplikasi Laravel, migration/seeder, UI demo, tests, README, sample CSV, laporan PDF, dan dokumen asesmen.

## Risiko Utama

Data stok tidak akurat, upload file berbahaya, akses role bocor, queue tidak berjalan, dan downtime saat cutover.

## Success Criteria

Akun demo login, CRUD dan checkout berjalan, resep dapat diverifikasi, FIFO terbukti, laporan/PDF/import/alert/audit dapat didemonstrasikan, dan dokumentasi lengkap.
