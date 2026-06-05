@extends('layouts.app')

@section('title', 'Kategori Obat')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Kategori Obat"
        description="Kelola kategori untuk filter katalog, pengelompokan master obat, dan laporan."
    >
        <x-slot:actions>
        <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">Tambah</a>
        </x-slot:actions>
    </x-page-header>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>Slug</th><th>Obat</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="font-medium">{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->medicines_count }}</td>
                            <td class="actions">
                                <a class="btn btn-muted" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                <form method="post" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">Kategori belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
