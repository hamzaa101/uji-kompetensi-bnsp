# Refactor Audit Tahap 1

Tanggal audit: 2026-06-06

## Status Terbaru

Audit ini adalah baseline awal sebelum perbaikan bertahap. Kondisi terbaru setelah tahap layout, UI, Chart.js, polling, regression test, dan demo readiness dirangkum di:

- `docs/21_ui_refactor_log.md`
- `docs/22_demo_checklist.md`

Beberapa risiko yang ditulis di audit ini sudah ditangani, terutama sidebar responsive, chart lifecycle, polling notifikasi/monitoring, dan regression test alur demo.

Mulai bagian berikutnya sampai akhir dokumen adalah catatan historis kondisi awal sebelum refactor. Untuk status terkini, gunakan dokumen tahap 21 dan checklist tahap 22.

## Scope Audit

Audit ini dilakukan sebagai baseline sebelum refactor bertahap. Tidak ada refactor besar, penggantian stack, atau perubahan fitur bisnis pada tahap ini.

File yang diperiksa:

- `routes/web.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/monitoring/index.blade.php`
- `resources/css/app.css`
- `resources/js/app.js`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/MonitoringController.php`
- `app/Http/Controllers/NotificationController.php`
- `app/Services/ReportService.php`
- `app/Services/ResourceMonitoringService.php`
- `package.json`
- `composer.json`

## Validasi Baseline

Dependency sudah tersedia, jadi `composer install` dan `npm install` tidak dijalankan ulang.

Hasil validasi:

- `php artisan migrate:fresh --seed`: berhasil pada database demo SQLite.
- `npm run build`: berhasil.
- `php artisan test`: berhasil, 10 test, 31 assertion.
- `php artisan route:list`: berhasil, 75 route.
- `composer validate`: berhasil.
- HTTP smoke test dengan `curl`: berhasil membuka login, login admin, dan mencapai dashboard admin.

Catatan browser:

In-app Browser MCP tersedia, tetapi runtime browser gagal start dua kali karena masalah sandbox Windows (`windows sandbox failed: spawn setup refresh`). Karena itu, audit visual langsung, console browser, dan observasi real-time tinggi canvas belum bisa dibuktikan lewat browser pada tahap ini. Temuan UI/performa di bawah ini berasal dari inspeksi Blade/CSS/JS dan HTTP smoke test.

## Ringkasan Masalah yang Ditemukan

### 1. Sidebar selalu terbuka dan belum punya state toggle

Layout utama selalu merender sidebar untuk user login:

- `resources/views/layouts/app.blade.php:36` memakai grid `lg:grid-cols-[220px_1fr]`.
- `resources/views/layouts/app.blade.php:38` merender `<aside>` tanpa tombol toggle, state open/closed, overlay mobile, atau atribut aksesibilitas.

Dampak:

- Di layar kecil, sidebar muncul sebagai blok biasa di atas konten.
- Tidak ada mekanisme buka-tutup navbar/sidebar.
- Potensi membuat halaman terasa penuh dan tumpang tindih, terutama ketika menu role admin panjang.

### 2. Navbar berpotensi tumpang tindih pada viewport kecil

Navbar menggunakan flex horizontal tetap:

- `resources/views/layouts/app.blade.php:15` memakai `nav class="flex items-center gap-3 text-sm"`.

Dampak:

- Link katalog, dashboard, notifikasi, badge role, dan tombol logout dapat berebut ruang.
- Tidak ada menu mobile, wrapping strategy, atau prioritas item.

### 3. Chart.js dibuat langsung di inline script tanpa lifecycle guard

Dashboard admin membuat chart langsung saat `DOMContentLoaded`:

- `resources/views/admin/dashboard.blade.php:24` canvas `salesChart`.
- `resources/views/admin/dashboard.blade.php:28` canvas `statusChart`.
- `resources/views/admin/dashboard.blade.php:62` listener `DOMContentLoaded`.
- `resources/views/admin/dashboard.blade.php:65` dan `:71` memanggil `new Chart(...)`.
- `resources/views/admin/dashboard.blade.php:68` dan `:74` memakai `maintainAspectRatio: false`.

Dampak:

- Pada aplikasi multi-page biasa, script ini hanya berjalan saat halaman dashboard dimuat. Jadi memory leak besar belum terbukti dari kode ini saja.
- Namun chart tidak disimpan sebagai instance, tidak ada `destroy()`, dan belum ada guard jika script dieksekusi ulang oleh hot reload/dev tooling.
- Animasi Chart.js default masih aktif. Ini bisa terasa berat di laptop rendah, terutama saat dev server/Vite aktif.
- Canvas memakai class CSS `chart-box`, tetapi belum ada wrapper chart khusus dengan dimensi stabil selain `h-72`.

### 4. Chart.js dibundle global untuk semua halaman

`resources/js/app.js:1` mengimpor `chart.js/auto` dan `resources/js/app.js:3` menaruhnya ke `window.Chart`.

Dampak:

- Semua halaman memuat Chart.js walaupun chart hanya dipakai di dashboard admin.
- Build JS tercatat sekitar 202 KB sebelum gzip.
- Ini bukan bug fatal, tetapi menjadi kandidat optimasi tahap berikutnya.

### 5. Polling notifikasi global belum punya pause/cleanup

`resources/js/app.js:25-26` langsung memanggil `refreshNotificationCount()` dan `setInterval(refreshNotificationCount, 7000)`.

Dampak:

- Selama user login, polling berjalan di semua halaman.
- Tidak ada pause saat tab hidden, tidak ada `AbortController`, dan tidak ada backoff saat request gagal.
- Pada aplikasi multi-page, interval lama hilang saat navigasi penuh. Namun saat dev/HMR atau jika nanti ditambah partial navigation, ini dapat menjadi sumber interval ganda.

### 6. Polling monitoring berjalan setiap 5 detik tanpa guard

Halaman monitoring memiliki polling inline:

- `resources/views/admin/monitoring/index.blade.php:24` memakai `setInterval`.
- `resources/views/admin/monitoring/index.blade.php:25` fetch ke `admin.monitoring.latest`.
- `resources/views/admin/monitoring/index.blade.php:27-32` update DOM langsung.

Dampak:

- Polling ini hanya aktif di halaman monitoring, bukan dashboard admin.
- Tidak ada pause saat tab hidden, abort request yang belum selesai, atau error handling.
- Jika endpoint lambat, request bisa menumpuk.

### 7. Query dashboard masih cukup berat untuk data besar

`DashboardController` memanggil:

- `ReportService::dashboardStats()`
- `ReportService::salesPerDay()`
- `Order::selectRaw(...)->groupBy(...)`
- `ResourceMonitoringService::latest()`

Di `ReportService::dashboardStats()`, `criticalMedicines()` mengambil semua medicine aktif lalu filter di PHP. Untuk prototype 30 obat ini aman, tetapi untuk target 2.000 obat, ini bisa mulai terasa.

Dampak:

- Dashboard admin bisa lambat saat data tumbuh.
- Beban ini terpisah dari Chart.js, tetapi efeknya bisa terasa seperti dashboard "berat".

### 8. Beberapa komponen CSS belum punya batas layout yang kuat

Referensi CSS:

- `.side-link`: `resources/css/app.css:66`
- `.stat strong`: `resources/css/app.css:98`
- `.actions`: `resources/css/app.css:110`
- `.chart-box`: `resources/css/app.css:150`
- `.autocomplete`: `resources/css/app.css:166`

Dampak:

- Tombol aksi di tabel bisa memanjang dan membuat cell terasa padat.
- Nilai statistik besar dapat mendorong card.
- Autocomplete memakai `position: absolute`, tetapi container pemanggil perlu `relative`; kalau tidak, posisinya bisa tidak stabil.
- Chart height saat ini `h-72`, tetapi belum ada wrapper chart yang mengunci tinggi panel secara eksplisit.

## File yang Kemungkinan Menjadi Penyebab

Prioritas penyebab UI:

1. `resources/views/layouts/app.blade.php`
   - Sidebar selalu terbuka.
   - Navbar tidak punya mobile/toggle pattern.
   - Tidak ada state layout.

2. `resources/css/app.css`
   - Belum ada kelas khusus untuk shell responsive, sidebar collapsed, chart wrapper, dan table action constraints.

3. `resources/views/admin/dashboard.blade.php`
   - Chart dibuat inline.
   - Belum ada lifecycle guard/destroy/disable animation.

4. `resources/js/app.js`
   - Chart.js global.
   - Polling notifikasi global tanpa pause/backoff.

5. `resources/views/admin/monitoring/index.blade.php`
   - Polling monitoring inline tanpa error handling/pause/abort.

Prioritas penyebab performa:

1. `resources/js/app.js`
   - Bundle Chart.js global.
   - Polling notifikasi jalan di semua halaman login.

2. `resources/views/admin/dashboard.blade.php`
   - Chart default animation dan no lifecycle guard.

3. `app/Services/ReportService.php`
   - Beberapa stats masih collection-heavy untuk data besar.

4. `app/Services/ResourceMonitoringService.php`
   - Query metrics ringan saat ini, tetapi perlu cache/throttle jika dipanggil sering.

## Prioritas Perbaikan

### Prioritas 1: UI shell responsive

Target:

- Tambah tombol toggle sidebar.
- Sidebar default tertutup di mobile, terbuka di desktop.
- Tambah overlay mobile.
- Navbar dibuat wrap/collapse dengan aman.

Alasan:

Ini langsung menyasar masalah paling terlihat: tumpang tindih dan sidebar selalu terbuka.

### Prioritas 2: Stabilkan chart dashboard

Target:

- Pindahkan init chart dari inline Blade ke modul JS kecil atau fungsi terkontrol.
- Simpan instance chart dan destroy sebelum re-init.
- Matikan animasi atau batasi durasi.
- Tambah wrapper chart dengan tinggi stabil, misalnya `h-64 md:h-72`.
- Pastikan init hanya berjalan jika canvas ada.

Alasan:

Ini mengurangi risiko render ulang, resize loop, dan beban CPU dari Chart.js.

### Prioritas 3: Polling lebih hemat

Target:

- Polling notifikasi pause saat `document.hidden`.
- Gunakan satu interval global yang tersimpan.
- Tambah `AbortController` atau guard request in-flight.
- Monitoring polling diberi try/catch dan tidak menumpuk request.

Alasan:

Mengurangi beban background tanpa menghapus fitur notifikasi/monitoring.

### Prioritas 4: Optimasi query dashboard

Target:

- Ubah stok kritis dari filter PHP menjadi agregasi SQL.
- Cache statistik dashboard singkat, misalnya 30-60 detik.
- Pisahkan data chart dari initial page jika perlu.

Alasan:

Performa dashboard akan lebih stabil saat data mendekati 2.000 obat.

### Prioritas 5: Perapihan komponen tabel/form

Target:

- Tetapkan min-width tabel tertentu.
- Batasi `.actions` agar tombol tidak merusak cell.
- Tambah kelas utilitas untuk text truncate dan wrapping angka panjang.
- Pastikan autocomplete berada dalam container `relative`.

Alasan:

Mengurangi tumpang tindih minor lintas halaman tanpa mengubah fitur.

## Risiko Jika Diperbaiki Sekaligus

- Refactor layout besar bisa memecah semua halaman karena semua view memakai `layouts.app`.
- Memindahkan Chart.js sekaligus ke modul baru dapat membuat dashboard kosong jika asset/build salah.
- Mengubah polling dan controller bersamaan bisa membuat notifikasi/monitoring tampak mati.
- Optimasi query dashboard berisiko mengubah angka statistik jika tidak ditest dengan data seed.
- Terlalu banyak perubahan UI sekaligus membuat sulit membedakan bug layout dari bug data.

## Rencana Refactor Bertahap

### Tahap 2: Layout Shell Minimal

- Tambah toggle sidebar di `layouts.app`.
- Tambah CSS untuk mobile sidebar.
- Tambah JS kecil untuk open/close.
- Test: login admin, dashboard, katalog, mobile width.

### Tahap 3: Chart Stabil

- Buat helper init chart di `resources/js/app.js` atau file khusus dashboard.
- Guard canvas exists.
- Simpan instance di `window.KMJCharts`.
- Destroy instance sebelum re-init.
- Disable/kurangi animation.
- Test: reload dashboard beberapa kali, cek tinggi canvas tetap.

### Tahap 4: Polling Hemat

- Refactor notification polling dengan visibility guard dan in-flight guard.
- Refactor monitoring polling dengan try/catch dan interval id.
- Test: endpoint tetap jalan, tidak ada console error saat halaman bukan monitoring.

### Tahap 5: Query Dashboard

- Ganti critical stock count ke SQL aggregation.
- Tambah cache pendek untuk stats.
- Test: nilai dashboard tetap sesuai seed dan test tetap hijau.

### Tahap 6: Layout Komponen

- Perbaiki table action, stat cards, autocomplete positioning.
- Test halaman CRUD utama dan katalog.

## Rekomendasi Batas Perubahan Tahap Berikutnya

Mulai dari Tahap 2 saja: layout shell responsive. Jangan sekaligus mengubah chart dan query agar dampaknya mudah diukur.
