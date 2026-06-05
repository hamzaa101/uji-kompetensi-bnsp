# FAQ

1. Apa password akun demo? `password123`.
2. Apakah pembayaran sungguhan? Tidak, masih simulasi prototype.
3. Apakah obat resep wajib upload resep? Ya.
4. Kapan stok dikurangi? Obat bebas saat checkout selesai, obat resep saat apoteker approve, offline saat kasir checkout.
5. Apa itu FIFO? Batch expiry terdekat dikurangi lebih dulu.
6. Kenapa import belum selesai? Jalankan `php artisan queue:work`.
7. Apakah PDF memakai package? Ya, dompdf/dompdf.
8. Apakah notifikasi real-time? Menggunakan polling 5-10 detik.
9. Apakah bisa MySQL/PostgreSQL? Migration portable, demo lokal memakai SQLite.
10. Apakah email aktif? Default mail log, email produksi perlu konfigurasi.
11. Apakah audit log otomatis? Aksi utama auth/CRUD/checkout/import/export/simulasi dicatat.
12. Apakah upload aman? File divalidasi image jpg/png/webp max 2MB.
