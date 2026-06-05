# Scalability Analysis

## Beban

Klinik melayani 150-200 pasien/hari dengan 2.000 obat. Transaksi online/offline relatif kecil, tetapi search katalog, dashboard, report, dan import dapat meningkat saat operasional sibuk.

## Strategi Scaling

- Index `medicines.name`, `orders.status`, `orders.created_at`, dan batch expiry.
- Pagination untuk tabel besar.
- Queue untuk import CSV dan generate laporan besar.
- Cache dashboard/report ringkas jika data tumbuh.
- Pisahkan storage file ke object storage dan CDN untuk gambar.
- Horizontal scaling app server dengan session/cache terpusat.
- Monitoring dengan Pulse/Telescope untuk dev dan Prometheus/Grafana/Sentry untuk produksi.

## Bottleneck

Report raw SQL tanpa cache, upload file besar, queue worker tidak aktif, dan transaksi stok bersamaan pada batch yang sama.
