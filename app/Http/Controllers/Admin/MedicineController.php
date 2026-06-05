<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::query()->with('category', 'supplier')->withSum('batches as stock_sum', 'quantity');

        $query->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"));
        $query->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id));
        $query->when($request->type, fn ($q, $type) => $q->where('type', $type));

        match ($request->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'stock_asc' => $query->orderBy('stock_sum'),
            'stock_desc' => $query->orderByDesc('stock_sum'),
            default => $query->latest(),
        };

        return view('admin.medicines.index', [
            'medicines' => $query->paginate(12)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'types' => ['obat_resep', 'obat_bebas', 'suplemen', 'alat_kesehatan'],
        ]);
    }

    public function create()
    {
        return view('admin.medicines.form', $this->formData(new Medicine));
    }

    public function store(Request $request, AuditLogService $audit)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);
        $data['requires_prescription'] = $request->boolean('requires_prescription');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('medicines', 'public');
        }

        $medicine = Medicine::create($data);
        $audit->record('create_medicine', $medicine);

        return redirect()->route('admin.medicines.index')->with('success', 'Obat dibuat.');
    }

    public function edit(Medicine $medicine)
    {
        return view('admin.medicines.form', $this->formData($medicine));
    }

    public function update(Request $request, Medicine $medicine, AuditLogService $audit)
    {
        $data = $this->validated($request, $medicine);
        $data['slug'] = Str::slug($data['name']);
        $data['requires_prescription'] = $request->boolean('requires_prescription');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('medicines', 'public');
        }

        $medicine->update($data);
        $audit->record('update_medicine', $medicine);

        return redirect()->route('admin.medicines.index')->with('success', 'Obat diperbarui.');
    }

    public function destroy(Medicine $medicine, AuditLogService $audit)
    {
        $medicine->update(['is_active' => false]);
        $audit->record('disable_medicine', $medicine);

        return back()->with('success', 'Obat dinonaktifkan.');
    }

    private function formData(Medicine $medicine): array
    {
        return [
            'medicine' => $medicine,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'types' => ['obat_resep', 'obat_bebas', 'suplemen', 'alat_kesehatan'],
        ];
    }

    private function validated(Request $request, ?Medicine $medicine = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'name' => ['required', 'max:180', Rule::unique('medicines', 'name')->ignore($medicine?->id)],
            'description' => ['nullable'],
            'composition' => ['nullable'],
            'dosage' => ['nullable'],
            'side_effects' => ['nullable'],
            'type' => ['required', Rule::in(['obat_resep', 'obat_bebas', 'suplemen', 'alat_kesehatan'])],
            'price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }
}
