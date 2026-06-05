@extends('layouts.app')

@section('title', $medicine->exists ? 'Edit Obat' : 'Tambah Obat')

@section('content')
<div class="space-y-4">
    <x-page-header
        :title="$medicine->exists ? 'Edit Obat' : 'Tambah Obat'"
        description="Lengkapi informasi obat yang akan muncul di katalog, stok, dan laporan."
    />

    <div class="panel">
        <form class="grid gap-4 lg:grid-cols-3" method="post" enctype="multipart/form-data" action="{{ $medicine->exists ? route('admin.medicines.update', $medicine) : route('admin.medicines.store') }}">
            @csrf
            @if($medicine->exists) @method('put') @endif
            <label class="field"><span>Nama</span><input name="name" value="{{ old('name', $medicine->name) }}" required><x-field-error name="name" /></label>
            <label class="field"><span>Kategori</span><select name="category_id" required><option value="">Pilih</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $medicine->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select><x-field-error name="category_id" /></label>
            <label class="field"><span>Supplier</span><select name="supplier_id"><option value="">Tanpa supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id', $medicine->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select><x-field-error name="supplier_id" /></label>
            <label class="field"><span>Type</span><select name="type" required>@foreach($types as $type)<option value="{{ $type }}" @selected(old('type', $medicine->type) === $type)>{{ str_replace('_', ' ', $type) }}</option>@endforeach</select><x-field-error name="type" /></label>
            <label class="field"><span>Harga</span><input name="price" type="number" step="0.01" min="0" value="{{ old('price', $medicine->price) }}" required><x-field-error name="price" /></label>
            <label class="field"><span>Min Stok</span><input name="min_stock" type="number" min="0" value="{{ old('min_stock', $medicine->min_stock ?? 10) }}" required><x-field-error name="min_stock" /></label>
            <label class="field lg:col-span-3"><span>Deskripsi</span><textarea name="description" rows="3">{{ old('description', $medicine->description) }}</textarea><x-field-error name="description" /></label>
            <label class="field"><span>Komposisi</span><textarea name="composition" rows="3">{{ old('composition', $medicine->composition) }}</textarea><x-field-error name="composition" /></label>
            <label class="field"><span>Dosis</span><textarea name="dosage" rows="3">{{ old('dosage', $medicine->dosage) }}</textarea><x-field-error name="dosage" /></label>
            <label class="field"><span>Efek Samping</span><textarea name="side_effects" rows="3">{{ old('side_effects', $medicine->side_effects) }}</textarea><x-field-error name="side_effects" /></label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="requires_prescription" value="1" @checked(old('requires_prescription', $medicine->requires_prescription))> Wajib resep dokter</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $medicine->exists ? $medicine->is_active : true))> Aktif</label>
            <label class="field">
                <span>Gambar Obat</span>
                <input id="image-input" name="image" type="file" accept="image/jpeg,image/png,image/webp">
                <x-field-error name="image" />
                <img id="image-preview" class="mt-3 h-28 w-28 rounded object-cover {{ $medicine->image_path ? '' : 'hidden' }}" src="{{ $medicine->image_path ? asset('storage/'.$medicine->image_path) : '' }}" alt="">
            </label>
            <div class="form-actions lg:col-span-3">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-muted" href="{{ route('admin.medicines.index') }}">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('image-input')?.addEventListener('change', event => {
    const file = event.target.files?.[0];
    const preview = document.getElementById('image-preview');
    if (!file || !preview) return;
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
});
</script>
@endpush
