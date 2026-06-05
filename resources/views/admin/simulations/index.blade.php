@extends('layouts.app')

@section('title', 'Simulasi Alert')

@section('content')
<div class="panel max-w-3xl">
    <h1 class="text-xl font-semibold">Simulasi Alert</h1>
    <p class="mt-1 text-sm text-slate-600">Gunakan tombol ini untuk demonstrasi notifikasi, error log, dan audit log.</p>
    <div class="mt-5 grid gap-3 md:grid-cols-3">
        <form method="post" action="{{ route('admin.simulations.low-stock') }}">@csrf <button class="btn btn-primary w-full" type="submit">Low Stock Alert</button></form>
        <form method="post" action="{{ route('admin.simulations.expired') }}">@csrf <button class="btn btn-primary w-full" type="submit">Expired Alert</button></form>
        <form method="post" action="{{ route('admin.simulations.error') }}">@csrf <button class="btn btn-danger w-full" type="submit">Application Error</button></form>
    </div>
</div>
@endsection
