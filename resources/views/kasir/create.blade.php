@extends('layouts.app')

@section('title', 'Transaksi Kasir')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Transaksi Offline / Counter"
        description="Input transaksi langsung di kasir. Validasi stok dan harga tetap diproses oleh server."
    />

    <div class="panel">
        <form class="space-y-4" method="post" action="{{ route('kasir.sales.store') }}">
            @csrf
            <div class="form-grid">
                <label class="field"><span>Metode Pembayaran</span><select name="payment_method"><option value="cash">Cash</option><option value="transfer">Transfer</option><option value="ewallet">Ewallet</option><option value="insurance">Insurance</option></select><x-field-error name="payment_method" /></label>
                <label class="field"><span>Catatan</span><input name="notes" value="{{ old('notes') }}" placeholder="Opsional"><x-field-error name="notes" /></label>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Obat</th><th>Stok</th><th>Harga</th><th>Qty</th></tr></thead>
                    <tbody>
                        @for($i = 0; $i < 6; $i++)
                            <tr>
                                <td><select name="items[{{ $i }}][medicine_id]"><option value="">Pilih obat</option>@foreach($medicines as $medicine)<option value="{{ $medicine->id }}" @selected(old("items.$i.medicine_id") == $medicine->id)>{{ $medicine->name }}</option>@endforeach</select><x-field-error :name="'items.'.$i.'.medicine_id'" /></td>
                                <td class="text-sm text-slate-500">Pilih untuk validasi server</td>
                                <td class="text-sm text-slate-500">Harga snapshot otomatis</td>
                                <td><input name="items[{{ $i }}][quantity]" type="number" min="1" value="{{ old("items.$i.quantity", $i === 0 ? 1 : '') }}"><x-field-error :name="'items.'.$i.'.quantity'" /></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Checkout Cash</button>
                <a class="btn btn-muted" href="{{ route('kasir.dashboard') }}">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
