<?php

namespace App\Http\Controllers;

use App\Helpers\FlowLogger;
use App\Models\Cart;
use App\Models\Order;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $total = $subtotal;

        return view('checkout.index', compact('cart', 'subtotal', 'total'));
    }

    public function process(Request $request)
    {
        FlowLogger::log('CHECKOUT_PROCESS_START', [
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'request_data' => $request->except('_token'),
        ]);

        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            FlowLogger::log('CHECKOUT_PROCESS_FAIL', 'Cart is empty');
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $data = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_country' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_street' => 'required|string|max:255',
            'shipping_number' => 'nullable|string|max:50',
        ]);

        $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
        $total = $subtotal;

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'subtotal' => $subtotal,
            'discount' => 0,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => 'Flow.cl',
            'shipping_name' => $data['shipping_name'],
            'shipping_email' => $data['shipping_email'],
            'shipping_country' => $data['shipping_country'],
            'shipping_city' => $data['shipping_city'],
            'shipping_street' => $data['shipping_street'],
            'shipping_number' => $data['shipping_number'],
        ]);

        FlowLogger::log('CHECKOUT_ORDER_CREATED', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total' => $total,
            'items_count' => $cart->items->count(),
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }

        session()->put('last_order_id', $order->id);

        Log::info('Checkout - Order created', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total' => $total,
            'items_count' => $cart->items->count(),
        ]);

        try {
            $flowService = new FlowService();
            $payment = $flowService->createPayment([
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'subject' => 'Compra Centra Market - Orden ' . $order->order_number,
                'amount' => (int) round($total),
                'email' => $data['shipping_email'],
            ]);

            FlowLogger::log('CHECKOUT_PAYMENT_CREATED', [
                'payment_url' => $payment['url'] ?? 'MISSING',
                'payment_token' => $payment['token'] ?? 'MISSING',
                'flow_order' => $payment['flowOrder'] ?? 'MISSING',
            ]);

            $order->update(['transaction_id' => $payment['token'] ?? null]);

            $cart->items()->delete();

            $redirectUrl = ($payment['url'] ?? '') . '?token=' . ($payment['token'] ?? '');
            FlowLogger::log('CHECKOUT_REDIRECT', ['url' => $redirectUrl]);

            return redirect($redirectUrl);
        } catch (\Exception $e) {
            FlowLogger::log('CHECKOUT_PAYMENT_FAILED', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ], $e);
            Log::error('Checkout - Payment error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
            $order->update(['status' => 'cancelled']);
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function success()
    {
        $orderId = session('last_order_id');
        $order = $orderId ? Order::find($orderId) : null;

        FlowLogger::log('CHECKOUT_SUCCESS', [
            'session_order_id' => $orderId,
            'order_found' => $order ? $order->order_number : 'NO',
            'order_status' => $order ? $order->status : 'N/A',
        ]);

        return view('checkout.success', compact('order'));
    }

    public function cancel()
    {
        $orderId = session('last_order_id');
        FlowLogger::log('CHECKOUT_CANCEL', [
            'session_order_id' => $orderId,
        ]);

        if ($orderId) {
            Order::where('id', $orderId)->where('status', 'pending')->update(['status' => 'cancelled']);
        }

        return view('checkout.cancel');
    }

    private function getCart(): ?Cart
    {
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();
            if ($cart) return $cart;
        }

        return Cart::where('session_id', session()->getId())->with('items.product')->first();
    }
}
