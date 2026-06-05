<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly NotificationService $notifications,
        private readonly AuditLogService $audit,
    ) {}

    public function checkoutOnline(User $user, string $paymentMethod, ?UploadedFile $prescriptionFile = null, ?string $notes = null): Order
    {
        $cart = $user->cart()->with('items.medicine')->first();

        if (! $cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Cart masih kosong.']);
        }

        $requiresPrescription = $cart->items->contains(fn ($item) => $item->medicine->requires_prescription);
        if ($requiresPrescription && ! $prescriptionFile) {
            throw ValidationException::withMessages(['prescription' => 'Upload resep dokter wajib untuk obat resep.']);
        }

        foreach ($cart->items as $item) {
            $this->stock->assertAvailable($item->medicine, $item->quantity);
        }

        return DB::transaction(function () use ($user, $cart, $paymentMethod, $prescriptionFile, $requiresPrescription, $notes): Order {
            $total = $cart->items->sum(fn ($item) => $item->quantity * (float) $item->price_snapshot);
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->nextOrderNumber('ONL'),
                'channel' => 'online',
                'status' => $requiresPrescription ? 'waiting_prescription' : 'completed',
                'payment_method' => $paymentMethod,
                'payment_status' => $requiresPrescription ? 'waiting_confirmation' : 'paid',
                'total_amount' => $total,
                'notes' => $notes,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'medicine_id' => $item->medicine_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price_snapshot,
                    'subtotal' => $item->quantity * (float) $item->price_snapshot,
                ]);

                if (! $requiresPrescription) {
                    $this->stock->deductFifo($item->medicine, $item->quantity, $order, $user->id, 'Online checkout tanpa resep');
                }
            }

            if ($prescriptionFile) {
                $order->prescription()->create([
                    'user_id' => $user->id,
                    'image_path' => $prescriptionFile->store('prescriptions', 'public'),
                    'status' => 'pending',
                ]);
            }

            $cart->items()->delete();
            $this->notifications->newOrderAlert($order->order_number);
            $this->audit->record('checkout', $order, 'Pasien melakukan checkout online.');

            return $order->load('items.medicine', 'prescription');
        });
    }

    public function offlineSale(User $cashier, array $items, string $paymentMethod = 'cash', ?string $notes = null): Order
    {
        if ($items === []) {
            throw ValidationException::withMessages(['items' => 'Tambahkan minimal satu obat.']);
        }

        $medicines = Medicine::whereIn('id', array_column($items, 'medicine_id'))->get()->keyBy('id');
        foreach ($items as $row) {
            $medicine = $medicines->get((int) $row['medicine_id']);
            if (! $medicine) {
                throw new RuntimeException('Obat tidak ditemukan.');
            }
            $this->stock->assertAvailable($medicine, (int) $row['quantity']);
        }

        return DB::transaction(function () use ($cashier, $items, $paymentMethod, $notes, $medicines): Order {
            $total = collect($items)->sum(fn ($row) => (int) $row['quantity'] * (float) $medicines[(int) $row['medicine_id']]->price);
            $order = Order::create([
                'cashier_id' => $cashier->id,
                'order_number' => $this->nextOrderNumber('KSR'),
                'channel' => 'offline',
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'payment_status' => 'paid',
                'total_amount' => $total,
                'notes' => $notes,
            ]);

            foreach ($items as $row) {
                $medicine = $medicines[(int) $row['medicine_id']];
                $quantity = (int) $row['quantity'];
                $order->items()->create([
                    'medicine_id' => $medicine->id,
                    'quantity' => $quantity,
                    'price' => $medicine->price,
                    'subtotal' => $quantity * (float) $medicine->price,
                ]);
                $this->stock->deductFifo($medicine, $quantity, $order, $cashier->id, 'Transaksi kasir offline');
            }

            $this->audit->record('cashier_checkout', $order, 'Kasir membuat transaksi offline.');

            return $order->load('items.medicine');
        });
    }

    public function verifyPrescription(Order $order, User $apoteker, bool $approved, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $apoteker, $approved, $notes): Order {
            $order->loadMissing('items.medicine', 'prescription');
            if (! $order->prescription || $order->prescription->status !== 'pending') {
                throw ValidationException::withMessages(['prescription' => 'Resep ini sudah diproses.']);
            }

            $order->prescription->update([
                'status' => $approved ? 'approved' : 'rejected',
                'verified_by' => $apoteker->id,
                'verified_at' => now(),
                'notes' => $notes,
            ]);

            if ($approved) {
                foreach ($order->items as $item) {
                    $this->stock->assertAvailable($item->medicine, $item->quantity);
                    $this->stock->deductFifo($item->medicine, $item->quantity, $order, $apoteker->id, 'Resep disetujui apoteker');
                }

                $order->update(['status' => 'processing', 'payment_status' => 'paid']);
                $this->notifications->create($order->user_id, null, 'Resep disetujui', "Order {$order->order_number} sedang diproses.", 'success');
            } else {
                $order->update(['status' => 'prescription_rejected', 'payment_status' => 'failed']);
                $this->notifications->create($order->user_id, null, 'Resep ditolak', $notes ?: "Order {$order->order_number} ditolak.", 'critical');
            }

            $this->audit->record($approved ? 'approve_prescription' : 'reject_prescription', $order, $notes);

            return $order->refresh();
        });
    }

    private function nextOrderNumber(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd-His').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }
}
