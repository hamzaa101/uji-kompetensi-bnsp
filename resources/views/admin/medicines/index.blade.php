@extends('layouts.app')

@section('title', 'Data Obat')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Data Obat"
        description="Kelola data obat, kategori, harga, stok batch, dan status aktif yang tampil di katalog."
    >
        <x-slot:actions>
            <a class="btn btn-primary" href="{{ route('admin.medicines.create') }}">Tambah Obat</a>
        </x-slot:actions>
    </x-page-header>

    <div class="filter-panel">
        <form class="filter-grid">
            <input name="search" placeholder="Cari obat" value="{{ request('search') }}">
            <select name="category_id"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select>
            <select name="type"><option value="">Semua type</option>@foreach($types as $type)<option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', $type) }}</option>@endforeach</select>
            <select name="sort"><option value="">Terbaru</option><option value="price_asc" @selected(request('sort')==='price_asc')>Harga naik</option><option value="price_desc" @selected(request('sort')==='price_desc')>Harga turun</option><option value="stock_asc" @selected(request('sort')==='stock_asc')>Stok naik</option><option value="stock_desc" @selected(request('sort')==='stock_desc')>Stok turun</option></select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Obat</th><th>Kategori</th><th>Type</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($medicines as $medicine)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $medicine->name }}</div>
                                <div class="text-xs text-slate-500">{{ $medicine->requires_prescription ? 'Wajib resep' : 'Tanpa resep' }}</div>
                            </td>
                            <td>{{ $medicine->category?->name }}</td>
                            <td>{{ str_replace('_', ' ', $medicine->type) }}</td>
                            <td>Rp {{ number_format($medicine->price, 0, ',', '.') }}</td>
                            <td><span class="{{ ($medicine->stock_sum ?? 0) <= $medicine->min_stock ? 'stock-low' : 'stock-ok' }}">{{ $medicine->stock_sum ?? 0 }}</span></td>
                            <td><span class="status status-{{ $medicine->is_active ? 'success' : 'warning' }}">{{ $medicine->is_active ? 'aktif' : 'nonaktif' }}</span></td>
                            <td class="actions">
                                <a class="btn btn-muted" href="{{ route('admin.medicines.edit', $medicine) }}">Edit</a>
                                <form method="post" action="{{ route('admin.medicines.destroy', $medicine) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Nonaktif</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">Data obat belum ada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">{{ $medicines->links() }}</div>
    </div>
</div>
@endsection
