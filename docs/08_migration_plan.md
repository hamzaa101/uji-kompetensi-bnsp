# Migration Plan

## Sumber Data

Spreadsheet/manual apotek berisi nama obat, kategori, supplier, harga, stok, nomor batch, dan expiry date.

## Mapping Field

- `nama_obat` -> `medicines.name`
- `kategori` -> `categories.name`
- `supplier` -> `suppliers.name`
- `jenis` -> `medicines.type`
- `harga_jual` -> `medicines.price`
- `stok_minimum` -> `medicines.min_stock`
- `nomor_batch` -> `medicine_batches.batch_number`
- `stok` -> `medicine_batches.quantity`
- `tanggal_expired` -> `medicine_batches.expiry_date`

## Validasi

Nama wajib, harga numeric, quantity integer, expiry date valid, kategori/type sesuai opsi.

## Data Cleansing

Samakan kapitalisasi, hilangkan duplikasi nama/batch, validasi expired, dan cek supplier kosong.

## Dry Run

Import CSV ke database demo, cek jumlah row, cek stok, dan bandingkan laporan.

## Rollback Data

Backup database sebelum import. Jika gagal, restore backup atau hapus import berdasarkan batch/created_at.
