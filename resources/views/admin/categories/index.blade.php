@extends('layouts.app')

@section('title', 'Kategori Obat')

@section('content')
<div class="panel">
    <div class="toolbar">
        <div>
            <h1 class="text-xl font-semibold">Kategori Obat</h1>
            <p class="text-sm text-slate-600">Kelola kategori untuk filter katalog dan laporan.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">Tambah</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Slug</th><th>Obat</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->medicines_count }}</td>
                        <td class="actions">
                            <a class="btn btn-muted" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                            <form method="post" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $categories->links() }}
</div>
@endsection
