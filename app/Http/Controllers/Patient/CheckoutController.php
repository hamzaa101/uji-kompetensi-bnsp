<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use RuntimeException;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $cart = $request->user()->cart()->with('items.medicine')->firstOrCreate();

        return view('cart.checkout', ['cart' => $cart]);
    }

    public function store(Request $request, CheckoutService $checkout)
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:cash,transfer,ewallet,insurance'],
            'notes' => ['nullable', 'string'],
            'prescription' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        try {
            $order = $checkout->checkoutOnline(
                $request->user(),
                $data['payment_method'],
                $request->file('prescription'),
                $data['notes'] ?? null
            );
        } catch (RuntimeException $exception) {
            return back()->withErrors(['stock' => $exception->getMessage()]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Checkout berhasil.');
    }
}
