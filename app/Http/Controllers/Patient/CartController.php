<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Medicine;
use App\Services\StockService;
use Illuminate\Http\Request;
use RuntimeException;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->user()->cart()->firstOrCreate();

        return view('cart.index', ['cart' => $cart->load('items.medicine')]);
    }

    public function add(Request $request, Medicine $medicine, StockService $stock)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        try {
            $stock->assertAvailable($medicine, (int) $data['quantity']);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['quantity' => $exception->getMessage()]);
        }
        $cart = $request->user()->cart()->firstOrCreate();
        $item = $cart->items()->firstOrNew(['medicine_id' => $medicine->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + (int) $data['quantity'];
        $item->price_snapshot = $medicine->price;
        $item->save();

        return redirect()->route('cart.index')->with('success', 'Obat ditambahkan ke cart.');
    }

    public function update(Request $request, CartItem $cartItem, StockService $stock)
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        try {
            $stock->assertAvailable($cartItem->medicine, (int) $data['quantity']);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['quantity' => $exception->getMessage()]);
        }
        $cartItem->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Cart diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
        $cartItem->delete();

        return back()->with('success', 'Item dihapus.');
    }
}
