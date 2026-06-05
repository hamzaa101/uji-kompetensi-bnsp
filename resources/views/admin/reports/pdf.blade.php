<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 22px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan Klinik Makmur Jaya</h1>
    <p>Periode: {{ $from }} sampai {{ $to }}</p>
    <h2>Penjualan Harian</h2>
    <table><thead><tr><th>Tanggal</th><th>Transaksi</th><th>Omzet</th></tr></thead><tbody>@foreach($daily as $row)<tr><td>{{ $row->period }}</td><td>{{ $row->transactions }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table>
    <h2>Obat Terlaris</h2>
    <table><thead><tr><th>Obat</th><th>Qty</th><th>Revenue</th></tr></thead><tbody>@foreach($topMedicines as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->sold_qty }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table>
    <h2>Rekap Status</h2>
    <table><thead><tr><th>Status</th><th>Total</th><th>Revenue</th></tr></thead><tbody>@foreach($statusRecap as $row)<tr><td>{{ $row->status }}</td><td>{{ $row->total }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table>
    <h2>Stok Kritis</h2>
    <table><thead><tr><th>Obat</th><th>Stok</th><th>Min</th></tr></thead><tbody>@foreach($criticalStock as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->stock }}</td><td>{{ $row->min_stock }}</td></tr>@endforeach</tbody></table>
</body>
</html>
