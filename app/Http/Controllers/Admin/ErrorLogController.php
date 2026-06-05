<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ErrorLog::latest();
        $query->when($request->severity, fn ($q, $severity) => $q->where('severity', $severity));

        return view('admin.error_logs.index', ['logs' => $query->paginate(15)->withQueryString()]);
    }

    public function resolve(ErrorLog $errorLog, AuditLogService $audit)
    {
        $errorLog->update(['is_resolved' => true, 'resolved_at' => now()]);
        $audit->record('resolve_error', $errorLog);

        return back()->with('success', 'Error ditandai resolved.');
    }

    public function simulate(NotificationService $notifications, AuditLogService $audit)
    {
        $error = ErrorLog::create([
            'severity' => 'critical',
            'message' => 'Simulasi error critical dari dashboard admin.',
            'file' => 'app/Http/Controllers/Admin/ErrorLogController.php',
            'line' => 42,
        ]);
        $notifications->createErrorAlert($error);
        $audit->record('simulate_error', $error);

        return back()->with('success', 'Simulasi error critical dibuat.');
    }
}
