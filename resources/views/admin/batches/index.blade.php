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
            <select name="status"><option value="">Semua status</option>@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
            <button class="btn btn-primary" type="submit">Filter</button>
            @if(request()->hasAny(['medicine_id', 'status']))
                <a class="btn btn-muted" href="{{ route('admin.medicine-batches.index') }}">Reset</a>
            @endif
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Obat</th><th>Batch</th><th>Qty</th><th>Initial</th><th>Expired</th><th>Status</th><th>Harga Beli</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($batches as $batch)
                        @php
                            $today = now()->toDateString();
                            $isExpired = $batch->expiry_date->toDateString() < $today;
                            $isExpiring = ! $isExpired && $batch->expiry_date->lte(now()->addDays(30));
                            $isCritical = $batch->quantity <= $batch->medicine->min_stock;
                        @endphp
                        <tr>
                            <td class="font-medium">{{ $batch->medicine->name }}</td>
                            <td>{{ $batch->batch_number }}</td>
                            <td><span class="{{ $isCritical ? 'stock-low' : 'stock-ok' }}">{{ $batch->quantity }}</span></td>
                            <td>{{ $batch->initial_quantity }}</td>
                            <td>{{ $batch->expiry_date->format('d M Y') }}</td>
                            <td>
                                <div class="status-stack">
                                    @if($isExpired)
                                        <span class="status status-critical">expired</span>
                                    @elseif($isExpiring)
                                        <span class="status status-warning">hampir expired</span>
                                    @endif
                                    @if($isCritical)
                                        <span class="status status-critical">stok kritis</span>
                                    @else
                                        <span class="status status-success">stok aman</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $batch->purchase_price ? 'Rp '.number_format($batch->purchase_price, 0, ',', '.') : '-' }}</td>
                            <td class="actions">
                                <a class="btn btn-muted" href="{{ route('admin.medicine-batches.edit', $batch) }}">Edit</a>
                                <form method="post" action="{{ route('admin.medicine-batches.destroy', $batch) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty">Batch stok belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">{{ $batches->links() }}</div>
    </div>
</div>
@endsection
