<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

// Print initial state of product 2
$p = Category::find(2);
echo "INITIAL COLORS FOR ID 2:\n";
print_r($p->colors);

// Simulate the input payload
$fullname = "Nguyen Van A";
$phone = "0912345678";
$address = "123 Street, District 1, HCM";
$note = "Giao hang nhanh";
$payment_method = "cod";
$cart_items = json_encode([
    [
        "id" => 2,
        "name" => "Butterfly Addoy 1000",
        "price" => 500000,
        "variant" => "Đỏ",
        "quantity" => 3
    ]
]);

$items = json_decode($cart_items, true);

DB::beginTransaction();
try {
    // Calculate total price
    $totalPrice = 0;
    foreach ($items as $item) {
        $totalPrice += floatval($item['price']) * intval($item['quantity']);
    }

    // Create Order
    $order = Order::create([
        'user_id'        => null, // Guest or dummy
        'fullname'       => $fullname,
        'phone'          => $phone,
        'address'        => $address,
        'note'           => $note,
        'payment_method' => $payment_method,
        'total_price'    => $totalPrice,
        'status'         => 'pending',
    ]);

    // Save Order Items & Deduct Stock
    foreach ($items as $item) {
        OrderItem::create([
            'order_id'     => $order->id,
            'category_id'  => $item['id'],
            'product_name' => $item['name'],
            'variant'      => $item['variant'] ?? 'Mặc định',
            'price'        => floatval($item['price']),
            'quantity'     => intval($item['quantity']),
        ]);

        $product = Category::find($item['id']);
        if ($product) {
            $orderedQty = intval($item['quantity']);
            $variantName = $item['variant'] ?? 'Mặc định';

            if ($product->colors && is_array($product->colors) && count($product->colors) > 0 && $variantName !== 'Mặc định' && $variantName !== 'Tiêu chuẩn') {
                $colors = $product->colors;
                $updated = false;
                foreach ($colors as &$color) {
                    $cName = $color['name'] ?? '';
                    if ($cName === $variantName) {
                        $currQty = intval($color['quantity'] ?? 0);
                        $color['quantity'] = max(0, $currQty - $orderedQty);
                        $updated = true;
                        break;
                    }
                }
                if ($updated) {
                    $product->colors = $colors;
                    $product->save();
                }
            } else {
                $currQty = intval($product->quantity ?? 0);
                $product->quantity = max(0, $currQty - $orderedQty);
                $product->save();
            }
        }
    }

    DB::commit();
    echo "\nTEST PASSED!\n";
    echo "Order created with ID: " . $order->id . ", Total Price: " . $order->total_price . "\n";

} catch(\Exception $e) {
    DB::rollBack();
    echo "TEST FAILED: " . $e->getMessage() . "\n";
}

// Print final state of product 2
$p = Category::find(2);
echo "\nFINAL COLORS FOR ID 2:\n";
print_r($p->colors);

// Verify order_items
echo "\nORDER ITEMS CREATED:\n";
print_r(OrderItem::where('order_id', $order->id)->get()->toArray());
