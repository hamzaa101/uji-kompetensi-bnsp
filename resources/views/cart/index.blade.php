@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Keranjang"
        description="Periksa item, jumlah, subtotal, dan total sebelum checkout."
    >
        <x-slot:actions>
        <a class="btn btn-primary" href="{{ route('checkout.create') }}">Checkout</a>
        </x-slot:actions>
    </x-page-header>

    <div class="panel">
        <div class="table-wrap">
            <table><thead><tr><th>Obat</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th>Aksi</th></tr></thead><tbody>
                @forelse($cart->items as $item)
                    <tr>
                        <td class="font-medium">{{ $item->medicine->name }}</td>
                        <td>Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}</td>
                        <td><form class="form-actions" method="post" action="{{ route('cart.update', $item) }}">@csrf @method('put')<input class="w-20" name="quantity" type="number" min="1" value="{{ $item->quantity }}"><button class="btn btn-muted" type="submit">Update</button></form></td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        <td class="actions"><form method="post" action="{{ route('cart.destroy', $item) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Cart masih kosong.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="summary-row">
            <span>Total</span>
            <span>Rp {{ number_format($cart->items->sum(fn($item) => $item->subtotal), 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection
