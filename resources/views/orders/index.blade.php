@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="panel">
    <h1 class="text-xl font-semibold">Riwayat Pesanan</h1>
    <div class="table-wrap mt-4"><table><thead><tr><th>Order</th><th>Channel</th><th>Status</th><th>Pembayaran</th><th>Total</th><th>Aksi</th></tr></thead><tbody>@foreach($orders as $order)<tr><td>{{ $order->order_number }}</td><td>{{ $order->channel }}</td><td>{{ $order->status }}</td><td>{{ $order->payment_status }}</td><td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td><td><a class="btn btn-muted" href="{{ route('orders.show', $order) }}">Detail</a></td></tr>@endforeach</tbody></table></div>
    {{ $orders->links() }}
</div>
@endsection
