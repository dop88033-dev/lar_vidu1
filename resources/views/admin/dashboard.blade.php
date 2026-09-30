@extends('layouts.admin')

@section('title', 'Admin Dashboard - Báo Cáo & Thống Kê Doanh Số')

@section('content')
<!-- Header Banner -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="font-weight-bold text-dark mb-1">
            <i class="fas fa-chart-line text-primary mr-2"></i>Báo Cáo & Thống Kê Doanh Số
        </h3>
        <p class="text-muted small mb-0">Tổng quan doanh thu, tình trạng đơn hàng và danh sách sản phẩm bán chạy nhất</p>
    </div>
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-purple font-weight-bold shadow-sm">
            <i class="fas fa-shopping-cart mr-1"></i> Quản lý đơn hàng
        </a>
    </div>
</div>

<!-- Revenue & Order Stats Cards -->
<div class="row">
    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-custom bg-white d-flex align-items-center justify-content-between p-4 border-0 shadow-sm rounded-lg h-100" style="border-left: 4px solid #6366f1 !important;">
            <div>
                <span class="text-uppercase small font-weight-bold text-muted d-block mb-1">Tổng doanh thu</span>
                <h4 class="font-weight-bold text-dark mb-0" style="color: #4f46e5;">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }} đ</h4>
            </div>
            <div class="rounded-circle p-3 flex-shrink-0 ml-2" style="background-color: #e0e7ff; color: #4f46e5; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-custom bg-white d-flex align-items-center justify-content-between p-4 border-0 shadow-sm rounded-lg h-100" style="border-left: 4px solid #10b981 !important;">
            <div>
                <span class="text-uppercase small font-weight-bold text-muted d-block mb-1">Doanh thu tháng <span class="d-inline-block">{{ date('m/Y') }}</span></span>
                <h4 class="font-weight-bold text-dark mb-0" style="color: #059669;">{{ number_format($monthRevenue ?? 0, 0, ',', '.') }} đ</h4>
            </div>
            <div class="rounded-circle p-3 flex-shrink-0 ml-2" style="background-color: #d1fae5; color: #059669; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>

    <!-- Today Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-custom bg-white d-flex align-items-center justify-content-between p-4 border-0 shadow-sm rounded-lg h-100" style="border-left: 4px solid #f59e0b !important;">
            <div>
                <span class="text-uppercase small font-weight-bold text-muted d-block mb-1">Doanh thu hôm nay</span>
                <h4 class="font-weight-bold text-dark mb-0" style="color: #d97706;">{{ number_format($todayRevenue ?? 0, 0, ',', '.') }} đ</h4>
            </div>
            <div class="rounded-circle p-3 flex-shrink-0 ml-2" style="background-color: #fef3c7; color: #d97706; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card-custom bg-white d-flex align-items-center justify-content-between p-4 border-0 shadow-sm rounded-lg h-100" style="border-left: 4px solid #3b82f6 !important;">
            <div>
                <span class="text-uppercase small font-weight-bold text-muted d-block mb-1">Tổng đơn hàng</span>
                <h4 class="font-weight-bold text-dark mb-0" style="color: #2563eb;">{{ $totalOrders ?? 0 }} đơn</h4>
            </div>
            <div class="rounded-circle p-3 flex-shrink-0 ml-2" style="background-color: #dbeafe; color: #2563eb; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Breakdown Pills -->
<div class="card card-custom border-0 shadow-sm mb-4 p-4">
    <h6 class="font-weight-bold text-dark mb-3 text-uppercase small"><i class="fas fa-tasks mr-2 text-primary"></i>Phân bổ trạng thái đơn hàng</h6>
    <div class="row text-center">
        <div class="col-md-2 col-6 mb-3 mb-md-0">
            <div class="p-3 bg-light rounded-lg">
                <span class="badge badge-warning px-2 py-1 mb-2">Đang xử lý</span>
                <h4 class="font-weight-bold text-dark mb-0">{{ $orderCounts['pending'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3 mb-md-0">
            <div class="p-3 bg-light rounded-lg">
                <span class="badge badge-info px-2 py-1 mb-2">Đã đóng gói</span>
                <h4 class="font-weight-bold text-dark mb-0">{{ $orderCounts['packaged'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3 mb-md-0">
            <div class="p-3 bg-light rounded-lg">
                <span class="badge badge-primary px-2 py-1 mb-2">Đang vận chuyển</span>
                <h4 class="font-weight-bold text-dark mb-0">{{ $orderCounts['shipping'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3 mb-md-0">
            <div class="p-3 bg-light rounded-lg">
                <span class="badge badge-success px-2 py-1 mb-2">Đã giao hàng</span>
                <h4 class="font-weight-bold text-dark mb-0">{{ $orderCounts['delivered'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-md-2 col-12">
            <div class="p-3 bg-light rounded-lg">
                <span class="badge badge-danger px-2 py-1 mb-2">Đã hủy</span>
                <h4 class="font-weight-bold text-dark mb-0">{{ $orderCounts['cancelled'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart & Best-Selling Products -->
<div class="row">
    <!-- Revenue Chart -->
    <div class="col-lg-7 mb-4">
        <div class="card card-custom border-0 shadow-sm h-100 p-4">
            <h5 class="font-weight-bold text-dark mb-3">
                <i class="fas fa-chart-bar text-primary mr-2"></i>Biểu Đồ Doanh Thu 6 Tháng Gần Đây
            </h5>
            <div style="height: 320px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-lg-5 mb-4">
        <div class="card card-custom border-0 shadow-sm h-100 p-0 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom font-weight-bold text-dark d-flex justify-content-between align-items-center">
                <span><i class="fas fa-fire text-danger mr-2"></i>Sản Phẩm Bán Chạy Nhất</span>
                <span class="badge badge-danger badge-pill">Top 5</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light small text-uppercase text-muted">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đã bán</th>
                                <th class="text-right">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $top)
                                <tr class="border-bottom">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(!empty($top->main_image))
                                                <img src="{{ $top->main_image }}" class="rounded mr-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded mr-2 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;"><i class="fas fa-box"></i></div>
                                            @endif
                                            <span class="font-weight-bold text-dark small text-truncate" style="max-width: 140px;" title="{{ $top->product_name }}">{{ $top->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold">
                                        <span class="badge badge-purple px-2 py-1" style="background-color: #6366f1; color: white;">{{ $top->total_sold }} cái</span>
                                    </td>
                                    <td class="text-right font-weight-bold text-success small">
                                        {{ number_format($top->total_revenue, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">Chưa có dữ liệu bán hàng.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card card-custom border-0 shadow-sm p-0 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 border-bottom font-weight-bold text-dark d-flex justify-content-between align-items-center">
        <span><i class="fas fa-clock text-info mr-2"></i>Đơn Hàng Mới Nhất</span>
        <a href="{{ route('admin.orders.index') }}" class="small font-weight-bold text-primary">Xem tất cả <i class="fas fa-arrow-right ml-1"></i></a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light small text-uppercase text-muted">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th class="text-right">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $rOrder)
                    <tr>
                        <td class="font-weight-bold">#{{ $rOrder->id }}</td>
                        <td>{{ $rOrder->name }} <small class="text-muted d-block">{{ $rOrder->phone }}</small></td>
                        <td class="font-weight-bold text-purple" style="color: #6366f1;">{{ number_format($rOrder->total_price, 0, ',', '.') }} đ</td>
                        <td>
                            @if($rOrder->status === 'delivered')
                                <span class="badge badge-success">Đã giao hàng</span>
                            @elseif($rOrder->status === 'shipping')
                                <span class="badge badge-primary">Đang vận chuyển</span>
                            @elseif($rOrder->status === 'packaged')
                                <span class="badge badge-info">Đã đóng gói</span>
                            @elseif($rOrder->status === 'cancelled')
                                <span class="badge badge-danger">Đã hủy</span>
                            @else
                                <span class="badge badge-warning text-dark">Đang xử lý</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $rOrder->created_at ? $rOrder->created_at->format('H:i d/m/Y') : '' }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.orders.show', $rOrder) }}" class="btn btn-sm btn-outline-purple font-weight-bold">
                                Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted small">Chưa có đơn hàng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const months = @json($chartMonths ?? []);
    const revenues = @json($chartRevenue ?? []);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: revenues,
                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection
