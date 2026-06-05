@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
<div class="space-y-4">
    <x-page-header
        :title="'Order '.$order->order_number"
        :description="'Status: '.$order->status.' | Pembayaran: '.$order->payment_status.' | Channel: '.$order->channel"
    />

    <div class="panel">
        <div class="table-wrap"><table><thead><tr><th>Obat</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td class="font-medium">{{ $item->medicine->name }}</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->price, 0, ',', '.') }}</td><td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
        <div class="summary-row">
            <span>Total</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($order->prescription)
        <div class="panel">
            <h2 class="section-title">Resep</h2>
            <p class="section-description">Status: {{ $order->prescription->status }} | Catatan: {{ $order->prescription->notes }}</p>
            <img class="preview-image mt-3 h-48 w-full max-w-md" src="{{ asset('storage/'.$order->prescription->image_path) }}" alt="Resep">
        </div>
    @endif
</div>
@endsection
