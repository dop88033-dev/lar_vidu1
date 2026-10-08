<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MomoService;

$order = Order::first();
if (!$order) {
    $order = Order::create([
        'user_id' => 1,
        'name' => 'Nguyen Van A',
        'phone' => '0912345678',
        'address' => '123 Le Loi, P1, Q1, TP.HCM',
        'total_price' => 50000,
        'status' => 'pending',
        'payment_method' => 'momo_atm',
    ]);
} else {
    $order->update(['payment_method' => 'momo_atm', 'total_price' => 50000]);
}

$transaction = PaymentTransaction::create([
    'order_id' => $order->id,
    'gateway' => 'momo',
    'amount' => $order->total_price,
    'status' => 'pending',
]);

$service = new MomoService();
$res = $service->createPayment($order, $transaction);

echo "RESULT CODE: " . ($res['resultCode'] ?? 'NONE') . "\n";
echo "MESSAGE: " . ($res['message'] ?? 'NONE') . "\n";
echo "PAY URL: " . ($res['payUrl'] ?? 'NONE') . "\n";
