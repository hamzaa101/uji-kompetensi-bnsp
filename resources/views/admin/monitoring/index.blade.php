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
        <div
            id="metrics"
            class="grid gap-4 md:grid-cols-3"
            data-monitoring-endpoint="{{ route('admin.monitoring.latest') }}"
            data-poll-interval="10000"
        >
            <div class="stat"><span>Memory</span><strong data-key="memory_usage">{{ number_format($metric->memory_usage / 1024 / 1024, 2) }} MB</strong></div>
            <div class="stat"><span>Disk Used</span><strong data-key="disk_usage">{{ number_format($metric->disk_usage / 1024 / 1024 / 1024, 2) }} GB</strong></div>
            <div class="stat"><span>Queue Pending</span><strong data-key="queue_pending">{{ $metric->queue_pending }}</strong></div>
            <div class="stat"><span>Request Count</span><strong data-key="request_count">{{ $metric->request_count }}</strong></div>
            <div class="stat"><span>Error Count</span><strong data-key="error_count">{{ $metric->error_count }}</strong></div>
            <div class="stat"><span>Critical Notification</span><strong data-key="critical_notification_count">{{ $metric->critical_notification_count ?? 0 }}</strong></div>
            <div class="stat"><span>Avg Response</span><strong data-key="avg_response_time">{{ $metric->avg_response_time }} ms</strong></div>
        </div>
    </div>
</div>
@endsection
