@extends('layouts.app')

@section('title', 'Verifikasi Resep')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Daftar Verifikasi Resep"
        description="Periksa order yang membutuhkan verifikasi resep sebelum diproses lebih lanjut."
    />

    <div class="panel">
        <div class="table-wrap">
            <table><thead><tr><th>Order</th><th>Pasien</th><th>Total</th><th>Resep</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="font-medium">{{ $order->order_number }}</td>
                        <td>{{ $order->user?->name }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td><span class="status status-{{ $order->prescription?->status === 'approved' ? 'success' : ($order->prescription?->status === 'rejected' ? 'critical' : 'warning') }}">{{ $order->prescription?->status }}</span></td>
                        <td><span class="status status-info">{{ $order->status }}</span></td>
                        <td class="actions"><a class="btn btn-primary" href="{{ route('apoteker.prescriptions.show', $order) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada resep.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="table-footer">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
