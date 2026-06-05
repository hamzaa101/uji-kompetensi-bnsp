<?php

namespace App\Services;

use App\Models\ErrorLog;
use App\Models\ResourceMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResourceMonitoringService
{
    public function snapshot(): ResourceMetric
    {
        return ResourceMetric::create($this->latest());
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
            'avg_response_time' => 0,
        ];
    }
}
