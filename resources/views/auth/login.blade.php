@extends('layouts.app')

@section('title', 'Login')

@section('content')
<section class="mx-auto max-w-md">
    <div class="panel">
        <h1 class="text-2xl font-semibold">Login Sistem</h1>
        <p class="mt-1 text-sm text-slate-600">Masuk sebagai admin, apoteker, kasir, atau pasien.</p>
        <form class="mt-6 space-y-4" method="post" action="{{ route('login') }}">
            @csrf
            <label class="field">
                <span>Email</span>
                <input name="email" type="email" value="{{ old('email') }}" required autofocus>
            </label>
            <label class="field">
                <span>Password</span>
                <input name="password" type="password" required>
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1"> Ingat saya
            </label>
            <button class="btn btn-primary w-full" type="submit">Login</button>
        </form>
        <div class="mt-4 rounded bg-slate-100 p-3 text-xs text-slate-600">
            Demo: admin@klinik.test, apoteker@klinik.test, kasir@klinik.test, pasien@klinik.test. Password: password123.
        </div>
    </div>
</section>
@endsection
