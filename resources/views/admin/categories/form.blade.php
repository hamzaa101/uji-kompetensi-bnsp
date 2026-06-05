@extends('layouts.app')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="panel max-w-2xl">
    <h1 class="text-xl font-semibold">{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h1>
    <form class="mt-5 space-y-4" method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if($category->exists) @method('put') @endif
        <label class="field"><span>Nama</span><input name="name" value="{{ old('name', $category->name) }}" required></label>
        <label class="field"><span>Deskripsi</span><textarea name="description" rows="4">{{ old('description', $category->description) }}</textarea></label>
        <div class="flex gap-2">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-muted" href="{{ route('admin.categories.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
