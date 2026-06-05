@extends('layouts.app')

@section('title', 'Verifikasi Resep')

@section('content')
<div class="panel">
    <h1 class="text-xl font-semibold">Daftar Verifikasi Resep</h1>
    <div class="table-wrap mt-4">
        <table><thead><tr><th>Order</th><th>Pasien</th><th>Total</th><th>Resep</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->user?->name }}</td>
                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td><span class="status status-{{ $order->prescription?->status === 'approved' ? 'success' : ($order->prescription?->status === 'rejected' ? 'critical' : 'warning') }}">{{ $order->prescription?->status }}</span></td>
                    <td>{{ $order->status }}</td>
                    <td><a class="btn btn-primary" href="{{ route('apoteker.prescriptions.show', $order) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Tidak ada resep.</td></tr>
            @endforelse
        </tbody></table>
    </div>
    {{ $orders->links() }}
</div>
@endsection
