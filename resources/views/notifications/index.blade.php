@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="panel">
    <h1 class="text-xl font-semibold">Notifikasi</h1>
    <div class="mt-4 divide-y divide-slate-100">
        @foreach($notifications as $notification)
            <div class="flex items-start justify-between gap-4 py-3">
                <div>
                    <span class="status status-{{ $notification->type }}">{{ $notification->type }}</span>
                    <p class="mt-1 font-medium">{{ $notification->title }}</p>
                    <p class="text-sm text-slate-600">{{ $notification->message }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @unless($notification->is_read)
                    <button class="btn btn-muted" data-notification-read="{{ route('notifications.read', $notification) }}">Tandai Dibaca</button>
                @endunless
            </div>
        @endforeach
    </div>
    {{ $notifications->links() }}
</div>
@endsection
