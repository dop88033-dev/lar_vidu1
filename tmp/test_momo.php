<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$momo = new \App\Services\MomoService();
$order = \App\Models\Order::first();
if (!$order) {
    echo "No order found in database.\n";
    exit;
}
$trans = \App\Models\PaymentTransaction::where('order_id', $order->id)->first();
if (!$trans) {
    $trans = \App\Models\PaymentTransaction::create([
        'order_id' => $order->id,
        'gateway' => 'momo',
        'amount' => $order->total_price ?? 50000,
        'status' => 'pending'
    ]);
}

$res = $momo->createPayment($order, $trans);
print_r($res);
