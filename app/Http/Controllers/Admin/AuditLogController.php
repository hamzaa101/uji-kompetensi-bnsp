<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:120'],
            'date' => ['nullable', 'date'],
        ]);

        $query = AuditLog::with('user')->latest();
        $query->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id));
        $query->when($request->action, fn ($q, $action) => $q->where('action', 'like', "%{$action}%"));
        $query->when($request->date, fn ($q, $date) => $q->whereDate('created_at', $date));

        return view('admin.audit_logs.index', [
            'logs' => $query->paginate(20)->withQueryString(),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
