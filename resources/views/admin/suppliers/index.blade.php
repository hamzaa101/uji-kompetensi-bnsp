@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
<div class="panel">
    <div class="toolbar">
        <div><h1 class="text-xl font-semibold">Supplier</h1><p class="text-sm text-slate-600">Data pemasok obat klinik.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.suppliers.create') }}">Tambah</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Kontak</th><th>Email</th><th>Obat</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_person }}<br><span class="text-xs text-slate-500">{{ $supplier->phone }}</span></td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->medicines_count }}</td>
                        <td class="actions">
                            <a class="btn btn-muted" href="{{ route('admin.suppliers.edit', $supplier) }}">Edit</a>
                            <form method="post" action="{{ route('admin.suppliers.destroy', $supplier) }}">@csrf @method('delete')<button class="btn btn-danger" type="submit">Hapus</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $suppliers->links() }}
</div>
@endsection
