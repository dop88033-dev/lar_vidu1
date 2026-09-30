@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="font-weight-bold text-dark mb-1">
            <i class="fas fa-shopping-cart text-primary mr-2"></i>Quản Lý Đơn Hàng
        </h3>
        <p class="text-muted small mb-0">Theo dõi, lọc theo trạng thái vận chuyển và cập nhật trạng thái đơn hàng</p>
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Status Tabs -->
<div class="mb-4">
    <div class="d-flex flex-wrap gap-2">
        @foreach($tabs as $key => $tab)
            <a href="{{ route('admin.orders.index', array_merge($filters ?? [], ['tab' => $key, 'page' => 1])) }}" 
               class="btn btn-sm {{ $activeTab === $key ? 'btn-dark font-weight-bold' : 'btn-outline-secondary' }} rounded-pill px-3 mr-2 mb-2">
                {{ $tab['label'] }} <span class="badge badge-light ml-1">{{ $tab['count'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<!-- Filters & Search Form -->
<div class="card card-custom mb-4 p-3 border-0 shadow-sm">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row align-items-center">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        
        <div class="col-md-3 mb-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm tên, SĐT, mã GHN, mã đơn..." value="{{ request('search') }}">
        </div>

        <div class="col-md-2 mb-2">
            <select name="payment_status" class="form-control form-control-sm">
                <option value="">-- Trạng thái TT --</option>
                @foreach($paymentLabels as $pKey => $pLabel)
                    <option value="{{ $pKey }}" {{ request('payment_status') === $pKey ? 'selected' : '' }}>{{ $pLabel }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <select name="shipping_status" class="form-control form-control-sm">
                <option value="">-- Trạng thái VC --</option>
                @foreach($shippingLabels as $sKey => $sLabel)
                    <option value="{{ $sKey }}" {{ request('shipping_status') === $sKey ? 'selected' : '' }}>{{ $sLabel }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <select name="gateway" class="form-control form-control-sm">
                <option value="">-- Cổng TT --</option>
                <option value="cod" {{ request('gateway') === 'cod' ? 'selected' : '' }}>COD</option>
                <option value="momo" {{ request('gateway') === 'momo' ? 'selected' : '' }}>MoMo</option>
            </select>
        </div>

        <div class="col-md-3 mb-2 d-flex gap-2">
            <button type="submit" class="btn btn-purple btn-sm font-weight-bold flex-grow-1">
                <i class="fas fa-filter mr-1"></i> Lọc dữ liệu
            </button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm font-weight-bold ml-2">Đặt lại</a>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="card card-custom border-0 shadow-sm p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Vận chuyển</th>
                    <th>Mã GHN</th>
                    <th>Chuyển trạng thái</th>
                    <th class="text-right">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $isDelivering = in_array($order->status, ['shipping', 'delivering']) || in_array($order->shipping_status, ['delivering', 'picked', 'transporting']);
                    @endphp
                    <tr>
                        <td class="font-weight-bold text-dark">
                            #{{ $order->id }}
                        </td>
                        <td>
                            <div class="font-weight-bold text-dark">{{ $order->name }}</div>
                            <small class="text-muted d-block"><i class="fas fa-phone mr-1"></i>{{ $order->phone }}</small>
                        </td>
                        <td class="font-weight-bold text-purple" style="color: #6366f1;">
                            {{ number_format($order->total_price, 0, ',', '.') }} đ
                        </td>
                        <td>
                            @if(str_contains(strtolower($order->payment_method), 'momo') || $order->gateway === 'momo')
                                <span class="badge text-white px-2 py-1" style="background-color: #a50064;">MoMo</span>
                            @else
                                <span class="badge badge-secondary px-2 py-1">COD</span>
                            @endif
                            <small class="d-block text-muted">{{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</small>
                        </td>
                        <td>
                            <span class="badge badge-info px-2 py-1">
                                {{ $shippingLabels[$order->shipping_status] ?? $order->shipping_status }}
                            </span>
                        </td>
                        <td>
                            @if($order->ghn_order_code)
                                <span class="badge badge-dark px-2 py-1 font-weight-bold" style="background: #1e293b;">
                                    <i class="fas fa-shipping-fast text-warning mr-1"></i> {{ $order->ghn_order_code }}
                                </span>
                            @else
                                <span class="text-muted small italic">Chưa có</span>
                            @endif
                        </td>
                        <td>
                            {{-- Form chuyển trạng thái / Hủy --}}
                            <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline-block">
                                @csrf
                                <select name="status" class="form-control form-control-sm font-weight-bold border-0 shadow-sm rounded px-2" style="font-size: 12px; cursor: pointer; width: auto;" onchange="this.form.submit()" {{ $order->status === 'cancelled' ? 'disabled title="Đơn hàng đã hủy không thể đổi trạng thái"' : '' }}>
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
                                    <option value="packaged" {{ $order->status === 'packaged' ? 'selected' : '' }}>📦 Chờ lấy / Đóng gói</option>
                                    <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>🚚 Đang giao hàng</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Thành công</option>
                                    
                                    {{-- Nếu đang giao -> Vẫn hiển thị nhưng nếu chọn hủy sẽ bị controller chặn và cảnh báo --}}
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }} {{ $isDelivering ? 'disabled' : '' }}>
                                        ❌ {{ $isDelivering ? 'Hủy (Không khả thi do đang giao)' : 'Hủy đơn hàng' }}
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-purple font-weight-bold mr-1">
                                <i class="fas fa-eye mr-1"></i> Xem
                            </a>
                            @if(!$isDelivering && $order->status !== 'cancelled')
                                <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng #{{ $order->id }}?');">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold mr-1">
                                        <i class="fas fa-ban"></i> Hủy
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-light text-muted font-weight-bold mr-1" disabled title="Đơn hàng đang giao không được phép hủy">
                                    <i class="fas fa-ban"></i> Không thể hủy
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="h6 mb-0 font-weight-bold">Không tìm thấy đơn hàng nào</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
