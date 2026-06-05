@extends('layouts.app')

@section('title', 'Batch Stok')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Batch Stok Obat"
        description="Pantau stok per batch dan tanggal kedaluwarsa untuk demonstrasi FIFO."
    >
        <x-slot:actions>
        <a class="btn btn-primary" href="{{ route('admin.medicine-batches.create') }}">Tambah Batch</a>
        </x-slot:actions>
    </x-page-header>

    <div class="filter-panel">
        <form class="filter-actions">
            <select name="medicine_id" class="min-w-64"><option value="">Semua obat</option>@foreach($medicines as $medicine)<option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>{{ $medicine->name }}</option>@endforeach</select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Obat</th><th>Batch</th><th>Qty</th><th>Initial</th><th>Expired</th><th>Harga Beli</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr>
                            <td class="font-medium">{{ $batch->medicine->name }}</td>
                            <td>{{ $batch->batch_number }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>{{ $batch->initial_quantity }}</td>
                            <td>{{ $batch->expiry_date->format('d M Y') }}</td>
                            <td>{{ $batch->purchase_price ? 'Rp '.number_format($batch->purchase_price, 0, ',', '.') : '-' }}</td>
                            <td class="actions">
                                <a class="btn btn-muted" href="{{ route('admin.medicine-batches.edit', $batch) }}">Edit</a>
                                <form method="post" action="{{ route('admin.medicine-batches.destroy', $batch) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">Batch stok belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">{{ $batches->links() }}</div>
    </div>
</div>
@endsection
