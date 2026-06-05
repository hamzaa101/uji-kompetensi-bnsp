@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="panel">
    <h1 class="text-xl font-semibold">Audit Log</h1>
    <form class="mt-4 grid gap-3 md:grid-cols-4">
        <select name="user_id"><option value="">Semua user</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }} ({{ $user->role }})</option>@endforeach</select>
        <input name="action" value="{{ request('action') }}" placeholder="Action">
        <input type="date" name="date" value="{{ request('date') }}">
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
    <div class="table-wrap mt-4">
        <table><thead><tr><th>Waktu</th><th>User</th><th>Action</th><th>Tabel</th><th>IP</th><th>User Agent</th><th>Deskripsi</th></tr></thead><tbody>
            @foreach($logs as $log)
                <tr><td>{{ $log->created_at->format('d M Y H:i') }}</td><td>{{ $log->user?->name ?? 'system' }}</td><td>{{ $log->action }}</td><td>{{ $log->table_name }} #{{ $log->record_id }}</td><td>{{ $log->ip_address }}</td><td class="max-w-xs truncate">{{ $log->user_agent }}</td><td>{{ $log->description }}</td></tr>
            @endforeach
        </tbody></table>
    </div>
    {{ $logs->links() }}
</div>
@endsection
