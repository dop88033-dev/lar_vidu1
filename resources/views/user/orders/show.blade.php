@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id . ' - PTShop')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white shadow-sm border rounded-lg">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-primary font-weight-bold"><i class="fas fa-home mr-1"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.orders.index') }}" class="text-primary font-weight-bold"><i class="fas fa-box mr-1"></i> Lịch sử đơn hàng</a></li>
            <li class="breadcrumb-item active font-weight-semibold" aria-current="page">Đơn hàng #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-file-invoice text-indigo mr-2" style="color: #6366f1;"></i> Chi tiết đơn hàng #{{ $order->id }}
            </h3>
            <span class="text-muted small">Ngày đặt: {{ $order->created_at ? $order->created_at->format('H:i:s d/m/Y') : '' }}</span>
        </div>
        <div class="d-flex align-items-center">
            <button type="button" onclick="if(typeof openChatWithOrder === 'function') openChatWithOrder({{ $order->id }});" class="btn btn-dark btn-sm font-weight-bold rounded-lg mr-2">
                <i class="fas fa-comments mr-1"></i> Trò chuyện về đơn hàng
            </button>
            @if($order->status !== 'paid' && $order->status !== 'cancelled')
                <a href="{{ route('user.orders.edit', $order) }}" class="btn btn-outline-warning btn-sm font-weight-bold rounded-lg mr-2">
                    <i class="fas fa-pen mr-1"></i> Sửa thông tin
                </a>
                <form action="{{ route('user.orders.cancel', $order) }}" method="POST" class="d-inline-block mr-2" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }}?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm font-weight-bold rounded-lg">
                        <i class="fas fa-times-circle mr-1"></i> Hủy đơn hàng
                    </button>
                </form>
            @endif
            <a href="{{ route('user.orders.index') }}" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-lg">
                <i class="fas fa-arrow-left mr-1"></i> Trở về danh sách
            </a>
        </div>
    </div>

    <!-- Visual Order Tracking Timeline -->
    <div class="card card-custom border-0 shadow-sm rounded-lg mb-4 p-4">
        <h6 class="font-weight-bold text-dark mb-3 text-uppercase small"><i class="fas fa-truck-loading mr-2 text-indigo" style="color: #6366f1;"></i> Theo dõi trạng thái đơn hàng</h6>
        @php
            $statuses = ['pending', 'packaged', 'shipping', 'delivered'];
            $currentIndex = array_search($order->status, $statuses);
            if ($currentIndex === false) {
                if (in_array($order->status, ['paid', 'cod_ordered'])) $currentIndex = 0;
            }
        @endphp

        @if($order->status === 'cancelled')
            <div class="p-3 bg-danger text-white rounded-lg font-weight-bold text-center">
                <i class="fas fa-times-circle mr-2"></i> ĐƠN HÀNG NÀY ĐÃ BỊ HỦY
            </div>
        @else
            <div class="row text-center position-relative">
                <div class="col-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 0 ? 'bg-indigo text-white shadow-sm' : 'bg-light text-muted' }}" style="width: 46px; height: 46px; font-size: 18px; {{ $currentIndex >= 0 ? 'background-color: #6366f1;' : '' }}">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="font-weight-bold small text-dark">1. Đang xử lý</div>
                    <small class="text-muted d-block" style="font-size: 11px;">Đã tiếp nhận đơn</small>
                </div>
                <div class="col-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 1 ? 'bg-indigo text-white shadow-sm' : 'bg-light text-muted' }}" style="width: 46px; height: 46px; font-size: 18px; {{ $currentIndex >= 1 ? 'background-color: #6366f1;' : '' }}">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="font-weight-bold small text-dark">2. Đã đóng gói</div>
                    <small class="text-muted d-block" style="font-size: 11px;">Chuẩn bị giao hàng</small>
                </div>
                <div class="col-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 2 ? 'bg-indigo text-white shadow-sm' : 'bg-light text-muted' }}" style="width: 46px; height: 46px; font-size: 18px; {{ $currentIndex >= 2 ? 'background-color: #6366f1;' : '' }}">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="font-weight-bold small text-dark">3. Đang vận chuyển</div>
                    <small class="text-muted d-block" style="font-size: 11px;">Đang giao hàng</small>
                </div>
                <div class="col-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 3 ? 'bg-success text-white shadow-sm' : 'bg-light text-muted' }}" style="width: 46px; height: 46px; font-size: 18px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="font-weight-bold small text-dark">4. Đã giao hàng</div>
                    <small class="text-muted d-block" style="font-size: 11px;">Giao hàng thành công</small>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8 mb-4">
            <!-- Order Items Table -->
            <div class="card card-custom border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-shopping-basket text-indigo mr-2" style="color: #6366f1;"></i> Danh sách sản phẩm
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Phân loại</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr class="border-bottom">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                @if(!empty($item->product->main_image))
                                                    <img src="{{ $item->product->main_image }}" class="rounded mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="rounded mr-3 bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fas fa-box"></i></div>
                                                @endif
                                                <div>
                                                    <span class="font-weight-bold text-dark d-block">{{ $item->product_name ?? $item->product->name ?? 'Sản phẩm' }}</span>
                                                    <small class="text-muted">Mã SP: #{{ $item->product_id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-light border px-2 py-1">{{ $item->variant ?? 'Mặc định' }}</span>
                                        </td>
                                        <td class="align-middle text-center font-weight-bold">{{ $item->quantity }}</td>
                                        <td class="align-middle text-right text-muted">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                        <td class="align-middle text-right font-weight-bold text-indigo" style="color: #6366f1;">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Product Reviews Section -->
            <div class="card card-custom border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-star text-warning mr-2"></i> Đánh giá & Phản hồi sản phẩm
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($order->status !== 'delivered' || $order->shipping_status !== 'delivered')
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-info-circle mr-1"></i> Đánh giá sản phẩm sẽ mở sau khi đơn hàng được giao thành công.
                        </div>
                    @else
                        @foreach($order->items as $item)
                            @php
                                $catId = $item->category_id ?? $item->product_id;
                                $existingReview = \App\Models\Review::where('user_id', Auth::id())
                                    ->where('order_id', $order->id)
                                    ->where('category_id', $catId)
                                    ->first();
                            @endphp
                            <div class="border rounded-lg p-3 mb-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        @if(!empty($item->product->main_image))
                                            <img src="{{ $item->product->main_image }}" class="rounded mr-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <strong class="text-dark">{{ $item->product_name ?? $item->product->name }}</strong>
                                    </div>
                                    @if($existingReview)
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Đã đánh giá</span>
                                    @endif
                                </div>

                                @if($existingReview)
                                    <div class="p-2 bg-white rounded border">
                                        <div class="text-warning mb-1">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="{{ $i <= $existingReview->rating ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                            <span class="font-weight-bold text-dark ml-2">({{ $existingReview->rating }}/5 sao)</span>
                                        </div>
                                        <p class="mb-0 text-muted small">{{ $existingReview->comment ?: 'Không có bình luận.' }}</p>
                                    </div>
                                @else
                                    <form action="{{ route('user.reviews.store') }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <input type="hidden" name="category_id" value="{{ $catId }}">
                                        <div class="form-row align-items-center mb-2">
                                            <div class="col-md-4 mb-2 mb-md-0">
                                                <label class="small font-weight-bold mb-1">Chọn mức đánh giá:</label>
                                                <select name="rating" class="form-control form-control-sm font-weight-bold text-warning">
                                                    <option value="5" selected>⭐⭐⭐⭐⭐ (5 sao - Rất tốt)</option>
                                                    <option value="4">⭐⭐⭐⭐ (4 sao - Tốt)</option>
                                                    <option value="3">⭐⭐⭐ (3 sao - Bình thường)</option>
                                                    <option value="2">⭐⭐ (2 sao - Tạm được)</option>
                                                    <option value="1">⭐ (1 sao - Rất tệ)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="small font-weight-bold mb-1">Bình luận & Nhận xét:</label>
                                                <input type="text" name="comment" class="form-control form-control-sm" placeholder="Nhập cảm nhận của bạn về sản phẩm...">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-purple btn-sm font-weight-bold mt-1">
                                            <i class="fas fa-paper-plane mr-1"></i> Gửi đánh giá
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Payment Transactions History Log -->
            <div class="card card-custom border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-history text-indigo mr-2" style="color: #6366f1;"></i> Nhật ký giao dịch thanh toán (Payment Transactions)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($order->paymentTransactions && $order->paymentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th>Cổng thanh toán</th>
                                        <th>Mã giao dịch MoMo/Gateway</th>
                                        <th>Số tiền</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->paymentTransactions as $trans)
                                        <tr class="border-bottom">
                                            <td class="align-middle font-weight-bold text-uppercase">
                                                @if($trans->gateway === 'momo')
                                                    <span class="badge text-white px-2 py-1" style="background-color: #a50064;">MoMo</span>
                                                @else
                                                    <span class="badge badge-secondary px-2 py-1">{{ $trans->gateway }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle small">
                                                <div class="font-weight-bold">{{ $trans->gateway_order_id ?? '-' }}</div>
                                                <small class="text-muted">TransID: {{ $trans->transaction_id ?? '-' }}</small>
                                            </td>
                                            <td class="align-middle font-weight-bold text-dark">
                                                {{ number_format($trans->amount, 0, ',', '.') }} VNĐ
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($trans->status === 'paid' || $trans->status === 'completed')
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Thành công</span>
                                                @elseif($trans->status === 'failed')
                                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Thất bại</span>
                                                @else
                                                    <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> {{ ucfirst($trans->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle small text-muted">
                                                {{ $trans->created_at ? $trans->created_at->format('H:i d/m/Y') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted small">
                            Chưa có nhật ký giao dịch thanh toán nào cho đơn hàng này.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Shipping & Customer Summary -->
            <div class="card card-custom border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-dark text-white font-weight-bold py-3">
                    <i class="fas fa-truck text-indigo mr-2" style="color: #818cf8;"></i> Thông tin giao hàng GHN
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold">Người nhận hàng:</small>
                        <strong class="text-dark">{{ $order->name }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold">Số điện thoại:</small>
                        <strong class="text-dark">{{ $order->phone }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold">Địa chỉ giao hàng:</small>
                        <span class="text-dark">{{ $order->address }}</span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold">Mã vận đơn GHN:</small>
                        @if($order->ghn_order_code)
                            <span class="badge badge-dark px-3 py-2 font-weight-bold" style="background-color: #1e293b; font-size: 13px;">
                                <i class="fas fa-shipping-fast text-warning mr-1"></i> {{ $order->ghn_order_code }}
                            </span>
                        @else
                            <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                                <span class="text-muted small italic mr-2">Chưa phát sinh mã vận đơn GHN</span>
                                @if(in_array($order->status, ['paid', 'cod_ordered']))
                                    <form action="{{ route('user.orders.push_ghn', $order) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm font-weight-bold rounded-lg text-dark" style="font-size: 12px;">
                                            <i class="fas fa-paper-plane mr-1"></i> Tạo vận đơn GHN
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block text-uppercase font-weight-bold">Phí cước giao hàng GHN:</small>
                        <span class="text-success font-weight-bold">{{ number_format($order->ghn_total_fee ?? 0, 0, ',', '.') }} VNĐ</span>
                    </div>
                </div>
            </div>

            <!-- Order Total Summary -->
            <div class="card card-custom border-0 shadow-sm rounded-lg mb-4">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">Tóm tắt thanh toán</h6>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Trạng thái đơn hàng:</span>
                        @if($order->status === 'paid')
                            <span class="badge badge-success px-2 py-1">Đã thanh toán</span>
                        @elseif($order->status === 'cod_ordered')
                            <span class="badge badge-info px-2 py-1">Đã đặt COD</span>
                        @else
                            <span class="badge badge-warning text-dark px-2 py-1">Chờ thanh toán</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="font-weight-bold text-dark">Tổng tiền:</span>
                        <span class="font-weight-bold h4 mb-0 text-purple" style="color: #6366f1;">
                            {{ number_format($order->total_price, 0, ',', '.') }} đ
                        </span>
                    </div>

                    @if($order->status !== 'paid' && $order->status !== 'cod_ordered' && $order->status !== 'cancelled')
                        <div class="mt-4 pt-3 border-top">
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/momo-logo.png') }}" alt="MoMo" style="width: 40px; height: 40px; object-fit: contain;" class="mb-2">
                                <h6 class="font-weight-bold text-dark mb-1">Thanh toán qua MoMo</h6>
                                <small class="text-muted">Chọn phương thức thanh toán bên dưới</small>
                            </div>
                            <a href="{{ route('user.orders.momo.pay', [$order, 'atm']) }}" class="btn btn-block py-3 font-weight-bold rounded-lg text-white mb-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #005baa, #0078d4); border: none; box-shadow: 0 3px 10px rgba(0, 91, 170, 0.3); transition: all 0.2s;">
                                <i class="fas fa-credit-card mr-2" style="font-size: 18px;"></i>
                                <div class="text-left">
                                    <span class="d-block" style="font-size: 14px;">Thẻ Nội Địa (ATM)</span>
                                    <small class="d-block" style="opacity: 0.8; font-size: 11px;">Napas, Vietcombank, BIDV, ...</small>
                                </div>
                            </a>
                            <a href="{{ route('user.orders.momo.pay', [$order, 'cc']) }}" class="btn btn-block py-3 font-weight-bold rounded-lg text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #a50064, #d82d8b); border: none; box-shadow: 0 3px 10px rgba(165, 0, 100, 0.3); transition: all 0.2s;">
                                <i class="fab fa-cc-visa mr-2" style="font-size: 22px;"></i>
                                <div class="text-left">
                                    <span class="d-block" style="font-size: 14px;">VISA / Master / JCB</span>
                                    <small class="d-block" style="opacity: 0.8; font-size: 11px;">Thẻ quốc tế Visa, Mastercard, JCB</small>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
