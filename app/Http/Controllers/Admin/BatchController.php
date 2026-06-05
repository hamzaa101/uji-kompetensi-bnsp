<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\StockMovement;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicineBatch::with('medicine.category')->orderBy('expiry_date');
        $query->when($request->medicine_id, fn ($q, $id) => $q->where('medicine_id', $id));

        return view('admin.batches.index', [
            'batches' => $query->paginate(15)->withQueryString(),
            'medicines' => Medicine::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.batches.form', ['batch' => new MedicineBatch, 'medicines' => Medicine::orderBy('name')->get()]);
    }

    public function store(Request $request, AuditLogService $audit)
    {
        $data = $this->validated($request);
        $data['initial_quantity'] = $data['initial_quantity'] ?? $data['quantity'];
        $batch = MedicineBatch::create($data);
        StockMovement::create([
            'medicine_id' => $batch->medicine_id,
            'medicine_batch_id' => $batch->id,
            'type' => 'in',
            'quantity' => $batch->quantity,
            'description' => 'Batch stok baru',
            'created_by' => $request->user()->id,
        ]);
        $audit->record('create_batch', $batch);

        return redirect()->route('admin.medicine-batches.index')->with('success', 'Batch dibuat.');
    }

    public function edit(MedicineBatch $medicineBatch)
    {
        return view('admin.batches.form', ['batch' => $medicineBatch, 'medicines' => Medicine::orderBy('name')->get()]);
    }

    public function update(Request $request, MedicineBatch $medicineBatch, AuditLogService $audit)
    {
        $before = $medicineBatch->quantity;
        $data = $this->validated($request);
        $data['initial_quantity'] = $data['initial_quantity'] ?? $medicineBatch->initial_quantity;
        $medicineBatch->update($data);

        $diff = $medicineBatch->quantity - $before;
        if ($diff !== 0) {
            StockMovement::create([
                'medicine_id' => $medicineBatch->medicine_id,
                'medicine_batch_id' => $medicineBatch->id,
                'type' => 'adjustment',
                'quantity' => $diff,
                'description' => 'Penyesuaian quantity batch',
                'created_by' => $request->user()->id,
            ]);
        }

        $audit->record('update_batch', $medicineBatch);

        return redirect()->route('admin.medicine-batches.index')->with('success', 'Batch diperbarui.');
    }

    public function destroy(MedicineBatch $medicineBatch, AuditLogService $audit)
    {
        $audit->record('delete_batch', $medicineBatch);
        $medicineBatch->delete();

        return back()->with('success', 'Batch dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'batch_number' => ['required', 'max:80'],
            'quantity' => ['required', 'integer', 'min:0'],
            'initial_quantity' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['required', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'received_at' => ['nullable', 'date'],
        ]);
    }
}
