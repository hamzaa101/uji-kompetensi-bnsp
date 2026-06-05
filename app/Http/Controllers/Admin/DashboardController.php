<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Order;
use App\Services\ReportService;
use App\Services\ResourceMonitoringService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ReportService $reports, ResourceMonitoringService $monitoring)
    {
        $from = now()->subDays(13)->toDateString();
        $to = now()->toDateString();

        return view('admin.dashboard', [
            'stats' => $reports->dashboardStats(),
            'dailySales' => $reports->salesPerDay($from, $to),
            'statusRecap' => Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'notifications' => AppNotification::latest()->limit(6)->get(),
            'monitoring' => $monitoring->latest(),
        ]);
    }
}
