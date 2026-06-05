@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Dashboard Kasir"
        description="Ringkasan transaksi offline hari ini dan daftar transaksi terakhir di counter."
    >
        <x-slot:actions>
        <a class="btn btn-primary" href="{{ route('kasir.sales.create') }}">Transaksi Baru</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="stat"><span>Order Offline Hari Ini</span><strong>{{ $todayOrders }}</strong></div>
        <div class="stat"><span>Penjualan Offline Hari Ini</span><strong>Rp {{ number_format($todaySales, 0, ',', '.') }}</strong></div>
    </div>
    <div class="panel">
        <h2 class="section-title">Transaksi Terakhir</h2>
        <div class="table-wrap mt-4"><table><thead><tr><th>Order</th><th>Total</th><th>Waktu</th></tr></thead><tbody>@forelse($recent as $order)<tr><td class="font-medium">{{ $order->order_number }}</td><td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td><td>{{ $order->created_at->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="3" class="empty">Belum ada transaksi.</td></tr>@endforelse</tbody></table></div>
    </div>
</div>
@endsection
