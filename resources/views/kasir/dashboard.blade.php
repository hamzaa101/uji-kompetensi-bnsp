@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="space-y-4">
    <div class="toolbar">
        <h1 class="text-2xl font-semibold">Dashboard Kasir</h1>
        <a class="btn btn-primary" href="{{ route('kasir.sales.create') }}">Transaksi Baru</a>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div class="stat"><span>Order Offline Hari Ini</span><strong>{{ $todayOrders }}</strong></div>
        <div class="stat"><span>Penjualan Offline Hari Ini</span><strong>Rp {{ number_format($todaySales, 0, ',', '.') }}</strong></div>
    </div>
    <div class="panel">
        <h2 class="section-title">Transaksi Terakhir</h2>
        <div class="table-wrap"><table><thead><tr><th>Order</th><th>Total</th><th>Waktu</th></tr></thead><tbody>@foreach($recent as $order)<tr><td>{{ $order->order_number }}</td><td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td><td>{{ $order->created_at->format('d M Y H:i') }}</td></tr>@endforeach</tbody></table></div>
    </div>
</div>
@endsection
