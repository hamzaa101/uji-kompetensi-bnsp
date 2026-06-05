@extends('layouts.app')

@section('title', 'Stok dan Expired')

@section('content')
<div class="space-y-4">
    <div class="panel">
        <h1 class="text-xl font-semibold">Stok Kritis</h1>
        <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Kategori</th><th>Stok</th><th>Min</th></tr></thead><tbody>@foreach($critical as $medicine)<tr><td>{{ $medicine->name }}</td><td>{{ $medicine->category?->name }}</td><td>{{ $medicine->total_stock }}</td><td>{{ $medicine->min_stock }}</td></tr>@endforeach</tbody></table></div>
    </div>
    <div class="grid gap-4 xl:grid-cols-3">
        @foreach(['30' => $expiring30, '60' => $expiring60, '90' => $expiring90] as $days => $batches)
            <div class="panel">
                <h2 class="section-title">Expired {{ $days }} Hari</h2>
                <div class="space-y-2 text-sm">
                    @forelse($batches as $batch)
                        <div class="rounded border border-slate-200 p-2"><strong>{{ $batch->medicine->name }}</strong><br>{{ $batch->batch_number }} | {{ $batch->quantity }} unit | {{ $batch->expiry_date->format('d M Y') }}</div>
                    @empty
                        <p class="empty">Tidak ada.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
