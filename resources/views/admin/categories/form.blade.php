@extends('layouts.app')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="space-y-4">
    <x-page-header
        :title="$category->exists ? 'Edit Kategori' : 'Tambah Kategori'"
        description="Lengkapi nama dan deskripsi kategori obat yang akan dipakai di katalog."
    />

    <div class="panel max-w-2xl">
        <form class="space-y-4" method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
            @csrf
            @if($category->exists) @method('put') @endif
            <label class="field">
                <span>Nama</span>
                <input name="name" value="{{ old('name', $category->name) }}" required>
                <x-field-error name="name" />
            </label>
            <label class="field">
                <span>Deskripsi</span>
                <textarea name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                <x-field-error name="description" />
            </label>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-muted" href="{{ route('admin.categories.index') }}">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
