@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
<div class="panel">
    <h1 class="text-xl font-semibold">Order {{ $order->order_number }}</h1>
    <p class="text-sm text-slate-600">Status: {{ $order->status }} | Pembayaran: {{ $order->payment_status }} | Channel: {{ $order->channel }}</p>
    <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td>{{ $item->medicine->name }}</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->price, 0, ',', '.') }}</td><td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
    <p class="mt-4 text-right text-lg font-semibold">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
    @if($order->prescription)
        <div class="mt-5">
            <h2 class="section-title">Resep</h2>
            <p class="text-sm text-slate-600">Status: {{ $order->prescription->status }} | Catatan: {{ $order->prescription->notes }}</p>
            <img class="mt-3 h-48 rounded object-contain bg-slate-100" src="{{ asset('storage/'.$order->prescription->image_path) }}" alt="Resep">
        </div>
    @endif
</div>
@endsection
