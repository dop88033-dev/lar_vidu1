@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng - Admin')

@section('content')
<style>
    /* Status Tabs styling */
    .order-tabs-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 22px;
    }
    .order-tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
        color: #475569;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        text-decoration: none !important;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }
    .order-tab-pill:hover {
        background-color: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .order-tab-pill.active {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border-color: #4f46e5;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .order-tab-pill .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        background-color: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
    }
    .order-tab-pill.active .tab-count {
        background-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 22px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    .filter-card .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        height: 38px;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s ease;
    }
    .filter-card .form-control:focus {
        background-color: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    /* Modern Table Container */
    .table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03);
    }
    .table-modern {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-top: none;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        vertical-align: middle;
    }
    .table-modern tbody td {
        padding: 15px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
        font-size: 13.5px;
        transition: background-color 0.15s ease;
    }
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }
    .table-modern tbody tr:hover td {
        background-color: #fbfcfe;
    }
    .table-modern tbody tr.row-cancelled td {
        background-color: #fafbfc;
    }

    /* Column Specific Styles */
    .order-id-badge {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        color: #1e293b;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12.5px;
        border: 1px solid #e2e8f0;
        display: inline-block;
        letter-spacing: -0.2px;
    }
    .user-avatar-initial {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4338ca;
        font-weight: 700;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #c7d2fe;
    }
    .price-text-modern {
        font-weight: 700;
        color: #0f172a;
        font-size: 14.5px;
        white-space: nowrap !important;
        display: inline-block;
    }
    .price-currency {
        font-size: 11.5px;
        color: #64748b;
        font-weight: 500;
        margin-left: 2px;
    }

    /* Soft Pill Badges */
    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
        line-height: 1.2;
    }
    .badge-soft-momo {
        background-color: #fdf2f8;
        color: #be185d;
        border: 1px solid #fbcfe8;
    }
    .badge-soft-cod {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .badge-dot {
        width: 6.5px;
        height: 6.5px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .dot-success { background-color: #10b981; }
    .dot-warning { background-color: #f59e0b; }
    .dot-danger { background-color: #ef4444; }
    .dot-info { background-color: #3b82f6; }
    .dot-secondary { background-color: #94a3b8; }

    /* Shipping Badges */
    .badge-ship-pending { background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
    .badge-ship-ready { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-ship-delivering { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-ship-delivered { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .badge-ship-cancelled { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    /* GHN Chip */
    .ghn-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 11.5px;
        color: #0f172a;
        font-weight: 600;
        font-family: ui-monospace, SFMono-Regular, monospace;
        letter-spacing: 0.5px;
    }

    /* Select Status Dropdown */
    .select-status {
        height: 32px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        padding: 2px 10px;
        color: #334155;
        background-color: #ffffff;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        cursor: pointer;
    }
    .select-status:focus {
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .select-status:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
        border-color: #e2e8f0;
    }

    /* Modern Action Buttons */
    .action-group {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 7px;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        line-height: 1.3;
        white-space: nowrap;
        text-decoration: none !important;
        cursor: pointer;
    }
    .btn-action-chat {
        background-color: #eef2ff;
        color: #4f46e5;
        border-color: #c7d2fe;
    }
    .btn-action-chat:hover {
        background-color: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    }
    .btn-action-view {
        background-color: #f8fafc;
        color: #334155;
        border-color: #cbd5e1;
    }
    .btn-action-view:hover {
        background-color: #e2e8f0;
        color: #0f172a;
    }
    .btn-action-cancel {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .btn-action-cancel:hover {
        background-color: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }
    .btn-action-disabled {
        background-color: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
        opacity: 0.75;
    }
</style>

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
<div class="order-tabs-wrapper">
    @foreach($tabs as $key => $tab)
        <a href="{{ route('admin.orders.index', array_merge($filters ?? [], ['tab' => $key, 'page' => 1])) }}" 
           class="order-tab-pill {{ $activeTab === $key ? 'active' : '' }}">
            <span>{{ $tab['label'] }}</span>
            <span class="tab-count">{{ $tab['count'] }}</span>
        </a>
    @endforeach
</div>

<!-- Filters & Search Form -->
<div class="filter-card">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row align-items-center">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        
        <div class="col-md-3 mb-2">
            <input type="text" name="search" class="form-control" placeholder="Tìm tên, SĐT, mã GHN, mã đơn..." value="{{ request('search') }}">
        </div>

        <div class="col-md-2 mb-2">
            <select name="payment_status" class="form-control">
                <option value="">-- Trạng thái TT --</option>
                @foreach($paymentLabels as $pKey => $pLabel)
                    <option value="{{ $pKey }}" {{ request('payment_status') === $pKey ? 'selected' : '' }}>{{ $pLabel }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <select name="shipping_status" class="form-control">
                <option value="">-- Trạng thái VC --</option>
                @foreach($shippingLabels as $sKey => $sLabel)
                    <option value="{{ $sKey }}" {{ request('shipping_status') === $sKey ? 'selected' : '' }}>{{ $sLabel }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <select name="gateway" class="form-control">
                <option value="">-- Cổng TT --</option>
                <option value="cod" {{ request('gateway') === 'cod' ? 'selected' : '' }}>COD</option>
                <option value="momo" {{ request('gateway') === 'momo' ? 'selected' : '' }}>MoMo</option>
            </select>
        </div>

        <div class="col-md-3 mb-2 d-flex gap-2">
            <button type="submit" class="btn btn-purple btn-sm font-weight-bold flex-grow-1" style="height: 38px;">
                <i class="fas fa-filter mr-1"></i> Lọc dữ liệu
            </button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm font-weight-bold ml-2 d-inline-flex align-items-center justify-content-center" style="height: 38px; border: 1px solid #cbd5e1;">Đặt lại</a>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 100px;">Mã đơn</th>
                    <th style="min-width: 180px;">Khách hàng</th>
                    <th style="min-width: 130px;">Tổng tiền</th>
                    <th style="min-width: 140px;">Thanh toán</th>
                    <th style="min-width: 140px;">Vận chuyển</th>
                    <th style="min-width: 130px;">Mã GHN</th>
                    <th style="min-width: 160px;">Chuyển trạng thái</th>
                    <th class="text-right" style="min-width: 190px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $isDelivering = in_array($order->status, ['shipping', 'delivering']) || in_array($order->shipping_status, ['delivering', 'picked', 'transporting']);
                        $isCompleted = $order->status === 'delivered' || $order->shipping_status === 'delivered';
                        $isCancelled = $order->status === 'cancelled' || $order->shipping_status === 'cancelled';
                        $cannotCancel = $isDelivering || $isCompleted || $isCancelled;

                        // Payment dot
                        $payDot = 'dot-warning';
                        if($order->payment_status === 'paid') {
                            $payDot = 'dot-success';
                        } elseif(in_array($order->payment_status, ['failed', 'cancelled'])) {
                            $payDot = 'dot-danger';
                        }

                        // Shipping status badge class
                        $shipClass = 'badge-ship-pending';
                        if(in_array($order->shipping_status, ['ready_to_pick', 'picking', 'picked'])) {
                            $shipClass = 'badge-ship-ready';
                        } elseif(in_array($order->shipping_status, ['shipping', 'delivering', 'transporting', 'storing', 'sorting'])) {
                            $shipClass = 'badge-ship-delivering';
                        } elseif($order->shipping_status === 'delivered') {
                            $shipClass = 'badge-ship-delivered';
                        } elseif(in_array($order->shipping_status, ['cancelled', 'return', 'returned'])) {
                            $shipClass = 'badge-ship-cancelled';
                        }
                    @endphp
                    <tr class="{{ $isCancelled ? 'row-cancelled' : '' }}">
                        {{-- Mã đơn & Ngày giờ --}}
                        <td>
                            <div class="d-flex flex-column align-items-start">
                                <span class="order-id-badge">#{{ $order->id }}</span>
                                @if($order->created_at)
                                    <span class="text-muted mt-1" style="font-size: 11px;">
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Khách hàng & SĐT --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar-initial mr-2">
                                    {{ mb_strtoupper(mb_substr($order->name ?? 'K', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-weight-600 text-dark" style="font-size: 13.5px; line-height: 1.2;">
                                        {{ $order->name }}
                                    </div>
                                    <div class="text-muted mt-1 d-flex align-items-center" style="font-size: 11.5px;">
                                        <i class="fas fa-phone-alt mr-1 text-secondary" style="font-size: 9.5px;"></i>
                                        <span>{{ $order->phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Tổng tiền (Chống rớt dòng) --}}
                        <td>
                            <div class="price-text-modern">
                                {{ number_format($order->total_price, 0, ',', '.') }}<span class="price-currency">₫</span>
                            </div>
                        </td>

                        {{-- Thanh toán --}}
                        <td>
                            <div class="d-flex flex-column align-items-start">
                                @if(str_contains(strtolower($order->payment_method), 'momo') || $order->gateway === 'momo')
                                    <span class="badge-soft badge-soft-momo">
                                        <i class="fas fa-wallet" style="font-size: 9.5px;"></i> MoMo
                                    </span>
                                @else
                                    <span class="badge-soft badge-soft-cod">
                                        <i class="fas fa-money-bill-wave" style="font-size: 9.5px;"></i> COD
                                    </span>
                                @endif
                                <div class="text-muted mt-1 d-flex align-items-center" style="font-size: 11.5px; line-height: 1.2;">
                                    <span class="badge-dot {{ $payDot }} mr-1"></span>
                                    <span>{{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Vận chuyển --}}
                        <td>
                            <span class="badge-soft {{ $shipClass }}">
                                {{ $shippingLabels[$order->shipping_status] ?? $order->shipping_status }}
                            </span>
                        </td>

                        {{-- Mã GHN --}}
                        <td>
                            @if($order->ghn_order_code)
                                <span class="ghn-chip" title="Mã vận đơn Giao Hàng Nhanh">
                                    <i class="fas fa-truck text-primary"></i>
                                    <span>{{ $order->ghn_order_code }}</span>
                                </span>
                            @else
                                <span class="text-muted" style="font-size: 12px;">
                                    <i class="fas fa-minus mr-1 opacity-50"></i>Chưa có
                                </span>
                            @endif
                        </td>

                        {{-- Chuyển trạng thái --}}
                        <td>
                            <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline-block m-0">
                                @csrf
                                <select name="status" class="select-status" onchange="this.form.submit()" {{ ($isCancelled || $isCompleted) ? 'disabled title="Đơn hàng đã ' . ($isCancelled ? 'hủy' : 'thành công') . ' không thể đổi trạng thái"' : '' }}>
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
                                    <option value="packaged" {{ $order->status === 'packaged' ? 'selected' : '' }}>📦 Chờ lấy hàng</option>
                                    <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>🚚 Đang giao hàng</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Thành công</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }} {{ $isDelivering ? 'disabled' : '' }}>
                                        ❌ {{ $isDelivering ? 'Hủy (Đang giao)' : 'Hủy đơn hàng' }}
                                    </option>
                                </select>
                            </form>
                        </td>

                        {{-- Hành động --}}
                        <td class="text-right">
                            <div class="action-group">
                                {{-- Nút Chat --}}
                                <button type="button" onclick="if(typeof openAdminChatWithUserAndOrder === 'function') openAdminChatWithUserAndOrder({{ $order->user_id }}, {{ $order->id }});" class="btn-action btn-action-chat" title="Trò chuyện về đơn hàng #{{ $order->id }}">
                                    <i class="fas fa-comment-dots"></i> <span>Chat</span>
                                </button>

                                {{-- Nút Xem --}}
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-action btn-action-view" title="Xem chi tiết đơn hàng #{{ $order->id }}">
                                    <i class="fas fa-eye"></i> <span>Xem</span>
                                </a>

                                {{-- Nút Hủy / Khóa --}}
                                @if(!$cannotCancel)
                                    <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="d-inline-block m-0" onsubmit="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng #{{ $order->id }}?');">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn-action btn-action-cancel" title="Hủy đơn hàng #{{ $order->id }}">
                                            <i class="fas fa-times-circle"></i> <span>Hủy</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="btn-action btn-action-disabled" title="{{ $isCompleted ? 'Đơn hàng đã giao thành công không thể hủy' : ($isCancelled ? 'Đơn hàng đã bị hủy' : 'Đơn hàng đang giao không được phép hủy') }}">
                                        <i class="fas fa-ban"></i> <span>Khóa</span>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="h6 mb-1 font-weight-bold text-dark">Không tìm thấy đơn hàng nào</p>
                            <small class="text-muted">Hãy thử thay đổi điều kiện lọc hoặc từ khóa tìm kiếm</small>
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
