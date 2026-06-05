# Impact Analysis

## Perubahan

Fitur stok FIFO mengurangi batch dengan expiry date paling dekat terlebih dahulu.

## Dampak

- Cart: perlu validasi total stok sebelum checkout.
- Checkout: stok dikurangi saat obat bebas selesai atau resep disetujui.
- Kasir: transaksi offline langsung mengurangi batch.
- Laporan: stock movement menjadi sumber audit stok.
- Alert: stok kritis berubah setelah transaksi.
- Import: batch baru mempengaruhi urutan FIFO.

## Mitigasi

Gunakan `StockService`, DB transaction, validasi stok, dan audit movement.

## Regression Test

Login role, tambah cart, checkout stok cukup/tidak cukup, approve resep, transaksi kasir, report, dan FIFO unit test.
