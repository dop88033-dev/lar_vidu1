<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GHNWebhookController extends Controller
{
    private const STATUS_MAP = [
        'ready_to_pick' => 'ready_to_pick',
        'picking' => 'picking',
        'picked' => 'picked',
        'storing' => 'storing',
        'transporting' => 'transporting',
        'sorting' => 'sorting',
        'delivering' => 'delivering',
        'delivered' => 'delivered',
        'cancel' => 'cancelled',
        'cancelled' => 'cancelled',
        'waiting_to_return' => 'return',
        'return' => 'return',
        'returning' => 'returning',
        'return_transporting' => 'return_transporting',
        'return_sorting' => 'return_sorting',
        'returned' => 'returned',
    ];

    public function handle(Request $request)
    {
        $secret = (string) config('services.ghn.webhook_secret');
        $providedSecret = (string) ($request->header('X-GHN-Webhook-Secret') ?: $request->query('secret'));

        if ($secret === '') {
            Log::error('GHN webhook rejected because GHN_WEBHOOK_SECRET is not configured.');
            return response()->json(['message' => 'Webhook is not configured.'], 503);
        }

        if ($providedSecret === '' || !hash_equals($secret, $providedSecret)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $orderCode = $request->input('OrderCode')
            ?? $request->input('order_code')
            ?? $request->input('data.OrderCode')
            ?? $request->input('data.order_code');
        $status = $request->input('Status')
            ?? $request->input('status')
            ?? $request->input('data.Status')
            ?? $request->input('data.status');

        if (!is_string($orderCode) || trim($orderCode) === '' || !is_string($status) || trim($status) === '') {
            return response()->json(['message' => 'OrderCode and Status are required.'], 422);
        }

        $shippingStatus = self::STATUS_MAP[strtolower(trim($status))] ?? null;
        if ($shippingStatus === null) {
            Log::info('Ignored unsupported GHN webhook status.', ['status' => $status]);
            return response()->json(['message' => 'Status ignored.'], 202);
        }

        $order = Order::where('ghn_order_code', trim($orderCode))->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $updates = ['shipping_status' => $shippingStatus];
        if ($shippingStatus === 'delivered') {
            $updates['status'] = 'delivered';
        }

        $order->update($updates);

        return response()->json(['message' => 'Order status updated.']);
    }
}