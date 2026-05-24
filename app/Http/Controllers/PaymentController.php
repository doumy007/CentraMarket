<?php

namespace App\Http\Controllers;

use App\Helpers\FlowLogger;
use App\Models\Order;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function confirm(Request $request)
    {
        FlowLogger::log('WEBHOOK_CONFIRM_RECEIVED', [
            'method' => $request->method(),
            'all_params' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);

        Log::info('Flow.cl confirmation received', $request->all());

        $token = $request->input('token');

        if (!$token) {
            FlowLogger::log('WEBHOOK_CONFIRM_NO_TOKEN', 'Token missing from request');
            return response('No token provided', 400);
        }

        try {
            $flowService = new FlowService();
            $paymentStatus = $flowService->verifyPayment($token);

            FlowLogger::log('WEBHOOK_CONFIRM_STATUS', [
                'payment_status' => $paymentStatus,
                'commerceOrder' => $paymentStatus['commerceOrder'] ?? null,
                'status_code' => $paymentStatus['status'] ?? null,
            ]);

            Log::info('Flow.cl payment status', $paymentStatus);

            $orderNumber = $paymentStatus['commerceOrder'] ?? null;

            if ($orderNumber) {
                $order = Order::where('order_number', $orderNumber)->first();

                if ($order) {
                    FlowLogger::log('WEBHOOK_CONFIRM_ORDER_FOUND', [
                        'order_id' => $order->id,
                        'order_current_status' => $order->status,
                    ]);

                    if ($paymentStatus['status'] === 1) {
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

                        FlowLogger::log('WEBHOOK_CONFIRM_PAID', 'Order ' . $order->order_number . ' marked as paid');
                        return response('OK', 200);
                    } else {
                        FlowLogger::log('WEBHOOK_CONFIRM_NOT_PAID', [
                            'status' => $paymentStatus['status'],
                            'expected' => 1,
                        ]);
                    }
                } else {
                    FlowLogger::log('WEBHOOK_CONFIRM_ORDER_NOT_FOUND', [
                        'order_number' => $orderNumber,
                    ]);
                }
            } else {
                FlowLogger::log('WEBHOOK_CONFIRM_NO_ORDERNUMBER', 'commerceOrder not in response');
            }
        } catch (\Exception $e) {
            FlowLogger::log('WEBHOOK_CONFIRM_EXCEPTION', $e->getMessage(), $e);
            Log::error('Flow.cl confirmation error: ' . $e->getMessage());
        }

        return response('Error', 500);
    }

    public function return(Request $request)
    {
        FlowLogger::log('RETURN_URL_HIT', [
            'method' => $request->method(),
            'all_params' => $request->all(),
            'url' => $request->fullUrl(),
        ]);

        $token = $request->input('token');

        if (!$token) {
            FlowLogger::log('RETURN_NO_TOKEN', 'No token in return URL');
            return redirect()->route('checkout.cancel');
        }

        try {
            $flowService = new FlowService();
            $paymentStatus = $flowService->verifyPayment($token);

            FlowLogger::log('RETURN_STATUS_CHECK', [
                'status' => $paymentStatus['status'] ?? null,
                'commerceOrder' => $paymentStatus['commerceOrder'] ?? null,
                'full_response' => $paymentStatus,
            ]);

            if (isset($paymentStatus['status']) && $paymentStatus['status'] === 1) {
                FlowLogger::log('RETURN_SUCCESS', 'Payment confirmed, redirecting to success');
                return redirect()->route('checkout.success');
            } else {
                FlowLogger::log('RETURN_NOT_PAID', [
                    'status_received' => $paymentStatus['status'] ?? 'NOT SET',
                ]);
            }
        } catch (\Exception $e) {
            FlowLogger::log('RETURN_EXCEPTION', $e->getMessage(), $e);
            Log::error('Flow.cl return error: ' . $e->getMessage());
        }

        return redirect()->route('checkout.cancel');
    }
}
