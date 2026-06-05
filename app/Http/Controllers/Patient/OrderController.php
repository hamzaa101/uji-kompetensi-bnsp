<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.medicine', 'prescription')->latest();
        if ($request->user()->role === 'pasien') {
            $query->where('user_id', $request->user()->id);
        }

        return view('orders.index', ['orders' => $query->paginate(10)]);
    }

    public function show(Request $request, Order $order)
    {
        if ($request->user()->role === 'pasien') {
            abort_unless($order->user_id === $request->user()->id, 403);
        }

        return view('orders.show', ['order' => $order->load('items.medicine', 'prescription')]);
    }
}
