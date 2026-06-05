<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('notifications.index', ['notifications' => $this->baseQuery($request)->latest()->paginate(20)]);
    }

    public function latest(Request $request)
    {
        return response()->json($this->baseQuery($request)->latest()->limit(8)->get());
    }

    public function unreadCount(Request $request)
    {
        return response()->json(['count' => $this->baseQuery($request)->where('is_read', false)->count()]);
    }

    public function read(Request $request, AppNotification $notification)
    {
        $allowed = $notification->user_id === $request->user()->id || $notification->role_target === $request->user()->role;
        abort_unless($allowed, 403);
        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    private function baseQuery(Request $request)
    {
        return AppNotification::query()
            ->where(function ($query) use ($request) {
                $query->where('user_id', $request->user()->id)
                    ->orWhere('role_target', $request->user()->role);
            });
    }
}
