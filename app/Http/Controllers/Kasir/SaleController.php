<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use RuntimeException;

class SaleController extends Controller
{
    public function dashboard()
    {
        return view('kasir.dashboard', [
            'todayOrders' => Order::where('channel', 'offline')->whereDate('created_at', now())->count(),
            'todaySales' => Order::where('channel', 'offline')->whereDate('created_at', now())->where('payment_status', 'paid')->sum('total_amount'),
            'recent' => Order::with('items.medicine')->where('channel', 'offline')->latest()->limit(5)->get(),
        ]);
    }

    public function create()
    {
        return view('kasir.create', ['medicines' => Medicine::active()->with('batches')->orderBy('name')->get()]);
    }

    public function store(Request $request, CheckoutService $checkout)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:cash,transfer,ewallet,insurance'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.medicine_id' => ['nullable', 'exists:medicines,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $items = collect($data['items'])
            ->filter(fn ($row) => filled($row['medicine_id']) && (int) ($row['quantity'] ?? 0) > 0)
            ->values()
            ->all();

        if ($items === []) {
            return back()->withErrors(['items' => 'Tambahkan minimal satu obat.'])->withInput();
        }

        try {
            $order = $checkout->offlineSale($request->user(), $items, $data['payment_method'], $data['notes'] ?? null);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['stock' => $exception->getMessage()]);
        }

        return view('kasir.receipt', compact('order'));
    }
}
