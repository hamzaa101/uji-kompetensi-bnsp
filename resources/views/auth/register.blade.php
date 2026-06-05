@extends('layouts.app')

@section('title', 'Register Pasien')

@section('content')
<section class="mx-auto max-w-xl">
    <div class="panel">
        <h1 class="text-2xl font-semibold">Register Pasien</h1>
        <form class="mt-6 grid gap-4" method="post" action="{{ route('register') }}">
            @csrf
            <label class="field"><span>Nama</span><input name="name" value="{{ old('name') }}" required></label>
            <label class="field"><span>Email</span><input name="email" type="email" value="{{ old('email') }}" required></label>
            <label class="field"><span>Telepon</span><input name="phone" value="{{ old('phone') }}"></label>
            <label class="field"><span>Alamat</span><textarea name="address" rows="3">{{ old('address') }}</textarea></label>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="field"><span>Password</span><input name="password" type="password" required></label>
                <label class="field"><span>Konfirmasi Password</span><input name="password_confirmation" type="password" required></label>
            </div>
            <button class="btn btn-primary" type="submit">Buat Akun</button>
        </form>
    </div>
</section>
@endsection
