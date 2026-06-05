@extends('layouts.app')

@section('title', 'Monitoring Resource')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Monitoring Resource"
        description="Pantau metrik prototype seperti memory, disk, queue, request, error, dan response time."
    >
        <x-slot:actions>
        <button class="btn btn-primary" type="button" onclick="location.reload()">Refresh</button>
        </x-slot:actions>
    </x-page-header>

    <div class="panel">
        <div id="metrics" class="grid gap-4 md:grid-cols-3">
            <div class="stat"><span>Memory</span><strong data-key="memory_usage">{{ number_format($metric->memory_usage / 1024 / 1024, 2) }} MB</strong></div>
            <div class="stat"><span>Disk Used</span><strong data-key="disk_usage">{{ number_format($metric->disk_usage / 1024 / 1024 / 1024, 2) }} GB</strong></div>
            <div class="stat"><span>Queue Pending</span><strong data-key="queue_pending">{{ $metric->queue_pending }}</strong></div>
            <div class="stat"><span>Request Count</span><strong data-key="request_count">{{ $metric->request_count }}</strong></div>
            <div class="stat"><span>Error Count</span><strong data-key="error_count">{{ $metric->error_count }}</strong></div>
            <div class="stat"><span>Avg Response</span><strong data-key="avg_response_time">{{ $metric->avg_response_time }} ms</strong></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    window.KMJApp = window.KMJApp || {};

    if (window.KMJApp.monitoringInterval) {
        clearInterval(window.KMJApp.monitoringInterval);
    }

    const updateMetrics = async () => {
        if (document.hidden || window.KMJApp.monitoringRequestInFlight) {
            return;
        }

        window.KMJApp.monitoringRequestInFlight = true;

        try {
            const response = await fetch("{{ route('admin.monitoring.latest') }}", {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                return;
            }

            const metric = await response.json();
            document.querySelector('[data-key="memory_usage"]').textContent = (metric.memory_usage / 1024 / 1024).toFixed(2) + ' MB';
            document.querySelector('[data-key="disk_usage"]').textContent = (metric.disk_usage / 1024 / 1024 / 1024).toFixed(2) + ' GB';
            document.querySelector('[data-key="queue_pending"]').textContent = metric.queue_pending;
            document.querySelector('[data-key="request_count"]').textContent = metric.request_count;
            document.querySelector('[data-key="error_count"]').textContent = metric.error_count;
            document.querySelector('[data-key="avg_response_time"]').textContent = metric.avg_response_time + ' ms';
        } finally {
            window.KMJApp.monitoringRequestInFlight = false;
        }
    };

    window.KMJApp.monitoringInterval = window.setInterval(updateMetrics, 5000);
});
</script>
@endpush
