<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceMetric extends Model
{
    protected $fillable = [
        'memory_usage', 'disk_usage', 'queue_pending', 'request_count',
        'error_count', 'avg_response_time',
    ];
}
