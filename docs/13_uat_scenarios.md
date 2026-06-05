# UAT Scenarios

| ID test | Role | Tujuan | Precondition | Langkah | Data input | Expected result | Actual result | Status |
|---|---|---|---|---|---|---|---|---|
| UAT-001 | Admin | CRUD obat | Login admin | Tambah/edit obat | Paracetamol demo | Obat tersimpan |  |  |
| UAT-002 | Admin | Export laporan | Ada order | Buka laporan, export | Periode bulan ini | PDF terunduh |  |  |
| UAT-003 | Admin | Import CSV | Queue siap | Upload CSV, run queue | sample_medicines.csv | Import completed |  |  |
| UAT-004 | Apoteker | Approve resep | Order pending resep | Detail resep, approve | Catatan OK | Status processing |  |  |
| UAT-005 | Apoteker | Reject resep | Order pending resep | Detail resep, reject | Catatan tidak valid | Status rejected |  |  |
| UAT-006 | Kasir | Transaksi offline | Login kasir | Pilih obat, checkout | Cash | Order offline completed |  |  |
| UAT-007 | Pasien | Register/login | Belum login | Register lalu logout/login | Email baru | Akun aktif |  |  |
| UAT-008 | Pasien | Search filter | Data obat ada | Cari dan filter | Vitamin | Hasil sesuai |  |  |
| UAT-009 | Pasien | Checkout bebas | Stok cukup | Cart dan checkout | Paracetamol | Order completed |  |  |
| UAT-010 | Pasien | Checkout resep | Stok cukup | Cart obat resep, upload | Amoxicillin + image | Waiting prescription |  |  |
| UAT-011 | Admin | Alert stok | Data stok kritis | Simulasi alert | Low stock | Notifikasi dibuat |  |  |
| UAT-012 | Admin | Error log | Login admin | Simulate error | Critical | Error log muncul |  |  |
