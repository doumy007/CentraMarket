<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderLookupController extends Controller
{
    public function showForm()
    {
        return view('orders.lookup');
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $orders = Order::where('shipping_email', $data['email'])
            ->latest()
            ->paginate(10);

        return view('orders.lookup-results', compact('orders', 'data'));
    }
}
