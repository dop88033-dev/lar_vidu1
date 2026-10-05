<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private const TABS = [
        'all' => ['label' => 'Tất cả', 'color' => 'blue', 'statuses' => []],
        'pending' => ['label' => 'Chờ xử lý', 'color' => 'slate', 'statuses' => ['pending', 'not_shipped', 'processing']],
        'ready' => ['label' => 'Chờ lấy hàng', 'color' => 'cyan', 'statuses' => ['ready_to_pick']],
        'picking' => ['label' => 'Đang lấy hàng', 'color' => 'cyan', 'statuses' => ['picking']],
        'delivering' => ['label' => 'Đang giao', 'color' => 'amber', 'statuses' => ['delivering', 'picked', 'storing', 'transporting', 'sorting']],
        'delivered' => ['label' => 'Thành công', 'color' => 'green', 'statuses' => ['delivered']],
        'return' => ['label' => 'Hoàn hàng', 'color' => 'orange', 'statuses' => ['return', 'returning', 'returned', 'return_transporting', 'return_sorting']],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'red', 'statuses' => ['cancelled']],
    ];

    /**
     * Hiển thị danh sách đơn hàng cho Admin
     */
    public function index(Request $request)
    {
        $paymentLabels = [
            'pending' => 'Chờ thanh toán', 'initiated' => 'Đang chờ MoMo', 'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại', 'cancelled' => 'Đã hủy',
            'refund_pending' => 'Chờ hoàn tiền', 'refunded' => 'Đã hoàn tiền',
        ];

        $shippingLabels = [
            'pending' => 'Chờ tạo vận đơn', 'not_shipped' => 'Chưa giao hàng', 'processing' => 'Đang tạo vận đơn',
            'ready_to_pick' => 'Chờ lấy hàng', 'picking' => 'Đang lấy hàng', 'picked' => 'Đã lấy hàng',
            'storing' => 'Đang lưu kho', 'transporting' => 'Đang trung chuyển', 'sorting' => 'Đang phân loại',
            'delivering' => 'Đang giao hàng', 'delivered' => 'Giao hàng thành công',
            'return' => 'Chờ hoàn hàng', 'returning' => 'Đang hoàn hàng', 'returned' => 'Đã hoàn hàng',
            'return_transporting' => 'Đang chuyển hoàn', 'return_sorting' => 'Đang phân loại hoàn', 'cancelled' => 'Đã hủy',
        ];

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'paid_momo', 'cod_ordered', 'cod_paid', 'cancelled', 'packaged', 'shipping', 'delivered'])],
            'payment_status' => ['nullable', Rule::in(array_keys($paymentLabels))],
            'shipping_status' => ['nullable', Rule::in(array_keys($shippingLabels))],
            'gateway' => ['nullable', Rule::in(['cod', 'momo', 'unknown'])],
            'tab' => ['nullable', Rule::in(array_keys(self::TABS))],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])],
            'per_page' => ['nullable', 'integer', Rule::in([25, 50, 100])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'amount_desc', 'amount_asc'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'date_to.after_or_equal' => 'Ngày kết thúc phải từ ngày bắt đầu trở đi.',
            '*.date_format' => 'Ngày lọc không hợp lệ.',
            '*.in' => 'Giá trị bộ lọc không hợp lệ.',
        ]);

        $paymentId = DB::table('payment_transactions')->select('id')->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')->limit(1);

        $source = DB::table('orders')->leftJoin('payment_transactions as payment', function ($join) use ($paymentId) {
            $join->on('payment.order_id', '=', 'orders.id')->where('payment.id', '=', $paymentId);
        })->select('orders.*')
            ->selectRaw("COALESCE(payment.gateway, CASE WHEN orders.status IN ('cod_ordered', 'cod_paid') THEN 'cod' WHEN orders.status IN ('paid', 'paid_momo') THEN 'momo' ELSE 'unknown' END) as gateway")
            ->selectRaw("COALESCE(payment.status, CASE WHEN orders.status = 'cod_ordered' THEN 'pending' WHEN orders.status IN ('cod_paid', 'paid_momo') THEN 'paid' ELSE orders.status END) as payment_status");

        $query = Order::query()->fromSub($source, 'orders');

        foreach (['status', 'payment_status', 'gateway'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $filters[$field] ?? null);
            }
        }

        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('ghn_order_code', 'like', '%' . $search . '%')
                    ->orWhereHas('items.product', fn ($products) => $products->where('name', 'like', '%' . $search . '%'));

                if (preg_match('/^(?:#|DH)?0*(\d+)$/i', $search, $matches)) {
                    $q->orWhere('orders.id', $matches[1]);
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
        }

        // Số lượng trên tab theo bộ lọc chung
        $shippingCounts = (clone $query)->select('shipping_status')->selectRaw('COUNT(*) as total')
            ->groupBy('shipping_status')->pluck('total', 'shipping_status');

        $tabs = collect(self::TABS)->map(function ($tab, $key) use ($shippingCounts) {
            $tab['count'] = $key === 'all' ? $shippingCounts->sum()
                : collect($tab['statuses'])->sum(fn ($status) => $shippingCounts->get($status, 0));
            return $tab;
        });

        $activeTab = $filters['tab'] ?? 'all';
        if ($activeTab !== 'all') {
            $query->whereIn('shipping_status', self::TABS[$activeTab]['statuses']);
        }

        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $filters['shipping_status']);
        }

        [$column, $direction] = match ($filters['sort'] ?? 'newest') {
            'oldest' => ['created_at', 'asc'],
            'amount_desc' => ['total_price', 'desc'],
            'amount_asc' => ['total_price', 'asc'],
            default => ['created_at', 'desc'],
        };

        $orders = $query->with('items.product')->orderBy($column, $direction)->orderBy('id', $direction)
            ->paginate((int) ($filters['per_page'] ?? 25))->withQueryString();

        return view('admin.orders.index', compact('orders', 'filters', 'tabs', 'activeTab', 'paymentLabels', 'shippingLabels'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'paymentTransactions' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (Xác nhận, chuyển trạng thái hoặc Hủy)
     * Quy tắc: Nếu đơn hàng ở trạng thái đang giao (shipping / delivering) -> KHÔNG cho Hủy
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $newStatus = $request->status;

        // Nếu đơn hàng đã bị HỦY, không cho phép đổi sang bất kỳ trạng thái nào khác
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã bị HỦY, không thể chuyển sang trạng thái khác!');
        }

        // Nếu đơn hàng đã THÀNH CÔNG (đã giao), không cho phép đổi sang bất kỳ trạng thái nào khác
        if ($order->status === 'delivered' || $order->shipping_status === 'delivered') {
            return back()->with('error', 'Đơn hàng đã giao THÀNH CÔNG, không thể chuyển sang trạng thái khác!');
        }

        // Nếu chuyển sang trạng thái HỦY, kiểm tra nếu đơn hàng đang giao thì không cho phép
        if ($newStatus === 'cancelled') {
            if (in_array($order->status, ['shipping', 'delivering']) || in_array($order->shipping_status, ['delivering', 'picked', 'transporting'])) {
                return back()->with('error', 'Đơn hàng đang trong quá trình vận chuyển / giao hàng, KHÔNG THỂ HỦY!');
            }
        }

        $shippingStatus = $order->shipping_status;
        switch ($newStatus) {
            case 'pending':
                $shippingStatus = 'pending';
                break;
            case 'packaged':
                $shippingStatus = 'ready_to_pick';
                break;
            case 'shipping':
                $shippingStatus = 'delivering';
                break;
            case 'delivered':
                $shippingStatus = 'delivered';
                break;
            case 'cancelled':
                $shippingStatus = 'cancelled';
                break;
        }

        $order->update([
            'status' => $newStatus,
            'shipping_status' => $shippingStatus,
        ]);

        $successMessage = 'Đã cập nhật trạng thái đơn hàng #' . $order->id . ' thành công!';
        $previousUrl = url()->previous();
        $previousPath = parse_url($previousUrl, PHP_URL_PATH);
        $previousQuery = [];
        parse_str((string) parse_url($previousUrl, PHP_URL_QUERY), $previousQuery);

        $isFilteredByStatus = ($previousQuery['tab'] ?? 'all') !== 'all'
            || isset($previousQuery['status'])
            || isset($previousQuery['shipping_status']);

        if ($previousPath === route('admin.orders.index', [], false) && $isFilteredByStatus) {
            unset($previousQuery['status'], $previousQuery['shipping_status']);
            $previousQuery['tab'] = 'all';
            $previousQuery['page'] = 1;

            return redirect()->route('admin.orders.index', $previousQuery)->with('success', $successMessage);
        }

        return back()->with('success', $successMessage);
    }

    /**
     * Xóa đơn hàng
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Đã xóa đơn hàng thành công!');
    }
}
