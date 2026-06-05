<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use App\Services\StockService;

class SimulationController extends Controller
{
    public function index()
    {
        return view('admin.simulations.index');
    }

    public function lowStock(StockService $stock, NotificationService $notifications, AuditLogService $audit)
    {
        $count = $notifications->createLowStockAlerts($stock->criticalMedicines());
        $audit->record('simulate_low_stock_alert', null, "{$count} alert dibuat.");

        return back()->with('success', "{$count} alert stok kritis dibuat.");
    }

    public function expired(StockService $stock, NotificationService $notifications, AuditLogService $audit)
    {
        $count = $notifications->createExpiredAlerts($stock->expiringBatches(90));
        $audit->record('simulate_expired_alert', null, "{$count} alert dibuat.");

        return back()->with('success', "{$count} alert expired dibuat.");
    }

    public function error(NotificationService $notifications, AuditLogService $audit)
    {
        $error = ErrorLog::create(['severity' => 'critical', 'message' => 'Simulasi application error dari menu simulasi.']);
        $notifications->createErrorAlert($error);
        $audit->record('simulate_application_error', $error);

        return back()->with('success', 'Simulasi error dibuat.');
    }
}
