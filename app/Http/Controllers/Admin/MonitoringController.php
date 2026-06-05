<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ResourceMonitoringService;

class MonitoringController extends Controller
{
    public function index(ResourceMonitoringService $monitoring)
    {
        return view('admin.monitoring.index', ['metric' => $monitoring->snapshot()]);
    }

    public function latest(ResourceMonitoringService $monitoring)
    {
        return response()->json($monitoring->latest());
    }
}
