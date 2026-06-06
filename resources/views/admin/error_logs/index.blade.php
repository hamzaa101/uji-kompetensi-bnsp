@extends('layouts.app')

@section('title', 'Error Log')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Dashboard Error Log"
        description="Pantau severity error, tandai resolve, dan gunakan simulasi untuk kebutuhan demonstrasi."
    >
        <x-slot:actions>
        <form method="post" action="{{ route('admin.error-logs.simulate') }}">@csrf <button class="btn btn-danger" type="submit">Simulate Error</button></form>
        </x-slot:actions>
    </x-page-header>

    <div class="filter-panel">
        <form class="filter-actions">
            <select name="severity"><option value="">Semua severity</option>@foreach(['info','warning','critical'] as $severity)<option value="{{ $severity }}" @selected(request('severity')===$severity)>{{ $severity }}</option>@endforeach</select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table><thead><tr><th>Severity</th><th>Pesan</th><th>File</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead><tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><span class="status status-{{ $log->severity }}">{{ $log->severity }}</span></td>
                        <td class="text-cell">
                            {{ $log->message }}
                            @if($log->trace)
                                <details class="trace-details">
                                    <summary>Trace</summary>
                                    <pre class="trace-box">{{ $log->trace }}</pre>
                                </details>
                            @endif
                        </td>
                        <td class="text-cell">{{ $log->file ?: '-' }}{{ $log->line ? ':'.$log->line : '' }}</td>
                        <td><span class="status status-{{ $log->is_resolved ? 'success' : 'warning' }}">{{ $log->is_resolved ? 'resolved' : 'open' }}</span></td>
                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="actions">@unless($log->is_resolved)<form method="post" action="{{ route('admin.error-logs.resolve', $log) }}">@csrf <button class="btn btn-muted" type="submit">Resolve</button></form>@endunless</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada error log.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="table-footer">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
