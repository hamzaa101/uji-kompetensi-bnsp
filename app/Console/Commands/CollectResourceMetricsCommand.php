<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\ResourceMonitoringService;
use App\Services\StockService;
use Illuminate\Console\Command;

class CollectResourceMetricsCommand extends Command
{
    protected $signature = 'app:collect-resource-metrics {--alerts : Generate low stock and expiry alerts too}';

    protected $description = 'Collect prototype resource metrics and optionally generate stock/expiry alerts.';

    public function handle(ResourceMonitoringService $monitoring, StockService $stock, NotificationService $notifications): int
    {
        $metric = $monitoring->snapshot();
        $this->info("Metric #{$metric->id} saved. Memory: {$metric->memory_usage} bytes, pending queue: {$metric->queue_pending}.");

        if ($this->option('alerts')) {
            $low = $notifications->createLowStockAlerts($stock->criticalMedicines());
            $expired = $notifications->createExpiredAlerts($stock->expiringBatches(90));
            $this->info("Generated {$low} low stock alerts and {$expired} expiry alerts.");
        }

        return self::SUCCESS;
    }
}
