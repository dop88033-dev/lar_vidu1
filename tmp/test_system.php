<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MomoService;
use Illuminate\Support\Facades\Route;

echo "=== CHECKING ROUTES ===\n";
$routeNames = [
    'user.payment.index',
    'user.payment.process',
    'user.orders.index',
    'user.orders.show',
    'user.orders.momo.pay',
    'user.orders.momo.start',
    'user.payment.momo.callback',
    'payment.momo.ipn',
    'ghn.webhook'
];

foreach ($routeNames as $name) {
    if (Route::has($name)) {
        echo "Route '{$name}': OK (" . route($name, $name === 'user.orders.show' || $name === 'user.orders.momo.pay' || $name === 'user.orders.momo.start' ? 1 : []) . ")\n";
    } else {
        echo "Route '{$name}': MISSING!\n";
    }
}

echo "\n=== CHECKING MODELS & MIGRATIONS ===\n";
$pt = new PaymentTransaction();
echo "PaymentTransaction table: " . $pt->getTable() . " OK\n";
echo "PaymentTransaction fillable: " . implode(', ', $pt->getFillable()) . " OK\n";

echo "\n=== CHECKING VIEWS ===\n";
$views = [
    'user.payment.index',
    'user.payment.order',
    'user.payment.show',
    'user.cart.index',
    'payment.checkout',
    'user.orders.index',
    'user.orders.show'
];
foreach ($views as $v) {
    echo "View '{$v}': " . (view()->exists($v) ? "EXISTS" : "MISSING!") . "\n";
}

echo "\n=== ALL CHECKS COMPLETED ===\n";
