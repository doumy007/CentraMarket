<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        $order->load('items.product');

        return view('orders.show', compact('order'));
    }
}
