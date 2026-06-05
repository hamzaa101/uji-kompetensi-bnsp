@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Audit Log"
        description="Catatan aktivitas user dan sistem untuk membantu penelusuran saat demo."
    />

    <div class="filter-panel">
    <form class="filter-grid">
        <select name="user_id"><option value="">Semua user</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }} ({{ $user->role }})</option>@endforeach</select>
        <input name="action" value="{{ request('action') }}" placeholder="Action">
        <input type="date" name="date" value="{{ request('date') }}">
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table><thead><tr><th>Waktu</th><th>User</th><th>Action</th><th>Tabel</th><th>IP</th><th>User Agent</th><th>Deskripsi</th></tr></thead><tbody>
                @forelse($logs as $log)
                    <tr><td>{{ $log->created_at->format('d M Y H:i') }}</td><td>{{ $log->user?->name ?? 'system' }}</td><td><span class="status status-info">{{ $log->action }}</span></td><td>{{ $log->table_name }} #{{ $log->record_id }}</td><td>{{ $log->ip_address }}</td><td class="max-w-xs truncate">{{ $log->user_agent }}</td><td>{{ $log->description }}</td></tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada audit log.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="table-footer">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
