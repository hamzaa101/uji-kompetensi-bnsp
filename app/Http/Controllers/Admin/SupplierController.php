<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('admin.suppliers.index', ['suppliers' => Supplier::withCount('medicines')->latest()->paginate(10)]);
    }

    public function create()
    {
        return view('admin.suppliers.form', ['supplier' => new Supplier]);
    }

    public function store(Request $request, AuditLogService $audit)
    {
        $supplier = Supplier::create($this->validated($request));
        $audit->record('create_supplier', $supplier);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier dibuat.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier, AuditLogService $audit)
    {
        $supplier->update($this->validated($request));
        $audit->record('update_supplier', $supplier);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier diperbarui.');
    }

    public function destroy(Supplier $supplier, AuditLogService $audit)
    {
        $audit->record('delete_supplier', $supplier);
        $supplier->delete();

        return back()->with('success', 'Supplier dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'max:160'],
            'contact_person' => ['nullable', 'max:120'],
            'phone' => ['nullable', 'max:40'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable'],
        ]);
    }
}
