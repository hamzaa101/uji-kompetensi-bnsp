@extends('layouts.app')

@section('title', 'Struk Transaksi')

@section('content')
<div class="panel max-w-2xl">
    <div class="toolbar">
        <h1 class="text-xl font-semibold">Struk {{ $order->order_number }}</h1>
        <button class="btn btn-muted" onclick="window.print()">Print</button>
    </div>
    <p class="text-sm text-slate-600">Kasir: {{ $order->cashier?->name }} | {{ $order->created_at->format('d M Y H:i') }}</p>
    <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td>{{ $item->medicine->name }}</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->price, 0, ',', '.') }}</td><td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
    <p class="mt-4 text-right text-lg font-semibold">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
</div>
@endsection
