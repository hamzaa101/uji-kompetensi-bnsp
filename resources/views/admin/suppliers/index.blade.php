@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
<div class="space-y-4">
    <x-page-header
        title="Supplier"
        description="Data pemasok obat klinik untuk kebutuhan pembelian dan pelacakan stok."
    >
        <x-slot:actions>
        <a class="btn btn-primary" href="{{ route('admin.suppliers.create') }}">Tambah</a>
        </x-slot:actions>
    </x-page-header>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama</th><th>Kontak</th><th>Email</th><th>Obat</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="font-medium">{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person }}<br><span class="text-xs text-slate-500">{{ $supplier->phone }}</span></td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->medicines_count }}</td>
                            <td class="actions">
                                <a class="btn btn-muted" href="{{ route('admin.suppliers.edit', $supplier) }}">Edit</a>
                                <form method="post" action="{{ route('admin.suppliers.destroy', $supplier) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Supplier belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
