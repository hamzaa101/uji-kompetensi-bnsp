@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<div class="panel">
    <div class="toolbar">
        <h1 class="text-xl font-semibold">Cart</h1>
        <a class="btn btn-primary" href="{{ route('checkout.create') }}">Checkout</a>
    </div>
    <div class="table-wrap mt-4">
        <table><thead><tr><th>Obat</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th>Aksi</th></tr></thead><tbody>
            @forelse($cart->items as $item)
                <tr>
                    <td>{{ $item->medicine->name }}</td>
                    <td>Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}</td>
                    <td><form class="flex gap-2" method="post" action="{{ route('cart.update', $item) }}">@csrf @method('put')<input class="w-20" name="quantity" type="number" min="1" value="{{ $item->quantity }}"><button class="btn btn-muted" type="submit">Update</button></form></td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    <td><form method="post" action="{{ route('cart.destroy', $item) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Cart masih kosong.</td></tr>
            @endforelse
        </tbody></table>
    </div>
    <p class="mt-4 text-right text-lg font-semibold">Total: Rp {{ number_format($cart->items->sum(fn($item) => $item->subtotal), 0, ',', '.') }}</p>
</div>
@endsection
