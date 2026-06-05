@extends('layouts.app')

@section('title', $supplier->exists ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="space-y-4">
    <x-page-header
        :title="$supplier->exists ? 'Edit Supplier' : 'Tambah Supplier'"
        description="Lengkapi data kontak pemasok obat agar mudah dilacak saat stok diperbarui."
    />

    <div class="panel max-w-3xl">
        <form class="form-grid" method="post" action="{{ $supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}">
            @csrf
            @if($supplier->exists) @method('put') @endif
            <label class="field"><span>Nama</span><input name="name" value="{{ old('name', $supplier->name) }}" required><x-field-error name="name" /></label>
            <label class="field"><span>Contact Person</span><input name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"><x-field-error name="contact_person" /></label>
            <label class="field"><span>Telepon</span><input name="phone" value="{{ old('phone', $supplier->phone) }}"><x-field-error name="phone" /></label>
            <label class="field"><span>Email</span><input name="email" type="email" value="{{ old('email', $supplier->email) }}"><x-field-error name="email" /></label>
            <label class="field md:col-span-2"><span>Alamat</span><textarea name="address" rows="3">{{ old('address', $supplier->address) }}</textarea><x-field-error name="address" /></label>
            <div class="form-actions md:col-span-2">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-muted" href="{{ route('admin.suppliers.index') }}">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
