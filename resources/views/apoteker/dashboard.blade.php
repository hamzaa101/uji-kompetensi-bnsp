@extends('layouts.app')

@section('title', 'Dashboard Apoteker')

@section('content')
<div class="space-y-4">
    <h1 class="text-2xl font-semibold">Dashboard Apoteker</h1>
    <div class="grid gap-4 md:grid-cols-3">
        <a class="stat" href="{{ route('apoteker.prescriptions') }}"><span>Resep Pending</span><strong>{{ $pending }}</strong></a>
        <a class="stat" href="{{ route('apoteker.stock-alerts') }}"><span>Stok Kritis</span><strong>{{ $critical }}</strong></a>
        <a class="stat" href="{{ route('apoteker.stock-alerts') }}"><span>Hampir Expired</span><strong>{{ $expiring }}</strong></a>
    </div>
</div>
@endsection
