<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function totalStock(Medicine|int $medicine): int
    {
        $medicineId = $medicine instanceof Medicine ? $medicine->id : $medicine;

        return (int) MedicineBatch::where('medicine_id', $medicineId)->sum('quantity');
    }

    public function criticalMedicines(): Collection
    {
        return Medicine::query()
            ->with('category')
            ->active()
            ->get()
            ->filter(fn (Medicine $medicine) => $this->totalStock($medicine) <= $medicine->min_stock)
            ->values();
    }

    public function expiringBatches(int $days = 90): Collection
    {
        return MedicineBatch::query()
            ->with('medicine.category')
            ->where('quantity', '>', 0)
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->orderBy('expiry_date')
            ->get();
    }

    public function assertAvailable(Medicine|int $medicine, int $quantity): void
    {
        $name = $medicine instanceof Medicine ? $medicine->name : Medicine::find($medicine)?->name;
        if ($this->totalStock($medicine) < $quantity) {
            throw new RuntimeException("Stok {$name} tidak cukup untuk quantity {$quantity}.");
        }
    }

    public function deductFifo(Medicine|int $medicine, int $quantity, ?Order $order = null, ?int $createdBy = null, ?string $description = null): void
    {
        $medicineId = $medicine instanceof Medicine ? $medicine->id : $medicine;
        $remaining = $quantity;

        DB::transaction(function () use ($medicineId, &$remaining, $order, $createdBy, $description): void {
            $batches = MedicineBatch::query()
                ->where('medicine_id', $medicineId)
                ->where('quantity', '>', 0)
                ->orderBy('expiry_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($batches->sum('quantity') < $remaining) {
                $medicine = Medicine::find($medicineId);
                throw new RuntimeException("Stok {$medicine?->name} tidak cukup.");
            }

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                // FIFO: batch dengan tanggal kedaluwarsa terdekat selalu dikurangi lebih dulu.
                $deducted = min($remaining, $batch->quantity);
                $batch->decrement('quantity', $deducted);
                $remaining -= $deducted;

                StockMovement::create([
                    'medicine_id' => $medicineId,
                    'medicine_batch_id' => $batch->id,
                    'order_id' => $order?->id,
                    'type' => 'out',
                    'quantity' => $deducted,
                    'description' => $description ?? 'FIFO stock deduction',
                    'created_by' => $createdBy,
                ]);
            }
        });
    }
}
