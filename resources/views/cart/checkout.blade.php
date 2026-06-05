@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Checkout"
        description="Pastikan item dan total sudah benar, lalu pilih metode pembayaran dan upload resep bila diperlukan."
    />

    <div class="grid gap-4 lg:grid-cols-[1fr_360px]">
        <div class="panel">
            <h2 class="section-title">Ringkasan Item</h2>
            <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>@forelse($cart->items as $item)<tr><td class="font-medium">{{ $item->medicine->name }} @if($item->medicine->requires_prescription)<span class="status status-warning">Resep</span>@endif</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="3" class="empty">Keranjang kosong.</td></tr>@endforelse</tbody></table></div>
            <div class="summary-row">
                <span>Total</span>
                <span>Rp {{ number_format($cart->items->sum(fn($item) => $item->subtotal), 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="panel">
            <h2 class="section-title">Pembayaran</h2>
            <form class="mt-4 space-y-4" method="post" enctype="multipart/form-data" action="{{ route('checkout.store') }}">
                @csrf
                <label class="field"><span>Metode Pembayaran</span><select name="payment_method"><option value="transfer">Transfer</option><option value="ewallet">Ewallet</option><option value="cash">Cash</option><option value="insurance">Insurance</option></select><x-field-error name="payment_method" /></label>
                <label class="field"><span>Upload Resep</span><input id="prescription-input" name="prescription" type="file" accept="image/jpeg,image/png,image/webp" data-preview-target="#prescription-preview"><x-field-error name="prescription" /><img id="prescription-preview" class="preview-image mt-3 hidden h-40 w-full" alt=""></label>
                <label class="field"><span>Catatan</span><textarea name="notes" rows="3">{{ old('notes') }}</textarea><x-field-error name="notes" /></label>
                <button class="btn btn-primary w-full" type="submit">Buat Order</button>
            </form>
        </div>
    </div>
</div>
@endsection
