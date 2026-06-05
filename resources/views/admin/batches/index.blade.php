@extends('layouts.app')

@section('title', 'Batch Stok')

@section('content')
<div class="panel">
    <div class="toolbar">
        <div><h1 class="text-xl font-semibold">Batch Stok Obat</h1><p class="text-sm text-slate-600">Urut tanggal kedaluwarsa untuk demonstrasi FIFO.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.medicine-batches.create') }}">Tambah Batch</a>
    </div>
    <form class="mt-4 flex gap-2">
        <select name="medicine_id"><option value="">Semua obat</option>@foreach($medicines as $medicine)<option value="{{ $medicine->id }}" @selected(request('medicine_id') == $medicine->id)>{{ $medicine->name }}</option>@endforeach</select>
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
    <div class="table-wrap mt-4">
        <table>
            <thead><tr><th>Obat</th><th>Batch</th><th>Qty</th><th>Initial</th><th>Expired</th><th>Harga Beli</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($batches as $batch)
                    <tr>
                        <td>{{ $batch->medicine->name }}</td>
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
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $batches->links() }}
</div>
@endsection
