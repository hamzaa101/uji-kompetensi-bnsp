@extends('layouts.app')

@section('title', 'Simulasi Alert')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Simulasi Alert"
        description="Gunakan tombol demo untuk memicu notifikasi, error log, dan audit log tanpa mengubah alur bisnis utama."
    />

    <div class="panel max-w-3xl">
        <div class="grid gap-3 md:grid-cols-3">
            <form method="post" action="{{ route('admin.simulations.low-stock') }}">@csrf <button class="btn btn-primary w-full" type="submit">Low Stock Alert</button></form>
            <form method="post" action="{{ route('admin.simulations.expired') }}">@csrf <button class="btn btn-primary w-full" type="submit">Expired Alert</button></form>
            <form method="post" action="{{ route('admin.simulations.error') }}">@csrf <button class="btn btn-danger w-full" type="submit">Application Error</button></form>
        </div>
    </div>
</div>
@endsection
