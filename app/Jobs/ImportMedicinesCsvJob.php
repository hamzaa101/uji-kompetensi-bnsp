<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\ImportJob;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImportMedicinesCsvJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $importJobId) {}

    public function handle(AuditLogService $audit): void
    {
        $import = ImportJob::findOrFail($this->importJobId);
        $import->update(['status' => 'processing']);

        try {
            $path = Storage::disk('local')->path($import->filename);
            $handle = fopen($path, 'r');
            if (! $handle) {
                throw new \RuntimeException('File CSV tidak dapat dibuka.');
            }

            $header = array_map(fn ($value) => Str::snake(trim((string) $value)), fgetcsv($handle) ?: []);
            $rows = [];
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = array_combine($header, array_pad($data, count($header), null));
            }
            fclose($handle);

            $import->update(['total_rows' => count($rows)]);

            foreach ($rows as $row) {
                if (blank($row['name'] ?? null)) {
                    continue;
                }

                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($row['category'] ?? 'Obat Bebas')],
                    ['name' => $row['category'] ?? 'Obat Bebas']
                );

                $supplier = null;
                if (filled($row['supplier'] ?? null)) {
                    $supplier = Supplier::firstOrCreate(['name' => $row['supplier']]);
                }

                $medicine = Medicine::updateOrCreate(
                    ['slug' => Str::slug($row['name'])],
                    [
                        'category_id' => $category->id,
                        'supplier_id' => $supplier?->id,
                        'name' => $row['name'],
                        'description' => $row['description'] ?? null,
                        'composition' => $row['composition'] ?? null,
                        'dosage' => $row['dosage'] ?? null,
                        'side_effects' => $row['side_effects'] ?? null,
                        'type' => $row['type'] ?? 'obat_bebas',
                        'requires_prescription' => filter_var($row['requires_prescription'] ?? false, FILTER_VALIDATE_BOOL),
                        'price' => (float) ($row['price'] ?? 0),
                        'min_stock' => (int) ($row['min_stock'] ?? 10),
                        'is_active' => true,
                    ]
                );

                $medicine->batches()->updateOrCreate(
                    ['batch_number' => $row['batch_number'] ?? 'IMP-'.now()->format('Ymd')],
                    [
                        'quantity' => (int) ($row['quantity'] ?? 0),
                        'initial_quantity' => (int) ($row['initial_quantity'] ?? $row['quantity'] ?? 0),
                        'expiry_date' => $row['expiry_date'] ?? now()->addYear()->toDateString(),
                        'purchase_price' => filled($row['purchase_price'] ?? null) ? (float) $row['purchase_price'] : null,
                        'received_at' => $row['received_at'] ?? now()->toDateString(),
                    ]
                );

                $import->increment('processed_rows');
            }

            $import->update(['status' => 'completed']);
            $audit->record('import_csv', $import, "Import {$import->original_name} selesai.");
        } catch (Throwable $throwable) {
            $import->update([
                'status' => 'failed',
                'error_message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }
    }
}
