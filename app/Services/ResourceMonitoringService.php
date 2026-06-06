<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\ErrorLog;
use App\Models\ResourceMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResourceMonitoringService
{
    public function snapshot(): ResourceMetric
    {
        $latest = $this->latest();
        $metric = ResourceMetric::create([
            'memory_usage' => $latest['memory_usage'],
            'disk_usage' => $latest['disk_usage'],
            'queue_pending' => $latest['queue_pending'],
            'request_count' => $latest['request_count'],
            'error_count' => $latest['error_count'],
            'avg_response_time' => $latest['avg_response_time'],
        ]);

        return $metric->forceFill([
            'critical_notification_count' => $latest['critical_notification_count'],
        ]);
    }

    public function latest(): array
    {
        $storage = storage_path();
        $total = @disk_total_space($storage) ?: 0;
        $free = @disk_free_space($storage) ?: 0;

        return [
            'memory_usage' => memory_get_usage(true),
            'disk_usage' => $total > 0 ? $total - $free : 0,
            'queue_pending' => Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0,
            'request_count' => Schema::hasTable('sessions') ? DB::table('sessions')->count() : 0,
            'error_count' => ErrorLog::where('is_resolved', false)->count(),
            'critical_notification_count' => Schema::hasTable('notifications')
                ? AppNotification::where('type', 'critical')->where('is_read', false)->count()
                : 0,
            'avg_response_time' => 0,
        ];
    }
}
