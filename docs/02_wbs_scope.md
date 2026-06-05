# WBS dan Scope

## WBS Modul

- Setup Laravel 13, dependency, database, Vite.
- Auth dan role access admin, apoteker, kasir, pasien.
- Master data kategori, supplier, obat, batch.
- Katalog, cart, checkout, resep.
- FIFO stock service dan stock movement.
- Dashboard, laporan SQL, PDF export.
- Import CSV queue dan sample CSV.
- Notifikasi, monitoring, error log, audit log.
- Testing, README, dokumen asesmen.

## Timeline 3 Hari / 18 Jam

- Hari 1: setup, auth, database, seeder, CRUD utama.
- Hari 2: katalog, cart, checkout, resep, kasir, FIFO.
- Hari 3: dashboard, report, PDF, import, monitoring, docs, testing, demo polish.

## In Scope

Prototype lokal siap demo dengan fitur utama tersambung database.

## Out of Scope

Payment gateway, email production, cloud deployment, integrasi ERP.

## Dependency

Auth diperlukan sebelum role UI. Medicine dan batch diperlukan sebelum cart, checkout, kasir, alert, dan laporan. Queue diperlukan sebelum import CSV berjalan asynchronous.
