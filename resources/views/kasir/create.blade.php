@extends('layouts.app')

@section('title', 'Transaksi Kasir')

@section('content')
<div class="panel">
    <h1 class="text-xl font-semibold">Transaksi Offline / Counter</h1>
    <form class="mt-5 space-y-4" method="post" action="{{ route('kasir.sales.store') }}">
        @csrf
        <div class="grid gap-3 md:grid-cols-2">
            <label class="field"><span>Metode Pembayaran</span><select name="payment_method"><option value="cash">Cash</option><option value="transfer">Transfer</option><option value="ewallet">Ewallet</option><option value="insurance">Insurance</option></select></label>
            <label class="field"><span>Catatan</span><input name="notes" placeholder="Opsional"></label>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Obat</th><th>Stok</th><th>Harga</th><th>Qty</th></tr></thead>
                <tbody>
                    @for($i = 0; $i < 6; $i++)
                        <tr>
                            <td><select name="items[{{ $i }}][medicine_id]"><option value="">Pilih obat</option>@foreach($medicines as $medicine)<option value="{{ $medicine->id }}">{{ $medicine->name }}</option>@endforeach</select></td>
                            <td class="text-sm text-slate-500">Pilih untuk validasi server</td>
                            <td class="text-sm text-slate-500">Harga snapshot otomatis</td>
                            <td><input name="items[{{ $i }}][quantity]" type="number" min="1" value="{{ $i === 0 ? 1 : '' }}"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <button class="btn btn-primary" type="submit">Checkout Cash</button>
    </form>
</div>
@endsection
