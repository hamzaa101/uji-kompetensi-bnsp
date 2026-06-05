@extends('layouts.app')

@section('title', 'Katalog Obat')

@section('content')
<div class="space-y-4">
    <div class="panel">
        <h1 class="text-2xl font-semibold">Katalog Obat</h1>
        <form class="mt-4 grid gap-3 md:grid-cols-5">
            <input id="search-input" name="search" value="{{ request('search') }}" placeholder="Cari obat">
            <select name="category_id"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select>
            <select name="type"><option value="">Semua type</option>@foreach($types as $type)<option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', $type) }}</option>@endforeach</select>
            <select name="sort"><option value="">Nama A-Z</option><option value="price_asc" @selected(request('sort')==='price_asc')>Harga naik</option><option value="price_desc" @selected(request('sort')==='price_desc')>Harga turun</option></select>
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
        @if($suggestions->isNotEmpty())
            <p class="mt-3 text-sm text-slate-600">Mungkin yang Anda maksud: @foreach($suggestions as $suggestion)<a class="text-teal-700" href="{{ route('catalog.index', ['search' => $suggestion]) }}">{{ $suggestion }}</a>{{ $loop->last ? '' : ', ' }}@endforeach</p>
        @endif
        <div id="autocomplete-box" class="autocomplete hidden"></div>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($medicines as $medicine)
            <article class="product-card">
                <a href="{{ route('catalog.show', $medicine) }}">
                    @if($medicine->image_path)
                        <img class="product-image" src="{{ asset('storage/'.$medicine->image_path) }}" alt="{{ $medicine->name }}">
                    @else
                        <div class="product-image product-placeholder">KMJ</div>
                    @endif
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-semibold">{{ $medicine->name }}</h2>
                            <span class="status status-{{ $medicine->requires_prescription ? 'warning' : 'success' }}">{{ $medicine->requires_prescription ? 'Resep' : 'Bebas' }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600">{{ $medicine->category?->name }} | Stok {{ $medicine->stock_sum ?? 0 }}</p>
                        <p class="mt-3 font-semibold text-teal-700">Rp {{ number_format($medicine->price, 0, ',', '.') }}</p>
                    </div>
                </a>
            </article>
        @empty
            <p class="empty md:col-span-2 xl:col-span-3">Obat tidak ditemukan.</p>
        @endforelse
    </div>
    {{ $medicines->links() }}
</div>
@endsection

@push('scripts')
<script>
const input = document.getElementById('search-input');
const box = document.getElementById('autocomplete-box');
input?.addEventListener('input', async () => {
    if (input.value.length < 2) { box.classList.add('hidden'); return; }
    const response = await fetch("{{ route('catalog.autocomplete') }}?q=" + encodeURIComponent(input.value));
    const rows = await response.json();
    box.innerHTML = rows.map(row => `<a href="${row.url}">${row.label}</a>`).join('');
    box.classList.toggle('hidden', rows.length === 0);
});
</script>
@endpush
