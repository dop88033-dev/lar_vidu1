<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

$endpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';
$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

$orderInfo = 'Thanh toan don hang #101';
$amount = '50000';
$orderId = 'TEST_METHOD_' . time();
$redirectUrl = 'http://localhost/payment/momo/callback';
$ipnUrl = 'http://localhost/payment/momo/ipn';
$extraData = '101';
$requestId = (string) time();
$requestType = 'payWithMethod';

$rawHash = 'accessKey=' . $accessKey .
    '&amount=' . $amount .
    '&extraData=' . $extraData .
    '&ipnUrl=' . $ipnUrl .
    '&orderId=' . $orderId .
    '&orderInfo=' . $orderInfo .
    '&partnerCode=' . $partnerCode .
    '&redirectUrl=' . $redirectUrl .
    '&requestId=' . $requestId .
    '&requestType=' . $requestType;

$signature = hash_hmac('sha256', $rawHash, $secretKey);

$data = [
    'partnerCode' => $partnerCode,
    'partnerName' => 'PTShop',
    'storeId' => 'MomoStore',
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature,
];

$response = Http::withOptions(['verify' => false])->post($endpoint, $data);
$res = $response->json();
echo "RESULT CODE: " . ($res['resultCode'] ?? 'NONE') . "\n";
echo "MESSAGE: " . ($res['message'] ?? 'NONE') . "\n";
echo "PAY URL: " . ($res['payUrl'] ?? 'NONE') . "\n";
