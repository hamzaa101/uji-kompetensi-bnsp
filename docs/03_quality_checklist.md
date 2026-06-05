# Quality Checklist

## Fitur

- [ ] Semua akun demo bisa login.
- [ ] Role middleware memblokir akses tidak sah.
- [ ] Admin CRUD kategori, supplier, obat, batch.
- [ ] Pasien checkout obat bebas dan obat resep.
- [ ] Apoteker approve/reject resep.
- [ ] Kasir transaksi offline.

## Keamanan

- [ ] Password hashed.
- [ ] CSRF aktif.
- [ ] Validasi input server-side.
- [ ] Upload hanya jpg/png/webp max 2MB.
- [ ] Raw SQL memakai binding.
- [ ] Blade memakai escaped output.

## Database

- [ ] Migration fresh sukses.
- [ ] Seeder demo lengkap.
- [ ] Index pada kolom search/status/tanggal.
- [ ] Stock movement tercatat.

## UI

- [ ] Tabel responsif.
- [ ] Empty state jelas.
- [ ] Badge status/stok jelas.
- [ ] Form error terlihat.

## Deployment

- [ ] `.env` tidak berisi credential produksi.
- [ ] `storage:link` dijalankan.
- [ ] Queue worker aktif.
- [ ] Build asset sukses.

## Dokumentasi

- [ ] README dapat diikuti dari nol.
- [ ] UAT, debugging, cutover, rollback, impact analysis tersedia.
