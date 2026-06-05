<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\ErrorLog;
use Illuminate\Support\Collection;

class NotificationService
{
    public function create(?int $userId, ?string $role, string $title, string $message, string $type = 'info'): AppNotification
    {
        return AppNotification::create([
            'user_id' => $userId,
            'role_target' => $role,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);
    }

    public function createLowStockAlerts(Collection $medicines): int
    {
        $count = 0;
        foreach ($medicines as $medicine) {
            $this->create(null, 'admin', 'Stok kritis', "{$medicine->name} tersisa {$medicine->total_stock} unit.", 'warning');
            $this->create(null, 'apoteker', 'Stok kritis', "{$medicine->name} tersisa {$medicine->total_stock} unit.", 'warning');
            $count++;
        }

        return $count;
    }

    public function createExpiredAlerts(Collection $batches): int
    {
        $count = 0;
        foreach ($batches as $batch) {
            $days = now()->startOfDay()->diffInDays($batch->expiry_date, false);
            $this->create(
                null,
                'apoteker',
                'Obat hampir kedaluwarsa',
                "{$batch->medicine->name} batch {$batch->batch_number} kedaluwarsa dalam {$days} hari.",
                $days <= 30 ? 'critical' : 'warning'
            );
            $count++;
        }

        return $count;
    }

    public function createErrorAlert(ErrorLog $error): void
    {
        $this->create(null, 'admin', 'Error aplikasi', $error->message, $error->severity === 'critical' ? 'critical' : 'warning');
    }

    public function newOrderAlert(string $orderNumber): void
    {
        $this->create(null, 'admin', 'Pesanan baru', "Order {$orderNumber} masuk.", 'info');
        $this->create(null, 'apoteker', 'Pesanan baru', "Order {$orderNumber} perlu dipantau.", 'info');
    }
}
