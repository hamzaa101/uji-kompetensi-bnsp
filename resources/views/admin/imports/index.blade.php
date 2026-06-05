@extends('layouts.app')

@section('title', 'Import CSV Obat')

@section('content')
<div class="space-y-4">
    <div class="panel">
        <h1 class="text-xl font-semibold">Import CSV Obat</h1>
        <p class="text-sm text-slate-600">Upload CSV lalu proses melalui database queue. Contoh file ada di storage/app/examples/sample_medicines.csv.</p>
        <form class="mt-4 flex flex-wrap gap-3" method="post" enctype="multipart/form-data" action="{{ route('admin.imports.store') }}">
            @csrf
            <input type="file" name="csv" accept=".csv,text/csv" required>
            <button class="btn btn-primary" type="submit">Upload & Queue</button>
        </form>
    </div>
    <div class="panel">
        <h2 class="section-title">Status Import</h2>
        <div class="table-wrap"><table><thead><tr><th>File</th><th>Status</th><th>Progress</th><th>Error</th><th>Dibuat</th></tr></thead><tbody>
            @foreach($imports as $import)
                <tr><td>{{ $import->original_name }}</td><td><span class="status status-{{ $import->status === 'failed' ? 'critical' : ($import->status === 'completed' ? 'success' : 'info') }}">{{ $import->status }}</span></td><td>{{ $import->processed_rows }}/{{ $import->total_rows }}</td><td>{{ $import->error_message }}</td><td>{{ $import->created_at->format('d M Y H:i') }}</td></tr>
            @endforeach
        </tbody></table></div>
        {{ $imports->links() }}
    </div>
</div>
@endsection
