@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <x-page-header
        title="Dashboard Admin"
        description="Ringkasan penjualan, stok, notifikasi, dan resource aplikasi untuk demo operasional klinik."
    />

    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
        <div class="stat"><span>Penjualan Hari Ini</span><strong>Rp {{ number_format($stats['today_sales'], 0, ',', '.') }}</strong></div>
        <div class="stat"><span>Total Order</span><strong>{{ $stats['order_count'] }}</strong></div>
        <div class="stat"><span>Obat Aktif</span><strong>{{ $stats['active_medicines'] }}</strong></div>
        <div class="stat"><span>Stok Kritis</span><strong>{{ $stats['critical_stock'] }}</strong></div>
        <div class="stat"><span>Hampir Expired</span><strong>{{ $stats['expiring'] }}</strong></div>
        <div class="stat"><span>Omzet Bulan Ini</span><strong>Rp {{ number_format($stats['month_revenue'], 0, ',', '.') }}</strong></div>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="panel">
            <h2 class="section-title">Grafik Penjualan Harian</h2>
            <canvas id="salesChart" class="chart-box"></canvas>
        </div>
        <div class="panel">
            <h2 class="section-title">Order Berdasarkan Status</h2>
            <canvas id="statusChart" class="chart-box"></canvas>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="panel">
            <h2 class="section-title">Notifikasi Terbaru</h2>
            <div class="divide-y divide-slate-100">
                @forelse($notifications as $notification)
                    <div class="py-3">
                        <span class="status status-{{ $notification->type }}">{{ $notification->type }}</span>
                        <p class="mt-1 font-medium">{{ $notification->title }}</p>
                        <p class="text-sm text-slate-600">{{ $notification->message }}</p>
                    </div>
                @empty
                    <p class="empty">Belum ada notifikasi.</p>
                @endforelse
            </div>
        </div>
        <div class="panel">
            <h2 class="section-title">Monitoring Resource</h2>
            <dl class="mt-4 grid gap-3 text-sm md:grid-cols-2">
                <div class="compact-card"><dt>Memory</dt><dd class="font-semibold">{{ number_format($monitoring['memory_usage'] / 1024 / 1024, 2) }} MB</dd></div>
                <div class="compact-card"><dt>Disk Used</dt><dd class="font-semibold">{{ number_format($monitoring['disk_usage'] / 1024 / 1024 / 1024, 2) }} GB</dd></div>
                <div class="compact-card"><dt>Queue Pending</dt><dd class="font-semibold">{{ $monitoring['queue_pending'] }}</dd></div>
                <div class="compact-card"><dt>Error Aktif</dt><dd class="font-semibold">{{ $monitoring['error_count'] }}</dd></div>
            </dl>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Chart) return;
    const daily = @json($dailySales);
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: { labels: daily.map(row => row.period), datasets: [{ label: 'Omzet', data: daily.map(row => row.revenue), borderColor: '#0f766e', backgroundColor: 'rgba(15,118,110,.12)', tension: .25, fill: true }] },
        options: { maintainAspectRatio: false }
    });
    const statuses = @json($statusRecap);
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: { labels: Object.keys(statuses), datasets: [{ label: 'Order', data: Object.values(statuses), backgroundColor: '#2563eb' }] },
        options: { maintainAspectRatio: false }
    });
});
</script>
@endpush
