# Debugging Report

## BUG-001 Stok Tidak Cukup Saat Checkout

- Deskripsi: Checkout gagal ketika quantity melebihi total batch.
- Langkah reproduksi: Pasien tambah obat stok rendah lebih dari stok tersedia lalu checkout.
- Expected result: Sistem menolak dengan pesan stok tidak cukup.
- Actual result: Validasi menampilkan error stok tidak cukup.
- Root cause: Quantity request lebih besar dari total `medicine_batches.quantity`.
- File yang diperbaiki: `app/Services/StockService.php`, `app/Services/CheckoutService.php`.
- Solusi: Tambahkan `assertAvailable` sebelum order dibuat dan transaction rollback.
- Verifikasi: Feature test checkout insufficient stock.

## BUG-002 Upload Resep Format Salah

- Deskripsi: Pasien upload file non-image.
- Langkah reproduksi: Checkout obat resep dengan file `.pdf`.
- Expected result: Ditolak.
- Actual result: Validator menolak file.
- Root cause: Rule file harus image jpg/png/webp.
- File yang diperbaiki: `app/Http/Controllers/Patient/CheckoutController.php`.
- Solusi: Rule `image|mimes:jpg,jpeg,png,webp|max:2048`.
- Verifikasi: Upload non-image menghasilkan validation error.

## BUG-003 Pasien Akses Admin Dashboard

- Deskripsi: Role pasien mencoba `/admin/dashboard`.
- Langkah reproduksi: Login pasien lalu buka URL admin.
- Expected result: HTTP 403.
- Actual result: Middleware role mengembalikan 403.
- Root cause: Role tidak termasuk admin.
- File yang diperbaiki: `app/Http/Middleware/RoleMiddleware.php`.
- Solusi: Abort jika user role tidak sesuai.
- Verifikasi: Feature test role access.
