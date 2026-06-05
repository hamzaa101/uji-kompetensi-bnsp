@extends('layouts.app')

@section('title', 'Stok dan Expired')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Stok dan Expired"
        description="Daftar obat stok kritis serta batch yang mendekati kedaluwarsa dalam 30, 60, dan 90 hari."
    />

    <div class="panel">
        <h2 class="section-title">Stok Kritis</h2>
        <div class="table-wrap mt-4"><table><thead><tr><th>Obat</th><th>Kategori</th><th>Stok</th><th>Min</th></tr></thead><tbody>@forelse($critical as $medicine)<tr><td class="font-medium">{{ $medicine->name }}</td><td>{{ $medicine->category?->name }}</td><td><span class="stock-low">{{ $medicine->total_stock }}</span></td><td>{{ $medicine->min_stock }}</td></tr>@empty<tr><td colspan="4" class="empty">Tidak ada stok kritis.</td></tr>@endforelse</tbody></table></div>
    </div>
    <div class="grid gap-4 xl:grid-cols-3">
        @foreach(['30' => $expiring30, '60' => $expiring60, '90' => $expiring90] as $days => $batches)
            <div class="panel">
                <h2 class="section-title">Expired {{ $days }} Hari</h2>
                <div class="mt-4 space-y-2 text-sm">
                    @forelse($batches as $batch)
                        <div class="compact-card"><strong>{{ $batch->medicine->name }}</strong><br>{{ $batch->batch_number }} | {{ $batch->quantity }} unit | {{ $batch->expiry_date->format('d M Y') }}</div>
                    @empty
                        <p class="empty">Tidak ada.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
