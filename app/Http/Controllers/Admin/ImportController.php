<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportMedicinesCsvJob;
use App\Models\ImportJob;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.imports.index', ['imports' => ImportJob::latest()->paginate(10)]);
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

        ImportMedicinesCsvJob::dispatch($import->id);
        $audit->record('queue_import_csv', $import, 'Import CSV masuk queue.');

        return back()->with('success', 'Import CSV masuk queue. Jalankan php artisan queue:work untuk memproses.');
    }
}
