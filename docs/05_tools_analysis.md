# Tools Analysis

## Laravel 13

Dipilih karena versi stabil terbaru di environment ini adalah Laravel 13.14.0 dan PHP 8.3.16 memenuhi requirement PHP 8.3+. Laravel MVC cocok untuk prototype asesmen yang mudah dibaca.

## Blade dan Tailwind

Blade menjaga struktur server-rendered sederhana, cepat didemokan, dan tidak memerlukan SPA. Tailwind CSS 4 dipakai untuk UI konsisten dan build Vite modern.

## SQL Database

SQLite dipakai sebagai fallback lokal stabil. Migration Laravel tetap portable untuk MySQL/PostgreSQL.

## Queue Database Driver

Database queue mudah disiapkan untuk demo tanpa Redis, cocok untuk import CSV dan generate report job.

## Chart.js

Chart.js dipakai untuk grafik dashboard ringan, populer, MIT License.

## Dompdf

`dompdf/dompdf` v3.1 dipilih untuk export PDF server-side tanpa browser headless. Lisensi LGPL-2.1.

## Package Utama

- laravel/framework 13.14.0, MIT.
- dompdf/dompdf 3.1, LGPL-2.1.
- chart.js, MIT.
- tailwindcss, MIT.
- vite, MIT.
