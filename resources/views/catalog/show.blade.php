@extends('layouts.app')

@section('title', $medicine->name)

@section('content')
<div class="grid gap-6 lg:grid-cols-[420px_1fr]">
    <div>
        @if($medicine->image_path)
            <img class="aspect-square w-full rounded-lg object-cover bg-white" src="{{ asset('storage/'.$medicine->image_path) }}" alt="{{ $medicine->name }}">
        @else
            <div class="product-image product-placeholder aspect-square text-3xl">KMJ</div>
        @endif
    </div>
    <div class="panel">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div><h1 class="text-2xl font-semibold">{{ $medicine->name }}</h1><p class="text-sm text-slate-600">{{ $medicine->category?->name }} | {{ str_replace('_', ' ', $medicine->type) }}</p></div>
            <span class="status status-{{ $medicine->requires_prescription ? 'warning' : 'success' }}">{{ $medicine->requires_prescription ? 'Wajib resep' : 'Tanpa resep' }}</span>
        </div>
        <p class="mt-4 text-2xl font-semibold text-teal-700">Rp {{ number_format($medicine->price, 0, ',', '.') }}</p>
        <dl class="mt-5 grid gap-4 md:grid-cols-2">
            <div><dt>Stok tersedia</dt><dd class="font-semibold">{{ $medicine->total_stock }} unit</dd></div>
            <div><dt>Supplier</dt><dd>{{ $medicine->supplier?->name ?? '-' }}</dd></div>
            <div class="md:col-span-2"><dt>Deskripsi</dt><dd>{{ $medicine->description }}</dd></div>
            <div><dt>Komposisi</dt><dd>{{ $medicine->composition }}</dd></div>
            <div><dt>Dosis</dt><dd>{{ $medicine->dosage }}</dd></div>
            <div class="md:col-span-2"><dt>Efek Samping</dt><dd>{{ $medicine->side_effects }}</dd></div>
        </dl>
        @auth
            @if(auth()->user()->role === 'pasien')
                <form class="mt-6 flex gap-2" method="post" action="{{ route('cart.add', $medicine) }}">
                    @csrf
                    <input class="w-24" name="quantity" type="number" min="1" value="1">
                    <button class="btn btn-primary" type="submit">Tambah ke Cart</button>
                </form>
            @endif
        @else
            <a class="btn btn-primary mt-6" href="{{ route('login') }}">Login untuk Membeli</a>
        @endauth
    </div>
</div>
@endsection
