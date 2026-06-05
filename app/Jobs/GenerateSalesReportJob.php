<?php

namespace App\Jobs;

use App\Services\NotificationService;
use App\Services\ReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSalesReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $userId, public string $from, public string $to) {}

    public function handle(ReportService $reports, NotificationService $notifications): void
    {
        $daily = $reports->salesPerDay($this->from, $this->to);
        $notifications->create(
            $this->userId,
            null,
            'Laporan selesai',
            'Generate laporan besar selesai dengan '.count($daily).' periode harian.',
            'success'
        );
    }
}
