@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Riwayat Pesanan"
        description="Lihat status order, pembayaran, dan detail transaksi yang pernah dibuat."
    />

    <div class="panel">
        <div class="table-wrap"><table><thead><tr><th>Order</th><th>Channel</th><th>Status</th><th>Pembayaran</th><th>Total</th><th>Aksi</th></tr></thead><tbody>@forelse($orders as $order)<tr><td class="font-medium">{{ $order->order_number }}</td><td>{{ $order->channel }}</td><td><span class="status status-info">{{ $order->status }}</span></td><td><span class="status status-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $order->payment_status }}</span></td><td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td><td class="actions"><a class="btn btn-muted" href="{{ route('orders.show', $order) }}">Detail</a></td></tr>@empty<tr><td colspan="6" class="empty">Belum ada pesanan.</td></tr>@endforelse</tbody></table></div>
        <div class="table-footer">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
