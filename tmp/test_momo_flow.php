<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MomoService;
use App\Http\Controllers\User\MomoController;

$order = Order::first();
if (!$order) {
    echo "No order found\n";
    exit;
}

$momo = new MomoService();
$res = $momo->createPayment($order, PaymentTransaction::create([
    'order_id' => $order->id,
    'gateway' => 'momo',
    'amount' => $order->total_price,
    'status' => 'pending',
]));

echo "TEST MOMO CREATE PAYMENT:\n";
echo "ResultCode: " . ($res['resultCode'] ?? 'NULL') . "\n";
echo "Message: " . ($res['message'] ?? 'NULL') . "\n";
echo "PayUrl: " . ($res['payUrl'] ?? 'NULL') . "\n";
