<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng với bảng Checkbox chọn sản phẩm
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Hiển thị trang thanh toán (Checkout)
     * Kiểm tra đăng nhập & xác thực email
     */
    public function checkout(Request $request)
    {
        // 1. Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập tài khoản để tiến hành thanh toán!');
        }

        // 2. Kiểm tra tài khoản đã xác thực email chưa
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('error', 'Tài khoản của bạn chưa xác thực email! Vui lòng xác thực email trước khi thanh toán.');
        }

        return view('payment.checkout');
    }

    /**
     * Xử lý đặt hàng & thanh toán (Trực tiếp hoặc MoMo)
     */
    public function processCheckout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập tài khoản để tiến hành thanh toán!');
        }

        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('error', 'Tài khoản của bạn chưa xác thực email!');
        }

        $request->validate([
            'fullname'          => 'required|string|max:255',
            'phone'             => 'required|string|max:20',
            'address'           => 'required|string|max:500',
            'note'              => 'nullable|string|max:500',
            'payment_method'    => 'required|in:cod,momo',
            'cart_items'        => 'required|string',
            'to_district_id'    => 'nullable',
            'to_ward_code'      => 'nullable',
            'total_price_input' => 'nullable',
        ]);

        $items = json_decode($request->cart_items, true);
        if (!$items || count($items) === 0) {
            return redirect()->back()->with('error', 'Đơn hàng của bạn không có sản phẩm nào!');
        }

        \DB::beginTransaction();
        try {
            // 1. Calculate product subtotal
            $subtotal = 0;
            $totalWeight = 0;
            foreach ($items as $item) {
                $subtotal += floatval($item['price']) * intval($item['quantity']);
                $totalWeight += intval($item['weight'] ?? 200) * intval($item['quantity']);
            }

            // 2. Calculate GHN Shipping Fee
            $shippingFee = 0;
            $toDistrictId = $request->to_district_id ? intval($request->to_district_id) : null;
            $toWardCode = $request->to_ward_code ? (string) $request->to_ward_code : null;

            if ($toDistrictId && $toWardCode) {
                try {
                    $ghnService = app(\App\Services\GHNService::class);
                    $feeRes = $ghnService->calculateFee([
                        'service_type_id'  => 2,
                        'from_district_id' => (int) config('services.ghn.from_district_id', 1454),
                        'to_district_id'   => $toDistrictId,
                        'to_ward_code'     => $toWardCode,
                        'weight'           => $totalWeight > 0 ? $totalWeight : 300,
                        'length'           => 15,
                        'width'            => 15,
                        'height'           => 10,
                    ]);
                    if (isset($feeRes['code']) && $feeRes['code'] === 200 && isset($feeRes['data']['total'])) {
                        $shippingFee = intval($feeRes['data']['total']);
                    } elseif ($request->total_price_input) {
                        $inputTotal = floatval($request->total_price_input);
                        if ($inputTotal > $subtotal) {
                            $shippingFee = $inputTotal - $subtotal;
                        }
                    }
                } catch (\Exception $ex) {
                    \Log::warning('GHN calculate fee error during checkout: ' . $ex->getMessage());
                }
            }

            // Total price = product subtotal + shipping fee
            $finalTotalPrice = $subtotal + $shippingFee;

            // 3. Create Order
            $order = \App\Models\Order::create([
                'user_id'        => Auth::id(),
                'name'           => $request->fullname,
                'fullname'       => $request->fullname,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'note'           => $request->note,
                'payment_method' => $request->payment_method,
                'total_price'    => $finalTotalPrice,
                'ghn_total_fee'  => $shippingFee,
                'to_district_id' => $toDistrictId,
                'to_ward_code'   => $toWardCode,
                'shipping_status'=> 'pending',
                'status'         => 'pending',
            ]);

            // 4. Save Order Items & Deduct Stock
            foreach ($items as $item) {
                // 4.1 Create order item
                \App\Models\OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'category_id'  => $item['id'], // Category maps to Product
                    'product_name' => $item['name'],
                    'variant'      => $item['variant'] ?? 'Mặc định',
                    'price'        => floatval($item['price']),
                    'quantity'     => intval($item['quantity']),
                ]);

                // 4.2 Deduct stock for the product
                $product = \App\Models\Category::find($item['id']);
                if ($product) {
                    $orderedQty = intval($item['quantity']);
                    $variantName = $item['variant'] ?? 'Mặc định';

                    // Deduct stock for variants inside JSON colors column
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
                        // Standard option, deduct from base product quantity
                        $currQty = intval($product->quantity ?? 0);
                        $product->quantity = max(0, $currQty - $orderedQty);
                        $product->save();
                    }
                }
            }

            // 5. Optionally create GHN shipping order
            if ($toDistrictId && $toWardCode) {
                try {
                    $ghnOrderService = app(\App\Services\GHNOrderService::class);
                    $ghnRes = $ghnOrderService->create($order, $request->payment_method !== 'cod');
                    if (isset($ghnRes['code']) && $ghnRes['code'] === 200 && isset($ghnRes['data']['order_code'])) {
                        $order->ghn_order_code = $ghnRes['data']['order_code'];
                        if (isset($ghnRes['data']['total_fee'])) {
                            $order->ghn_total_fee = $ghnRes['data']['total_fee'];
                        }
                        $order->shipping_status = 'ready_to_pick';
                        $order->save();
                    }
                } catch (\Exception $ghnEx) {
                    \Log::warning('GHN order creation skipped/failed: ' . $ghnEx->getMessage());
                }
            }

            \DB::commit();

            // Clear session cart too if there's any
            session()->forget('cart');

        } catch(\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }

        $methodName = $request->payment_method === 'momo' ? 'Ví điện tử MoMo' : 'Thanh toán trực tiếp khi nhận hàng (COD)';

        return redirect()->route('welcome')->with('success', '🎉 Đặt hàng thành công qua ' . $methodName . '! Đơn hàng #' . $order->id . ' đã được lưu vào hệ thống.');
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (Session)
     */
    public function add(Request $request, $id)
    {
        $product = \App\Models\Category::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name'     => $product->name,
                'quantity' => 1,
                'price'    => $product->price,
                'category' => $product->category_name ?? 'Chung',
                'image'    => $product->main_image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng thành công!');
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (Session)
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}
