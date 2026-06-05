<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\StockService;
use Illuminate\Http\Request;
use RuntimeException;

class ApotekerController extends Controller
{
    public function dashboard(StockService $stock)
    {
        return view('apoteker.dashboard', [
            'pending' => Order::where('status', 'waiting_prescription')->count(),
            'critical' => $stock->criticalMedicines()->count(),
            'expiring' => $stock->expiringBatches(90)->count(),
        ]);
    }

    public function prescriptions()
    {
        return view('apoteker.prescriptions', [
            'orders' => Order::with('user', 'items.medicine', 'prescription')
                ->whereHas('prescription')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function showPrescription(Order $order)
    {
        abort_unless($order->prescription, 404);

        return view('apoteker.prescription_detail', ['order' => $order->load('user', 'items.medicine', 'prescription')]);
    }

    public function approve(Request $request, Order $order, CheckoutService $checkout)
    {
        $request->validate(['notes' => ['nullable', 'string']]);
        try {
            $checkout->verifyPrescription($order, $request->user(), true, $request->notes);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['stock' => $exception->getMessage()]);
        }

        return redirect()->route('apoteker.prescriptions')->with('success', 'Resep disetujui dan stok dikurangi FIFO.');
    }

    public function reject(Request $request, Order $order, CheckoutService $checkout)
    {
        $request->validate(['notes' => ['nullable', 'string']]);
        $checkout->verifyPrescription($order, $request->user(), false, $request->notes);

        return redirect()->route('apoteker.prescriptions')->with('success', 'Resep ditolak.');
    }

    public function stockAlerts(StockService $stock)
    {
        return view('apoteker.stock_alerts', [
            'critical' => $stock->criticalMedicines(),
            'expiring30' => $stock->expiringBatches(30),
            'expiring60' => $stock->expiringBatches(60),
            'expiring90' => $stock->expiringBatches(90),
        ]);
    }
}
