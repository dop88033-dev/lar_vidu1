<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MomoService;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

try {
    echo "=== TESTING END-TO-END CHECKOUT & PAYMENT ===\n";

    $user = User::first();
    if (!$user) {
        echo "ERROR: No user found in database!\n";
        exit(1);
    }
    Auth::login($user);

    // 1. Create a Test Order
    $order = Order::create([
        'user_id' => $user->id,
        'name' => 'Nguyen Van Test',
        'fullname' => 'Nguyen Van Test',
        'phone' => '0912345678',
        'address' => '123 Đường Lê Lợi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
        'total_price' => 150000,
        'status' => 'pending',
        'payment_method' => 'momo_cc',
        'to_district_id' => 1454,
        'to_ward_code' => '21211',
        'ghn_total_fee' => 30000,
        'shipping_status' => 'pending',
    ]);

    echo "1. Order Created ID: #{$order->id}\n";

    // 2. Create Payment Transaction
    $transaction = PaymentTransaction::create([
        'order_id' => $order->id,
        'gateway' => 'momo',
        'amount' => $order->total_price,
        'status' => 'pending',
    ]);

    echo "2. PaymentTransaction Created ID: #{$transaction->id}\n";

    // 3. Create MoMo Payment
    $momo = new MomoService();
    $result = $momo->createPayment($order, $transaction);

    echo "3. MoMo API Result Code: " . ($result['resultCode'] ?? 'NULL') . "\n";
    echo "   Message: " . ($result['message'] ?? 'NULL') . "\n";
    echo "   PayUrl: " . ($result['payUrl'] ?? 'NULL') . "\n";

    // 4. Simulate MoMo Callback (Payment Success)
    $gatewayOrderId = $transaction->gateway_order_id;
    $requestId = (string) time();
    $amount = (string) $order->total_price;
    $accessKey = config('services.momo.access_key');
    $secretKey = config('services.momo.secret_key');
    $partnerCode = config('services.momo.partner_code');
    $message = 'Success';
    $orderInfo = 'Thanh toan don hang #' . $order->id;
    $orderType = 'momo_wallet';
    $payType = 'credit';
    $responseTime = (string) (time() * 1000);
    $resultCode = '0';
    $transId = (string) rand(1000000000, 9999999999);
    $extraData = (string) $order->id;

    $rawHash = 'accessKey=' . $accessKey .
        '&amount=' . $amount .
        '&extraData=' . $extraData .
        '&message=' . $message .
        '&orderId=' . $gatewayOrderId .
        '&orderInfo=' . $orderInfo .
        '&orderType=' . $orderType .
        '&partnerCode=' . $partnerCode .
        '&payType=' . $payType .
        '&requestId=' . $requestId .
        '&responseTime=' . $responseTime .
        '&resultCode=' . $resultCode .
        '&transId=' . $transId;

    $signature = hash_hmac('sha256', $rawHash, $secretKey);

    $callbackPayload = [
        'partnerCode' => $partnerCode,
        'orderId' => $gatewayOrderId,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderInfo' => $orderInfo,
        'orderType' => $orderType,
        'transId' => $transId,
        'resultCode' => $resultCode,
        'message' => $message,
        'payType' => $payType,
        'responseTime' => $responseTime,
        'extraData' => $extraData,
        'signature' => $signature,
    ];

    $momoController = new \App\Http\Controllers\User\MomoController();
    $ghnOrders = new GHNOrderService(new \App\Services\GHNService());
    $req = Request::create(route('user.payment.momo.callback'), 'GET', $callbackPayload);

    $res = $momoController->callback($req, $ghnOrders, $momo);

    $order->refresh();
    echo "4. Post-Callback Order Status: " . $order->status . "\n";
    echo "   GHN Order Code: " . ($order->ghn_order_code ?? 'None') . "\n";

    if ($order->status === 'paid') {
        echo "=== ALL PAYMENT TESTS PASSED PERFECTLY ===\n";
    } else {
        echo "=== TEST FAILED: ORDER NOT MARKED PAID ===\n";
    }

} catch (\Throwable $e) {
    echo "EX: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
