@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->id . ' - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-lg mb-2">
            <i class="fas fa-arrow-left mr-1"></i> Trở về Danh sách Đơn hàng
        </a>
        <h3 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-file-invoice text-primary mr-2"></i>Chi Tiết Đơn Hàng #{{ $order->id }}
        </h3>
    </div>
    <div class="d-flex align-items-center gap-2">
        @php
            $isCompleted = $order->status === 'delivered' || $order->shipping_status === 'delivered';
            $isCancelled = $order->status === 'cancelled' || $order->shipping_status === 'cancelled';
        @endphp
        <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-flex align-items-center">
            @csrf
            <label class="font-weight-bold text-dark mb-0 mr-2 small text-uppercase">Trạng thái:</label>
            <select name="status" class="form-control form-control-sm font-weight-bold mr-2" style="width: auto;" onchange="this.form.submit()" {{ ($isCancelled || $isCompleted) ? 'disabled title="Đơn hàng đã ' . ($isCancelled ? 'hủy' : 'thành công') . ' không thể đổi trạng thái"' : '' }}>
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Đang xử lý</option>
                <option value="packaged" {{ $order->status === 'packaged' ? 'selected' : '' }}>📦 Đã đóng gói</option>
                <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>🚚 Đang vận chuyển</option>
                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Đã giao hàng</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>💳 Đã thanh toán</option>
                <option value="cod_ordered" {{ $order->status === 'cod_ordered' ? 'selected' : '' }}>🚛 Đã đặt COD</option>
            </select>
        </form>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Order Status Progress Bar -->
<div class="card card-custom border-0 shadow-sm mb-4 p-4">
    <h6 class="font-weight-bold text-dark mb-3 text-uppercase small"><i class="fas fa-tasks mr-2 text-primary"></i>Tiến trình xử lý đơn hàng</h6>
    <div class="row text-center position-relative">
        @php
            $statuses = ['pending', 'packaged', 'shipping', 'delivered'];
            $currentIndex = array_search($order->status, $statuses);
            if ($currentIndex === false) {
                if (in_array($order->status, ['paid', 'cod_ordered'])) $currentIndex = 0;
            }
        @endphp

        @if($order->status === 'cancelled')
            <div class="col-12 py-3 bg-danger text-white rounded font-weight-bold">
                <i class="fas fa-times-circle mr-2"></i> ĐƠN HÀNG NÀY ĐÃ BỊ HỦY
            </div>
        @else
            <div class="col-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 0 ? 'bg-primary text-white' : 'bg-light text-muted' }}" style="width: 44px; height: 44px; font-size: 18px;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="font-weight-bold small text-dark">1. Đang xử lý</div>
            </div>
            <div class="col-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 1 ? 'bg-primary text-white' : 'bg-light text-muted' }}" style="width: 44px; height: 44px; font-size: 18px;">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="font-weight-bold small text-dark">2. Đã đóng gói</div>
            </div>
            <div class="col-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 2 ? 'bg-primary text-white' : 'bg-light text-muted' }}" style="width: 44px; height: 44px; font-size: 18px;">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="font-weight-bold small text-dark">3. Đang vận chuyển</div>
            </div>
            <div class="col-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 {{ $currentIndex >= 3 ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 44px; height: 44px; font-size: 18px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="font-weight-bold small text-dark">4. Đã giao hàng</div>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Products & Reviews List -->
    <div class="col-lg-8 mb-4">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom font-weight-bold text-dark">
                <i class="fas fa-shopping-bag text-primary mr-2"></i> Danh sách sản phẩm trong đơn
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
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(!empty($item->product->main_image))
                                                <img src="{{ $item->product->main_image }}" class="rounded mr-3" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="rounded mr-3 bg-light text-muted d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;"><i class="fas fa-box"></i></div>
                                            @endif
                                            <div>
                                                <span class="font-weight-bold text-dark d-block">{{ $item->product_name ?? $item->product->name ?? 'Sản phẩm' }}</span>
                                                <small class="text-muted">Mã SP: #{{ $item->product_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-light border px-2 py-1">{{ $item->variant ?? 'Mặc định' }}</span></td>
                                    <td class="text-center font-weight-bold">{{ $item->quantity }}</td>
                                    <td class="text-right text-muted">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                    <td class="text-right font-weight-bold text-purple" style="color: #6366f1;">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Transactions -->
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom font-weight-bold text-dark">
                <i class="fas fa-history text-primary mr-2"></i> Lịch sử giao dịch thanh toán
            </div>
            <div class="card-body p-0">
                @if($order->paymentTransactions && $order->paymentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th>Cổng</th>
                                    <th>Mã GD MoMo/Gate</th>
                                    <th>Số tiền</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->paymentTransactions as $trans)
                                    <tr class="border-bottom">
                                        <td class="font-weight-bold text-uppercase">
                                            @if($trans->gateway === 'momo')
                                                <span class="badge text-white px-2 py-1" style="background-color: #a50064;">MoMo</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1">{{ $trans->gateway }}</span>
                                            @endif
                                        </td>
                                        <td class="small">
                                            <div>{{ $trans->gateway_order_id ?? '-' }}</div>
                                            <small class="text-muted">TransID: {{ $trans->transaction_id ?? '-' }}</small>
                                        </td>
                                        <td class="font-weight-bold text-dark">{{ number_format($trans->amount, 0, ',', '.') }} đ</td>
                                        <td class="text-center">
                                            @if(in_array($trans->status, ['paid', 'completed']))
                                                <span class="badge badge-success px-2 py-1">Thành công</span>
                                            @elseif($trans->status === 'failed')
                                                <span class="badge badge-danger px-2 py-1">Thất bại</span>
                                            @else
                                                <span class="badge badge-warning text-dark px-2 py-1">{{ ucfirst($trans->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">{{ $trans->created_at ? $trans->created_at->format('H:i d/m/Y') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-3 text-center text-muted small">Chưa ghi nhận lịch sử giao dịch.</div>
                @endif
            </div>
        </div>

        <!-- Customer Reviews for this order -->
        @if($order->reviews && $order->reviews->count() > 0)
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom font-weight-bold text-dark">
                    <i class="fas fa-star text-warning mr-2"></i> Đánh giá từ khách hàng cho đơn này
                </div>
                <div class="card-body">
                    @foreach($order->reviews as $rev)
                        <div class="p-3 bg-light rounded mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <strong class="text-dark">{{ $rev->user->name ?? 'Khách hàng' }}</strong>
                                    <span class="text-warning ml-2">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="{{ $i <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </span>
                                </div>
                                <small class="text-muted">{{ $rev->created_at ? $rev->created_at->format('H:i d/m/Y') : '' }}</small>
                            </div>
                            <p class="mb-0 text-muted small">{{ $rev->comment ?? 'Không có nhận xét chi tiết.' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar Summary -->
    <div class="col-lg-4">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white font-weight-bold py-3">
                <i class="fas fa-user-circle mr-2 text-primary"></i> Thông tin khách hàng & Giao hàng
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase font-weight-bold">Tên khách hàng:</small>
                    <strong class="text-dark h6 mb-0">{{ $order->name }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase font-weight-bold">Số điện thoại:</small>
                    <strong class="text-dark">{{ $order->phone }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase font-weight-bold">Địa chỉ nhận hàng:</small>
                    <span class="text-dark">{{ $order->address }}</span>
                </div>
                @if($order->note)
                    <div class="mb-3">
                        <small class="text-muted d-block text-uppercase font-weight-bold">Ghi chú từ khách:</small>
                        <em class="text-dark small">{{ $order->note }}</em>
                    </div>
                @endif
                <hr>
                <div class="mb-3">
                    <small class="text-muted d-block text-uppercase font-weight-bold">Mã vận đơn GHN:</small>
                    @if($order->ghn_order_code)
                        <span class="badge badge-dark px-3 py-2 font-weight-bold" style="background: #1e293b;">
                            <i class="fas fa-shipping-fast text-warning mr-1"></i> {{ $order->ghn_order_code }}
                        </span>
                    @else
                        <span class="text-muted small italic">Chưa phát sinh vận đơn</span>
                    @endif
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block text-uppercase font-weight-bold">Phí giao hàng GHN:</small>
                    <span class="text-success font-weight-bold">{{ number_format($order->ghn_total_fee ?? 0, 0, ',', '.') }} đ</span>
                </div>
            </div>
        </div>

        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">Tóm tắt thanh toán</h6>
                <div class="d-flex justify-content-between align-items-center pt-2">
                    <span class="font-weight-bold text-dark">Tổng tiền đơn hàng:</span>
                    <span class="font-weight-bold h4 mb-0 text-purple" style="color: #6366f1;">
                        {{ number_format($order->total_price, 0, ',', '.') }} đ
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
