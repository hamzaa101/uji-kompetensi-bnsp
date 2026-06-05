@extends('layouts.app')

@section('title', $batch->exists ? 'Edit Batch' : 'Tambah Batch')

@section('content')
<div class="panel max-w-3xl">
    <h1 class="text-xl font-semibold">{{ $batch->exists ? 'Edit Batch' : 'Tambah Batch' }}</h1>
    <form class="mt-5 grid gap-4 md:grid-cols-2" method="post" action="{{ $batch->exists ? route('admin.medicine-batches.update', $batch) : route('admin.medicine-batches.store') }}">
        @csrf
        @if($batch->exists) @method('put') @endif
        <label class="field md:col-span-2"><span>Obat</span><select name="medicine_id" required><option value="">Pilih obat</option>@foreach($medicines as $medicine)<option value="{{ $medicine->id }}" @selected(old('medicine_id', $batch->medicine_id) == $medicine->id)>{{ $medicine->name }}</option>@endforeach</select></label>
        <label class="field"><span>Batch Number</span><input name="batch_number" value="{{ old('batch_number', $batch->batch_number) }}" required></label>
        <label class="field"><span>Quantity</span><input name="quantity" type="number" min="0" value="{{ old('quantity', $batch->quantity) }}" required></label>
        <label class="field"><span>Initial Quantity</span><input name="initial_quantity" type="number" min="0" value="{{ old('initial_quantity', $batch->initial_quantity) }}"></label>
        <label class="field"><span>Expiry Date</span><input name="expiry_date" type="date" value="{{ old('expiry_date', optional($batch->expiry_date)->toDateString()) }}" required></label>
        <label class="field"><span>Purchase Price</span><input name="purchase_price" type="number" min="0" step="0.01" value="{{ old('purchase_price', $batch->purchase_price) }}"></label>
        <label class="field"><span>Received At</span><input name="received_at" type="date" value="{{ old('received_at', optional($batch->received_at)->toDateString()) }}"></label>
        <div class="flex gap-2 md:col-span-2">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-muted" href="{{ route('admin.medicine-batches.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
