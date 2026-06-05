# Troubleshooting Guide

## Tidak Bisa Login

Pastikan migrate seed sudah jalan dan gunakan akun demo yang benar.

## Gagal Upload Gambar

Pastikan file jpg/png/webp dan ukuran maksimal 2MB.

## Gagal Checkout

Cek cart tidak kosong, stok cukup, dan resep diupload untuk obat resep.

## Stok Tidak Cukup

Tambahkan batch stok atau kurangi quantity.

## Queue Tidak Berjalan

Jalankan `php artisan queue:work`.

## PDF Gagal Dibuat

Jalankan `composer install` dan pastikan ekstensi `dom`, `gd`, `mbstring` aktif.

## Storage Link Error

Jalankan `php artisan storage:link`. Jika link sudah ada, hapus link lama secara hati-hati lalu buat ulang.

## Error 500

Cek `storage/logs/laravel.log`, error log dashboard, dan `.env`.

## Database Belum Migrate

Jalankan `php artisan migrate --seed` atau `php artisan migrate:fresh --seed` untuk demo.

## Notifikasi Tidak Muncul

Pastikan user login dan endpoint `/notifications/unread-count` dapat diakses.
