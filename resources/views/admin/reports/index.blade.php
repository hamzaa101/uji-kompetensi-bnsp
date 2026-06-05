@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="space-y-4">
    <div class="panel">
        <div class="toolbar">
            <div><h1 class="text-xl font-semibold">Laporan Penjualan</h1><p class="text-sm text-slate-600">Query laporan menggunakan raw SQL aman dengan binding.</p></div>
            <div class="flex gap-2">
                <a class="btn btn-primary" href="{{ route('admin.reports.pdf', request()->query()) }}">Export PDF</a>
                <form method="post" action="{{ route('admin.reports.generate-job', request()->query()) }}">@csrf <button class="btn btn-muted" type="submit">Queue Report</button></form>
            </div>
        </div>
        <form class="mt-4 flex flex-wrap gap-2">
            <input type="date" name="from" value="{{ $from }}">
            <input type="date" name="to" value="{{ $to }}">
            <button class="btn btn-primary" type="submit">Terapkan</button>
        </form>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="panel">
            <h2 class="section-title">Penjualan Harian</h2>
            <div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Transaksi</th><th>Omzet</th></tr></thead><tbody>@foreach($daily as $row)<tr><td>{{ $row->period }}</td><td>{{ $row->transactions }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Penjualan Bulanan</h2>
            <div class="table-wrap"><table><thead><tr><th>Bulan</th><th>Transaksi</th><th>Omzet</th></tr></thead><tbody>@foreach($monthly as $row)<tr><td>{{ $row->period }}</td><td>{{ $row->transactions }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Obat Terlaris</h2>
            <div class="table-wrap"><table><thead><tr><th>Obat</th><th>Qty</th><th>Revenue</th></tr></thead><tbody>@foreach($topMedicines as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->sold_qty }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Rekap Status</h2>
            <div class="table-wrap"><table><thead><tr><th>Status</th><th>Total</th><th>Revenue</th></tr></thead><tbody>@foreach($statusRecap as $row)<tr><td>{{ $row->status }}</td><td>{{ $row->total }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Obat Hampir Kedaluwarsa</h2>
            <div class="table-wrap"><table><thead><tr><th>Obat</th><th>Batch</th><th>Qty</th><th>Expired</th></tr></thead><tbody>@foreach($expiring as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->batch_number }}</td><td>{{ $row->quantity }}</td><td>{{ $row->expiry_date }}</td></tr>@endforeach</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Stok Kritis</h2>
            <div class="table-wrap"><table><thead><tr><th>Obat</th><th>Stok</th><th>Min</th></tr></thead><tbody>@foreach($criticalStock as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->stock }}</td><td>{{ $row->min_stock }}</td></tr>@endforeach</tbody></table></div>
        </div>
    </div>
</div>
@endsection
