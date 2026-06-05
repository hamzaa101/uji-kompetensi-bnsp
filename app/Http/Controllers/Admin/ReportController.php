<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateSalesReportJob;
use App\Services\AuditLogService;
use App\Services\ReportService;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports)
    {
        [$from, $to] = $this->range($request);

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'daily' => $reports->salesPerDay($from, $to),
            'monthly' => $reports->salesPerMonth($from, $to),
            'topMedicines' => $reports->topMedicines($from, $to),
            'expiring' => $reports->expiringMedicines(90),
            'statusRecap' => $reports->orderStatusRecap($from, $to),
            'criticalStock' => $reports->criticalStock(),
        ]);
    }

    public function exportPdf(Request $request, ReportService $reports, AuditLogService $audit)
    {
        [$from, $to] = $this->range($request);
        $payload = [
            'from' => $from,
            'to' => $to,
            'daily' => $reports->salesPerDay($from, $to),
            'monthly' => $reports->salesPerMonth($from, $to),
            'topMedicines' => $reports->topMedicines($from, $to),
            'statusRecap' => $reports->orderStatusRecap($from, $to),
            'criticalStock' => $reports->criticalStock(),
        ];

        $pdf = new Dompdf;
        $pdf->loadHtml(view('admin.reports.pdf', $payload)->render());
        $pdf->setPaper('A4');
        $pdf->render();

        $audit->record('export_report_pdf', null, "Export PDF laporan {$from} - {$to}");

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=laporan-{$from}-{$to}.pdf",
        ]);
    }

    public function generateJob(Request $request)
    {
        [$from, $to] = $this->range($request);
        GenerateSalesReportJob::dispatch($request->user()->id, $from, $to);

        return back()->with('success', 'Job generate laporan dimasukkan ke queue.');
    }

    private function range(Request $request): array
    {
        return [
            $request->date('from')?->toDateString() ?? now()->subDays(30)->toDateString(),
            $request->date('to')?->toDateString() ?? now()->toDateString(),
        ];
    }
}
