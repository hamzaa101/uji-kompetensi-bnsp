@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="grid gap-4 lg:grid-cols-[1fr_360px]">
    <div class="panel">
        <h1 class="text-xl font-semibold">Checkout</h1>
        <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>@foreach($cart->items as $item)<tr><td>{{ $item->medicine->name }} @if($item->medicine->requires_prescription)<span class="status status-warning">Resep</span>@endif</td><td>{{ $item->quantity }}</td><td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
        <p class="mt-4 text-right font-semibold">Total: Rp {{ number_format($cart->items->sum(fn($item) => $item->subtotal), 0, ',', '.') }}</p>
    </div>
    <div class="panel">
        <form class="space-y-4" method="post" enctype="multipart/form-data" action="{{ route('checkout.store') }}">
            @csrf
            <label class="field"><span>Metode Pembayaran</span><select name="payment_method"><option value="transfer">Transfer</option><option value="ewallet">Ewallet</option><option value="cash">Cash</option><option value="insurance">Insurance</option></select></label>
            <label class="field"><span>Upload Resep</span><input id="prescription-input" name="prescription" type="file" accept="image/jpeg,image/png,image/webp"><img id="prescription-preview" class="mt-3 hidden h-40 w-full rounded object-contain bg-slate-100" alt=""></label>
            <label class="field"><span>Catatan</span><textarea name="notes" rows="3"></textarea></label>
            <button class="btn btn-primary w-full" type="submit">Buat Order</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('prescription-input')?.addEventListener('change', event => {
    const file = event.target.files?.[0];
    const preview = document.getElementById('prescription-preview');
    if (!file || !preview) return;
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
});
</script>
@endpush
