<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Services\GHNService;
use App\Services\GHNOrderService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // ==========================================
    // 1. CÁC VIEW HIỂN THỊ ĐƠN HÀNG & THANH TOÁN
    // ==========================================
    public function index()
    {
        $cart = session('cart', []);
        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        if (view()->exists('user.payment.index')) {
            return view('user.payment.index', compact('cart', 'totalPrice'));
        }
        
        return view('payment.checkout', compact('cart', 'totalPrice'));
    }

    public function orderHistory()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'paymentTransactions' => function ($query) {
                $query->latest();
            }])
            ->orderByDesc('created_at')
            ->paginate(10);

        if (view()->exists('user.payment.order')) {
            return view('user.payment.order', compact('orders'));
        }
        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && !(Auth::user() && Auth::user()->is_admin)) {
            abort(403);
        }
        $order->load('items.product');

        if (view()->exists('user.payment.show')) {
            return view('user.payment.show', compact('order'));
        }
        return view('user.orders.show', compact('order'));
    }

    // ==========================================
    // 2. AJAX LOCATION & TÍNH PHÍ GHN
    // ==========================================
    public function getProvinces(GHNService $ghn)
    {
        $res = $ghn->getProvinces();
        if (isset($res['data']) && is_array($res['data'])) {
            $res['data'] = array_values(array_filter($res['data'], function ($p) {
                $name = $p['ProvinceName'] ?? '';
                return !str_contains(strtolower($name), 'test') && !str_contains(strtolower($name), 'alert');
            }));
            usort($res['data'], function ($a, $b) {
                return strcmp($a['ProvinceName'] ?? '', $b['ProvinceName'] ?? '');
            });
        }
        return response()->json($res);
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $cart = session('cart', []);
        $totalWeight = 0;
        foreach ($cart as $item) {
            $totalWeight += ((int)($item['weight'] ?? 200)) * (int)$item['quantity'];
        }
        $res = $ghn->calculateFee([
            'service_type_id' => 2, // Gói chuẩn E-commerce
            'from_district_id' => (int) config('services.ghn.from_district_id', 1454),
            'to_district_id' => (int) $request->to_district_id,
            'to_ward_code' => (string) $request->to_ward_code,
            'weight' => $totalWeight > 0 ? $totalWeight : 300,
            'length' => 15,
            'width' => 15,
            'height' => 10,
        ]);
        return response()->json($res);
    }

    // ==========================================
    // 3. XỬ LÝ ĐẶT HÀNG (PROCESS PAYMENT)
    // ==========================================
    public function processPayment(Request $request, GHNService $ghn, GHNOrderService $ghnOrders)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'regex:/^0(3|5|7|8|9)\d{8}$/'],
            'address' => 'required|string|max:255',
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'payment_method' => 'required|in:cod,momo,momo_atm,momo_cc',
        ], [
            'phone.regex' => 'Số điện thoại không hợp lệ (phải bắt đầu bằng 03, 05, 07, 08, 09 và đủ 10 chữ số).',
            'to_district_id.required' => 'Vui lòng chọn Quận/Huyện giao hàng.',
            'to_ward_code.required' => 'Vui lòng chọn Phường/Xã giao hàng.',
        ]);

        try {
            // Tự động đồng bộ giỏ hàng từ localStorage (gửi qua cart_items) vào Session nếu Session trống
            if (empty(session('cart')) && $request->has('cart_items') && !empty($request->cart_items)) {
                $jsonItems = json_decode($request->cart_items, true);
                if (is_array($jsonItems) && count($jsonItems) > 0) {
                    $formattedCart = [];
                    foreach ($jsonItems as $key => $item) {
                        $id = $item['id'] ?? (is_numeric($key) ? $key : 1);
                        $formattedCart[$id] = [
                            'id'       => $id,
                            'name'     => $item['name'] ?? 'Sản phẩm',
                            'price'    => (float) ($item['price'] ?? 0),
                            'quantity' => (int) ($item['quantity'] ?? 1),
                            'variant'  => $item['variant'] ?? 'Mặc định',
                            'weight'   => (int) ($item['weight'] ?? 200),
                            'image'    => $item['image'] ?? null,
                        ];
                    }
                    session(['cart' => $formattedCart]);
                }
            }

            $cart = session('cart', []);
            if (empty($cart)) {
                return redirect()->route('user.cart.index')->with('error', 'Không thể thanh toán vì giỏ hàng trống.');
            }

            // 1. Tính tổng tiền hàng và tổng khối lượng sản phẩm
            $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
            $totalWeight = collect($cart)->sum(
                fn($item) => ($item['weight'] ?? $ghn->productWeight()) * (int) $item['quantity']
            );

            // 2. Tính lại phí ship chuẩn xác từ GHN trên server
            $feeResponse = $ghn->calculateFee(array_merge([
                'from_district_id' => (int) config('services.ghn.from_district_id', 1454),
                'to_district_id' => (int) $request->to_district_id,
                'to_ward_code' => (string) $request->to_ward_code,
            ], $ghn->packageParameters($totalWeight)));

            $shippingFee = (isset($feeResponse['code']) && $feeResponse['code'] == 200)
                ? (int) $feeResponse['data']['total']
                : 0;

            // Tổng thanh toán = Tiền hàng + Phí ship
            $finalTotal = $subtotal + $shippingFee;

            // 3. Tạo đơn hàng và chi tiết đơn hàng trong Database
            $order = DB::transaction(function () use ($request, $shippingFee, $finalTotal, $cart) {
                $order = Order::create([
                    'user_id'         => Auth::id(),
                    'name'            => $request->name,
                    'fullname'        => $request->name,
                    'address'         => $request->address,
                    'phone'           => $request->phone,
                    'total_price'     => $finalTotal,
                    'status'          => 'pending',
                    'payment_method'  => $request->payment_method ?? 'cod',
                    'note'            => $request->note ?? null,
                    'to_district_id'  => (int) $request->to_district_id,
                    'to_ward_code'    => (string) $request->to_ward_code,
                    'ghn_total_fee'   => $shippingFee,
                    'shipping_status' => 'pending',
                ]);

                foreach ($cart as $key => $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'] ?? (is_numeric($key) ? $key : null),
                        'category_id' => $item['id'] ?? (is_numeric($key) ? $key : null),
                        'product_name' => $item['name'] ?? null,
                        'variant' => $item['variant'] ?? 'Mặc định',
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                return $order;
            });

            // Xóa session giỏ hàng
            session()->forget('cart');

            // 4. Phân luồng thanh toán
            if (in_array($request->payment_method, ['momo', 'momo_atm', 'momo_cc'])) {
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'gateway' => 'momo',
                    'amount' => $order->total_price,
                    'status' => 'pending',
                ]);

                return redirect()->route('user.orders.momo.start', $order);
            }

            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'cod',
                'amount' => $order->total_price,
                'status' => 'pending',
                'message' => 'Thanh toán khi nhận hàng',
            ]);

            // --- NHÁNH COD: TẠO VẬN ĐƠN GHN NGAY LẬP TỨC ---
            $order->load('items.product');
            $ghnOrderResponse = $ghnOrders->create($order);

            if (($ghnOrderResponse['code'] ?? null) == 200 && !empty($ghnOrderResponse['data']['order_code'])) {
                $order->update([
                    'status' => 'cod_ordered',
                    'ghn_order_code' => $ghnOrderResponse['data']['order_code'],
                    'shipping_status' => 'ready_to_pick',
                ]);

                return redirect()->route('user.orders.index')
                    ->with('success', 'Đặt hàng thành công! Mã vận đơn GHN: ' . $ghnOrderResponse['data']['order_code']);
            }

            Log::error('GHN COD Order Failed: ', $ghnOrderResponse ?? []);
            $order->update(['status' => 'cod_ordered']);

            return redirect()->route('user.orders.index')
                ->with('warning', 'Đặt hàng thành công nhưng chưa thể tạo vận đơn GHN tự động.');
        } catch (\Throwable $e) {
            Log::error('Process Payment Failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', 'Thanh toán thất bại: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 4. HỦY VÀ SỬA ĐƠN HÀNG (CANCEL & EDIT ORDER)
    // ==========================================
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã bị hủy từ trước.');
        }

        if ($order->status === 'paid') {
            return back()->with('error', 'Không thể hủy đơn hàng đã thanh toán. Vui lòng liên hệ hỗ trợ.');
        }

        $order->update([
            'status' => 'cancelled',
            'shipping_status' => 'cancelled',
        ]);

        return back()->with('success', 'Đã hủy đơn hàng #' . $order->id . ' thành công!');
    }

    public function edit(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('user.orders.index')->with('error', 'Không thể chỉnh sửa đơn hàng đã bị hủy.');
        }

        if ($order->status === 'paid') {
            return redirect()->route('user.orders.index')->with('error', 'Đơn hàng đã thanh toán không thể tự chỉnh sửa.');
        }

        return view('user.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order, GHNOrderService $ghnOrders)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'cancelled' || $order->status === 'paid') {
            return redirect()->route('user.orders.index')->with('error', 'Không thể cập nhật đơn hàng này.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'regex:/^0(3|5|7|8|9)\d{8}$/'],
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:500',
        ], [
            'phone.regex' => 'Số điện thoại không hợp lệ (phải bắt đầu bằng 03, 05, 07, 08, 09 và đủ 10 chữ số).',
        ]);

        $order->update([
            'name' => $request->name,
            'fullname' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'note' => $request->note,
        ]);

        // Nếu đơn hàng chưa có mã GHN và ở trạng thái cod_ordered hoặc paid, tự động thử tạo vận đơn GHN
        if (empty($order->ghn_order_code) && in_array($order->status, ['paid', 'cod_ordered'])) {
            $order->load('items.product');
            $res = $ghnOrders->create($order, $order->status === 'paid');
            if (isset($res['code']) && $res['code'] == 200 && !empty($res['data']['order_code'])) {
                $order->update([
                    'ghn_order_code' => $res['data']['order_code'],
                    'shipping_status' => 'ready_to_pick',
                ]);
            }
        }

        return redirect()->route('user.orders.show', $order)->with('success', 'Cập nhật thông tin nhận hàng thành công!');
    }

    public function pushGhn(Order $order, GHNOrderService $ghnOrders)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->ghn_order_code) {
            return back()->with('info', 'Đơn hàng đã có mã vận đơn GHN: ' . $order->ghn_order_code);
        }

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng đã bị hủy, không thể tạo vận đơn GHN.');
        }

        $order->load('items.product');
        $isPaid = $order->status === 'paid';
        $res = $ghnOrders->create($order, $isPaid);

        if (isset($res['code']) && $res['code'] == 200 && !empty($res['data']['order_code'])) {
            $order->update([
                'ghn_order_code' => $res['data']['order_code'],
                'shipping_status' => 'ready_to_pick',
            ]);
            return back()->with('success', 'Tạo vận đơn GHN thành công! Mã vận đơn: ' . $res['data']['order_code']);
        }

        $errMsg = $res['code_message_value'] ?? $res['message'] ?? 'Tạo vận đơn GHN thất bại. Vui lòng kiểm tra địa chỉ và số điện thoại.';
        return back()->with('error', 'Tạo vận đơn GHN không thành công: ' . $errMsg);
    }
}
