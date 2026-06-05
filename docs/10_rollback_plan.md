# Rollback Plan

## Kondisi Rollback

Database corrupt, stok mayoritas salah, checkout gagal massal, atau akses role kritis bocor.

## Backup Database

Ambil dump database sebelum cutover dan sebelum import besar.

## Restore Data

Matikan app sementara, restore dump, jalankan migration hanya jika kompatibel, lalu verifikasi login dan stok.

## Kembali Manual

Gunakan form penjualan manual dan kartu stok sementara. Semua transaksi manual selama downtime dicatat untuk diinput ulang.

## Komunikasi

Admin menginformasikan status ke kasir, apoteker, manajemen, dan pasien jika layanan online ditunda.
