@extends('layouts.app')

@section('title', 'Dashboard Apoteker')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Dashboard Apoteker"
        description="Pantau resep pending, stok kritis, dan batch obat yang mendekati kedaluwarsa."
    />
    <div class="grid gap-4 md:grid-cols-3">
        <a class="stat" href="{{ route('apoteker.prescriptions') }}"><span>Resep Pending</span><strong>{{ $pending }}</strong></a>
        <a class="stat" href="{{ route('apoteker.stock-alerts') }}"><span>Stok Kritis</span><strong>{{ $critical }}</strong></a>
        <a class="stat" href="{{ route('apoteker.stock-alerts') }}"><span>Hampir Expired</span><strong>{{ $expiring }}</strong></a>
    </div>
</div>
@endsection
