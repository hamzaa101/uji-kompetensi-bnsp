@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Laporan Penjualan"
        description="Rekap transaksi, omzet, stok kritis, dan batch hampir kedaluwarsa untuk kebutuhan evaluasi."
    >
        <x-slot:actions>
            <a class="btn btn-primary" href="{{ route('admin.reports.pdf', request()->query()) }}">Export PDF</a>
            <form method="post" action="{{ route('admin.reports.generate-job', request()->query()) }}">@csrf <button class="btn btn-muted" type="submit">Queue Report</button></form>
        </x-slot:actions>
    </x-page-header>

    <div class="filter-panel">
        <form class="filter-actions">
            <input type="date" name="from" value="{{ $from }}">
            <input type="date" name="to" value="{{ $to }}">
            <button class="btn btn-primary" type="submit">Terapkan</button>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="stat"><span>Total Penjualan</span><strong>Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</strong></div>
        <div class="stat"><span>Total Transaksi</span><strong>{{ number_format($summary['transactions'], 0, ',', '.') }}</strong></div>
        <div class="stat"><span>Periode</span><strong class="stat-range">{{ $from }} - {{ $to }}</strong></div>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="panel">
            <h2 class="section-title">Penjualan Harian</h2>
            <div class="table-wrap mt-4"><table><thead><tr><th>Tanggal</th><th>Transaksi</th><th>Omzet</th></tr></thead><tbody>@forelse($daily as $row)<tr><td>{{ $row->period }}</td><td>{{ $row->transactions }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="3" class="empty">Belum ada data.</td></tr>@endforelse</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Penjualan Bulanan</h2>
            <div class="table-wrap mt-4"><table><thead><tr><th>Bulan</th><th>Transaksi</th><th>Omzet</th></tr></thead><tbody>@forelse($monthly as $row)<tr><td>{{ $row->period }}</td><td>{{ $row->transactions }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="3" class="empty">Belum ada data.</td></tr>@endforelse</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Obat Terlaris</h2>
            <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Qty</th><th>Revenue</th></tr></thead><tbody>@forelse($topMedicines as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->sold_qty }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="3" class="empty">Belum ada data.</td></tr>@endforelse</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Rekap Status</h2>
            <div class="table-wrap mt-4"><table><thead><tr><th>Status</th><th>Total</th><th>Revenue</th></tr></thead><tbody>@forelse($statusRecap as $row)<tr><td><span class="status status-info">{{ $row->status }}</span></td><td>{{ $row->total }}</td><td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="3" class="empty">Belum ada data.</td></tr>@endforelse</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Obat Hampir Kedaluwarsa</h2>
            <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Batch</th><th>Qty</th><th>Expired</th></tr></thead><tbody>@forelse($expiring as $row)<tr><td>{{ $row->name }}</td><td>{{ $row->batch_number }}</td><td>{{ $row->quantity }}</td><td>{{ $row->expiry_date }}</td></tr>@empty<tr><td colspan="4" class="empty">Tidak ada batch hampir kedaluwarsa.</td></tr>@endforelse</tbody></table></div>
        </div>
        <div class="panel">
            <h2 class="section-title">Stok Kritis</h2>
            <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Stok</th><th>Min</th></tr></thead><tbody>@forelse($criticalStock as $row)<tr><td>{{ $row->name }}</td><td><span class="stock-low">{{ $row->stock }}</span></td><td>{{ $row->min_stock }}</td></tr>@empty<tr><td colspan="3" class="empty">Tidak ada stok kritis.</td></tr>@endforelse</tbody></table></div>
        </div>
    </div>
</div>
@endsection
