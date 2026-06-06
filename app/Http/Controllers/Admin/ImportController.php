<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportMedicinesCsvJob;
use App\Models\ImportJob;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Throwable;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.imports.index', [
            'imports' => ImportJob::latest()->paginate(10),
            'hasStalePendingImport' => ImportJob::where('status', 'pending')
                ->where('created_at', '<=', now()->subMinutes(2))
                ->exists(),
        ]);
    }

    public function store(Request $request, AuditLogService $audit)
    {
        $data = $request->validate(['csv' => ['required', 'file', 'mimes:csv,txt', 'max:4096']]);
        $path = $data['csv']->store('imports');
        $import = ImportJob::create([
            'filename' => $path,
            'original_name' => $data['csv']->getClientOriginalName(),
            'created_by' => $request->user()->id,
        ]);

        $audit->record('queue_import_csv', $import, 'Import CSV masuk queue.');

        try {
            ImportMedicinesCsvJob::dispatch($import->id);
        } catch (Throwable $throwable) {
            $import->refresh();

            if (! in_array($import->status, ['completed', 'failed'], true)) {
                $import->update([
                    'status' => 'failed',
                    'error_message' => $throwable->getMessage(),
                ]);
            }

            $audit->record('queue_import_csv_failed', $import, $throwable->getMessage());

            return back()->with('error', 'Import CSV gagal diproses: '.$throwable->getMessage());
        }

        $import->refresh();
        $message = $import->status === 'completed'
            ? 'Import CSV selesai diproses.'
            : 'Import CSV masuk queue. Jalankan php artisan queue:work jika status tetap pending.';

        return back()->with('success', $message);
    }
}
