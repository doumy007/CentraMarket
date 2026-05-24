<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function confirm(Request $request)
    {
        Log::info('Flow.cl confirmation received', $request->all());

        $token = $request->input('token');

        if (!$token) {
            return response('No token provided', 400);
        }

        try {
            $flowService = new FlowService();
            $paymentStatus = $flowService->verifyPayment($token);

            Log::info('Flow.cl payment status', $paymentStatus);

            $orderNumber = $paymentStatus['commerceOrder'] ?? null;

            if ($orderNumber) {
                $order = Order::where('order_number', $orderNumber)->first();

                if ($order && $paymentStatus['status'] === 1) {
                    $order->update([
                        'status' => 'paid',
                        'transaction_id' => $token,
                    ]);

                    foreach ($order->items as $item) {
                        $product = $item->product;
                        if ($product) {
                            $product->decrement('stock', $item->quantity);
                        }
                    }

                    return response('OK', 200);
                }
            }
        } catch (\Exception $e) {
            Log::error('Flow.cl confirmation error: ' . $e->getMessage());
        }

        return response('Error', 500);
    }

    public function return(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return redirect()->route('checkout.cancel');
        }

        try {
            $flowService = new FlowService();
            $paymentStatus = $flowService->verifyPayment($token);

            if ($paymentStatus['status'] === 1) {
                return redirect()->route('checkout.success');
            }
        } catch (\Exception $e) {
            Log::error('Flow.cl return error: ' . $e->getMessage());
        }

        return redirect()->route('checkout.cancel');
    }
}
