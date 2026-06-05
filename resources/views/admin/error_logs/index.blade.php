@extends('layouts.app')

@section('title', 'Error Log')

@section('content')
<div class="panel">
    <div class="toolbar">
        <div><h1 class="text-xl font-semibold">Dashboard Error Log</h1><p class="text-sm text-slate-600">Filter severity, resolve, dan simulasi error critical.</p></div>
        <form method="post" action="{{ route('admin.error-logs.simulate') }}">@csrf <button class="btn btn-danger" type="submit">Simulate Error</button></form>
    </div>
    <form class="mt-4 flex gap-2">
        <select name="severity"><option value="">Semua severity</option>@foreach(['info','warning','critical'] as $severity)<option value="{{ $severity }}" @selected(request('severity')===$severity)>{{ $severity }}</option>@endforeach</select>
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
    <div class="table-wrap mt-4">
        <table><thead><tr><th>Severity</th><th>Pesan</th><th>File</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
            @foreach($logs as $log)
                <tr>
                    <td><span class="status status-{{ $log->severity }}">{{ $log->severity }}</span></td>
                    <td>{{ $log->message }}</td>
                    <td>{{ $log->file }}{{ $log->line ? ':'.$log->line : '' }}</td>
                    <td>{{ $log->is_resolved ? 'resolved' : 'open' }}</td>
                    <td>@unless($log->is_resolved)<form method="post" action="{{ route('admin.error-logs.resolve', $log) }}">@csrf <button class="btn btn-muted" type="submit">Resolve</button></form>@endunless</td>
                </tr>
            @endforeach
        </tbody></table>
    </div>
    {{ $logs->links() }}
</div>
@endsection
