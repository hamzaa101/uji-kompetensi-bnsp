@extends('layouts.app')

@section('title', 'Import CSV Obat')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Import CSV Obat"
        description="Upload data obat dari CSV lalu proses melalui database queue untuk kebutuhan demo import."
    />

    <div class="panel">
        <p class="section-description">Contoh file tersedia di <span class="font-medium">storage/app/examples/sample_medicines.csv</span>.</p>
        <form class="mt-4 form-actions" method="post" enctype="multipart/form-data" action="{{ route('admin.imports.store') }}">
            @csrf
            <input type="file" name="csv" accept=".csv,text/csv" required>
            <button class="btn btn-primary" type="submit">Upload & Queue</button>
        </form>
        <x-field-error name="csv" />
    </div>
    <div class="panel">
        <h2 class="section-title">Status Import</h2>
        <div class="table-wrap mt-4"><table><thead><tr><th>File</th><th>Status</th><th>Progress</th><th>Error</th><th>Dibuat</th></tr></thead><tbody>
            @forelse($imports as $import)
                <tr><td>{{ $import->original_name }}</td><td><span class="status status-{{ $import->status === 'failed' ? 'critical' : ($import->status === 'completed' ? 'success' : 'info') }}">{{ $import->status }}</span></td><td>{{ $import->processed_rows }}/{{ $import->total_rows }}</td><td>{{ $import->error_message }}</td><td>{{ $import->created_at->format('d M Y H:i') }}</td></tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada import.</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="table-footer">{{ $imports->links() }}</div>
    </div>
</div>
@endsection
