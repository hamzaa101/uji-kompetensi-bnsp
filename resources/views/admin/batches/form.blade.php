@extends('layouts.app')

@section('title', $batch->exists ? 'Edit Batch' : 'Tambah Batch')

@section('content')
<div class="space-y-4">
    <x-page-header
        :title="$batch->exists ? 'Edit Batch' : 'Tambah Batch'"
        description="Isi batch, jumlah stok, harga beli, dan tanggal kedaluwarsa untuk kontrol FIFO."
    />

    <div class="panel max-w-3xl">
        <form class="form-grid" method="post" action="{{ $batch->exists ? route('admin.medicine-batches.update', $batch) : route('admin.medicine-batches.store') }}">
            @csrf
            @if($batch->exists) @method('put') @endif
            <label class="field md:col-span-2"><span>Obat</span><select name="medicine_id" required><option value="">Pilih obat</option>@foreach($medicines as $medicine)<option value="{{ $medicine->id }}" @selected(old('medicine_id', $batch->medicine_id) == $medicine->id)>{{ $medicine->name }}</option>@endforeach</select><x-field-error name="medicine_id" /></label>
            <label class="field"><span>Batch Number</span><input name="batch_number" value="{{ old('batch_number', $batch->batch_number) }}" required><x-field-error name="batch_number" /></label>
            <label class="field"><span>Quantity</span><input name="quantity" type="number" min="0" value="{{ old('quantity', $batch->quantity) }}" required><x-field-error name="quantity" /></label>
            <label class="field"><span>Initial Quantity</span><input name="initial_quantity" type="number" min="0" value="{{ old('initial_quantity', $batch->initial_quantity) }}"><x-field-error name="initial_quantity" /></label>
            <label class="field"><span>Expiry Date</span><input name="expiry_date" type="date" value="{{ old('expiry_date', optional($batch->expiry_date)->toDateString()) }}" required><x-field-error name="expiry_date" /></label>
            <label class="field"><span>Purchase Price</span><input name="purchase_price" type="number" min="0" step="0.01" value="{{ old('purchase_price', $batch->purchase_price) }}"><x-field-error name="purchase_price" /></label>
            <label class="field"><span>Received At</span><input name="received_at" type="date" value="{{ old('received_at', optional($batch->received_at)->toDateString()) }}"><x-field-error name="received_at" /></label>
            <div class="form-actions md:col-span-2">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-muted" href="{{ route('admin.medicine-batches.index') }}">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
