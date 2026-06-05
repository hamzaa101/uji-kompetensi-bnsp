<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $orderId) {}

    public function handle(NotificationService $notifications): void
    {
        $order = Order::find($this->orderId);
        if (! $order || ! in_array($order->status, ['processing', 'paid'], true)) {
            return;
        }

        $order->update(['status' => 'ready']);
        $notifications->create($order->user_id, null, 'Pesanan siap', "Order {$order->order_number} siap diambil/dikirim.", 'success');
    }
}
