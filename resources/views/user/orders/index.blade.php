@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng - PTShop')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white shadow-sm border rounded-lg">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-primary font-weight-bold"><i class="fas fa-home mr-1"></i> Trang chủ</a></li>
            <li class="breadcrumb-item active font-weight-semibold" aria-current="page">Lịch sử đơn hàng</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-box text-indigo mr-2" style="color: #6366f1;"></i> Lịch sử đơn hàng của bạn
            </h3>
            <p class="text-muted small mb-0">Theo dõi trạng thái giao hàng GHN & Nhật ký thanh toán MoMo / COD</p>
        </div>
        <a href="{{ route('welcome') }}" class="btn btn-outline-purple btn-sm font-weight-bold rounded-lg">
            <i class="fas fa-plus mr-1"></i> Đặt mua thêm
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="card card-custom border-0 shadow-sm rounded-lg py-5 text-center">
            <div class="card-body">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 90px; height: 90px;">
                    <i class="fas fa-box-open fa-3x text-secondary opacity-50"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-2">Bạn chưa có đơn hàng nào</h5>
                <p class="text-muted small mb-4">Hãy khám phá danh mục sản phẩm và tiến hành đặt hàng ngay hôm nay!</p>
                <a href="{{ route('welcome') }}" class="btn btn-purple px-4 py-2 font-weight-bold rounded-lg">
                    <i class="fas fa-shopping-bag mr-2"></i> Khám phá sản phẩm
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="card card-custom border-0 shadow-sm rounded-lg mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <span class="badge badge-purple px-3 py-2 mr-3 font-weight-bold" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; font-size: 13px;">
                                Đơn hàng #{{ $order->id }}
                            </span>
                            <span class="text-muted small">
                                <i class="far fa-clock mr-1"></i> {{ $order->created_at ? $order->created_at->format('H:i - d/m/Y') : '' }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <!-- Status Badge -->
                            @if($order->status === 'delivered')
                                <span class="badge badge-success px-3 py-1 mr-2" style="font-size: 12px;"><i class="fas fa-check-circle mr-1"></i> Đã giao hàng</span>
                            @elseif($order->status === 'shipping')
                                <span class="badge badge-primary px-3 py-1 mr-2" style="font-size: 12px;"><i class="fas fa-truck mr-1"></i> Đang vận chuyển</span>
                            @elseif($order->status === 'packaged')
                                <span class="badge badge-info px-3 py-1 mr-2" style="font-size: 12px;"><i class="fas fa-box-open mr-1"></i> Đã đóng gói</span>
                            @elseif($order->status === 'paid')
                                <span class="badge badge-success px-3 py-1 mr-2" style="font-size: 12px;"><i class="fas fa-check-circle mr-1"></i> Đã thanh toán</span>
                            @elseif($order->status === 'cod_ordered')
                                <span class="badge badge-info px-3 py-1 mr-2" style="font-size: 12px;"><i class="fas fa-box mr-1"></i> Đã đặt (COD)</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge badge-danger px-3 py-1 mr-2" style="font-size: 12px;"><i class="fas fa-times-circle mr-1"></i> Đã hủy</span>
                            @else
                                <span class="badge badge-warning text-dark px-3 py-1 mr-2" style="font-size: 12px;"><i class="fas fa-clock mr-1"></i> Đang xử lý</span>
                            @endif

                            <!-- GHN Order Code Badge -->
                            @if($order->ghn_order_code)
                                <span class="badge badge-dark px-3 py-1" style="font-size: 12px; background-color: #1e293b;">
                                    <i class="fas fa-shipping-fast text-warning mr-1"></i> GHN: {{ $order->ghn_order_code }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <div class="mb-2">
                                    <strong class="text-dark"><i class="fas fa-user text-muted mr-2"></i>Người nhận:</strong> {{ $order->name }} ({{ $order->phone }})
                                </div>
                                <div class="mb-2 text-muted small">
                                    <strong class="text-dark"><i class="fas fa-map-marker-alt text-muted mr-2"></i>Địa chỉ:</strong> {{ $order->address }}
                                </div>
                                <div class="text-muted small">
                                    <strong class="text-dark"><i class="fas fa-cubes text-muted mr-2"></i>Sản phẩm:</strong> 
                                    @if($order->items->count() > 0)
                                        {{ $order->items->pluck('product_name')->filter()->implode(', ') ?: 'Chi tiết đơn hàng' }}
                                    @else
                                        {{ $order->items->count() }} mặt hàng
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-5 text-md-right border-left-md">
                                <div class="mb-1 text-muted small">Tổng thanh toán:</div>
                                <div class="h4 font-weight-bold text-purple mb-3" style="color: #6366f1;">
                                    {{ number_format($order->total_price, 0, ',', '.') }} VNĐ
                                </div>
                                <div class="d-flex flex-wrap justify-content-md-end gap-2 align-items-center">
                                    <a href="{{ route('user.orders.show', $order) }}" class="btn btn-outline-purple btn-sm font-weight-bold rounded-lg mr-1 mb-1">
                                        <i class="fas fa-eye mr-1"></i> Xem chi tiết
                                    </a>

                                    @if($order->status !== 'paid' && $order->status !== 'cancelled')
                                        <a href="{{ route('user.orders.edit', $order) }}" class="btn btn-outline-warning btn-sm font-weight-bold rounded-lg mr-1 mb-1" style="font-size: 12px;">
                                            <i class="fas fa-pen mr-1"></i> Sửa
                                        </a>

                                        <form action="{{ route('user.orders.cancel', $order) }}" method="POST" class="d-inline-block mb-1 mr-1" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm font-weight-bold rounded-lg" style="font-size: 12px;">
                                                <i class="fas fa-times-circle mr-1"></i> Hủy đơn
                                            </button>
                                        </form>
                                    @endif

                                     @if(empty($order->ghn_order_code) && in_array($order->status, ['paid', 'cod_ordered']))
                                         <form action="{{ route('user.orders.push_ghn', $order) }}" method="POST" class="d-inline-block mb-1 mr-1">
                                             @csrf
                                             <button type="submit" class="btn btn-warning btn-sm font-weight-bold rounded-lg text-dark" style="font-size: 12px;">
                                                 <i class="fas fa-paper-plane mr-1"></i> Tạo vận đơn GHN
                                             </button>
                                         </form>
                                     @endif

                                    @if($order->status !== 'paid' && $order->status !== 'cod_ordered' && $order->status !== 'cancelled')
                                        <a href="{{ route('user.orders.momo.pay', [$order, 'atm']) }}" class="btn btn-sm font-weight-bold rounded-lg text-white mr-1 mb-1" style="background: linear-gradient(135deg, #005baa, #0078d4); border: none; font-size: 12px; padding: 6px 12px; box-shadow: 0 2px 6px rgba(0, 91, 170, 0.25);">
                                            <i class="fas fa-credit-card mr-1"></i> Thẻ Nội Địa
                                        </a>
                                        <a href="{{ route('user.orders.momo.pay', [$order, 'cc']) }}" class="btn btn-sm font-weight-bold rounded-lg text-white mb-1" style="background: linear-gradient(135deg, #a50064, #d82d8b); border: none; font-size: 12px; padding: 6px 12px; box-shadow: 0 2px 6px rgba(165, 0, 100, 0.25);">
                                            <i class="fab fa-cc-visa mr-1"></i> VISA / Master
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<style>
.momo-pay-btn:hover {
    box-shadow: 0 4px 14px rgba(165, 0, 100, 0.45) !important;
    transform: translateY(-1px);
}
</style>
@endsection
