# UI Refactor Log

Tanggal update: 2026-06-06

## Masalah Awal

- Layout utama sering tumpang tindih pada layar kecil.
- Sidebar selalu terbuka dan belum memiliki state open/closed.
- Navbar, sidebar, dan konten utama belum punya shell responsive yang stabil.
- Dashboard admin terasa berat karena Chart.js dibuat langsung dari inline Blade tanpa lifecycle helper.
- Polling notifikasi dan monitoring belum cukup aman dari interval ganda, request overlap, dan error fetch.
- Beberapa table, form, card katalog, cart, dan checkout masih padat dan kurang siap demo asesor.

## Perbaikan Layout

File utama:

- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/partials/navbar.blade.php`
- `resources/views/layouts/partials/sidebar.blade.php`
- `resources/views/layouts/partials/flash.blade.php`
- `resources/css/app.css`

Hasil:

- Navbar sticky dengan nama aplikasi, toggle sidebar, role, notifikasi, dan logout.
- Sidebar desktop fixed/collapsible.
- Sidebar mobile off-canvas dengan overlay.
- Main content tidak tertutup sidebar.
- State sidebar desktop disimpan di `localStorage`.
- Flash message dan validation error lebih rapi.

## Perbaikan Halaman Utama

Komponen/kelas yang ditambahkan:

- `resources/views/components/page-header.blade.php`
- `resources/views/components/field-error.blade.php`
- Reusable CSS untuk `page-header`, `filter-panel`, `table-wrap`, `form-actions`, `summary-row`, `chart-frame`, dan preview image.

Halaman yang dirapikan:

- Admin: dashboard, medicines, categories, suppliers, batches, reports, imports, monitoring, error logs, audit logs, simulations.
- Pasien: catalog, detail catalog, cart, checkout, orders.
- Apoteker: dashboard, prescriptions, stock alerts.
- Kasir: dashboard dan sales create.

## Perbaikan Chart

File utama:

- `resources/js/app.js`
- `resources/views/admin/dashboard.blade.php`
- `resources/css/app.css`

Hasil:

- Chart tidak lagi dibuat langsung dengan `new Chart(...)` dari Blade.
- Ditambahkan registry `window.KlinikCharts`.
- `createOrUpdateChart(id, config)` akan update chart existing atau destroy instance lama jika tipe berubah.
- Animasi chart dimatikan untuk demo yang lebih ringan.
- `responsive: true` dan `maintainAspectRatio: false`.
- Canvas dashboard dibungkus `.chart-frame` dengan tinggi tetap.
- Dashboard admin tidak punya polling chart.

## Perbaikan Polling dan Progressive Enhancement

File utama:

- `resources/js/app.js`
- `resources/views/layouts/app.blade.php`
- `resources/views/admin/monitoring/index.blade.php`
- `resources/views/catalog/index.blade.php`
- `resources/views/cart/checkout.blade.php`
- `resources/views/admin/medicines/form.blade.php`
- `resources/views/notifications/index.blade.php`

Hasil:

- Inline script eksekusi dipindahkan ke `resources/js/app.js`.
- Blade memakai `data-*` untuk endpoint dan target element.
- Notifikasi unread count polling setiap 15 detik, hanya jika user login dan badge tersedia.
- Monitoring resource polling setiap 10 detik, hanya di halaman monitoring.
- Fetch memakai in-flight guard dan `AbortController`.
- Autocomplete katalog debounce 300 ms, abort request lama, dan dropdown tertutup saat klik luar/Escape.
- Preview upload gambar/resep memakai `URL.createObjectURL` dan revoke URL lama.

## Regression Test dan Bug Fix

File:

- `tests/Feature/DemoFlowRegressionTest.php`
- `app/Http/Controllers/Kasir/SaleController.php`

Bug yang diperbaiki:

- Form kasir memiliki beberapa baris item kosong, tetapi validasi lama mewajibkan semua baris terisi. Ini bisa membuat transaksi offline gagal saat demo.
- Fix: baris item boleh kosong, lalu controller memfilter baris valid dan tetap mewajibkan minimal satu item valid.

Coverage regression:

- Role access.
- Katalog dan autocomplete.
- Checkout obat bebas.
- Checkout obat resep wajib upload resep.
- Approve resep oleh apoteker.
- FIFO stock deduction.
- Transaksi offline kasir.
- Admin report/PDF/import/monitoring/log/simulasi.

## Hasil Validasi

Validasi terakhir:

- `php artisan migrate:fresh --seed`: berhasil.
- `npm.cmd run build`: berhasil.
- `php artisan test`: berhasil, 16 test, 76 assertion.
- `php artisan route:list`: berhasil, 75 route.
- HTTP smoke halaman prioritas: berhasil pada tahap UI/refactor.

Catatan:

- Browser/Playwright MCP beberapa kali gagal start karena sandbox Windows (`windows sandbox failed: spawn setup refresh`), sehingga cek console/network otomatis tidak tersedia di sesi ini.
- Dry-run manual di browser tetap disarankan sebelum presentasi.
