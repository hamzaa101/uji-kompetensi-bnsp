@extends('layouts.app')

@section('title', 'Detail Resep')

@section('content')
<div class="grid gap-4 xl:grid-cols-[1fr_360px]">
    <div class="panel">
        <h1 class="text-xl font-semibold">Order {{ $order->order_number }}</h1>
        <p class="text-sm text-slate-600">Pasien: {{ $order->user?->name }} | Status: {{ $order->status }}</p>
        <div class="table-wrap mt-4">
            <table><thead><tr><th>Obat</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>
                @foreach($order->items as $item)
                    <tr><td>{{ $item->medicine->name }}</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->price, 0, ',', '.') }}</td><td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>
                @endforeach
            </tbody></table>
        </div>
        <p class="mt-4 text-right font-semibold">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
    </div>
    <div class="panel">
        <h2 class="section-title">Preview Resep</h2>
        <img class="h-80 w-full rounded object-contain bg-slate-100" src="{{ asset('storage/'.$order->prescription->image_path) }}" alt="Resep dokter">
        @if($order->prescription->status === 'pending')
            <form class="mt-4 space-y-3" method="post" action="{{ route('apoteker.prescriptions.approve', $order) }}">
                @csrf
                <label class="field"><span>Catatan</span><textarea name="notes" rows="3"></textarea></label>
                <button class="btn btn-primary w-full" type="submit">Approve Resep</button>
            </form>
            <form class="mt-3 space-y-3" method="post" action="{{ route('apoteker.prescriptions.reject', $order) }}">
                @csrf
                <label class="field"><span>Alasan Reject</span><textarea name="notes" rows="3"></textarea></label>
                <button class="btn btn-danger w-full" type="submit">Reject Resep</button>
            </form>
        @else
            <p class="mt-3 text-sm text-slate-600">Diproses oleh {{ $order->prescription->verifier?->name }} pada {{ optional($order->prescription->verified_at)->format('d M Y H:i') }}.</p>
        @endif
    </div>
</div>
@endsection
