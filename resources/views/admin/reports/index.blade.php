@extends('layouts.admin')

@section('title', 'Báo cáo doanh thu')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-chart-line text-primary mr-2"></i>Báo cáo doanh thu</h2>
    </div>

    <nav class="nav nav-pills mb-4" aria-label="Báo cáo">
        <a class="nav-link active font-weight-bold" aria-current="page" href="{{ route('admin.reports.index') }}">Bảng số liệu</a>
        <a class="nav-link font-weight-bold" href="{{ route('admin.reports.charts') }}">Biểu đồ</a>
    </nav>

    <p class="text-muted">Doanh thu tính theo ngày tạo đơn, chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng.</p>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0 rounded-lg">
                <span class="text-muted text-uppercase small font-weight-bold">Tổng số đơn hàng</span>
                <h3 class="mb-0 font-weight-bold text-dark">{{ number_format($totalOrders) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0 rounded-lg">
                <span class="text-muted text-uppercase small font-weight-bold">Tổng số khách hàng</span>
                <h3 class="mb-0 font-weight-bold text-dark">{{ number_format($totalCustomers) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0 rounded-lg" style="background-color: #ecfdf5;">
                <span class="text-muted text-uppercase small font-weight-bold">Tổng doanh thu (gồm phí vận chuyển)</span>
                <h3 class="mb-0 text-success font-weight-bold">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg mb-4">
        <div class="card-header bg-white py-3 border-bottom font-weight-bold text-dark">
            <strong>Doanh thu theo danh mục</strong>
            <div class="small text-muted font-weight-normal">Tính theo giá sản phẩm khi đặt hàng, không gồm phí vận chuyển.</div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="bg-light small text-uppercase">
                    <tr>
                        <th>Danh mục</th>
                        <th class="text-right">Số lượng bán</th>
                        <th class="text-right">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryRevenue as $revenue)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $revenue->category_name ?? ('Danh mục #'.$revenue->category_id) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($revenue->total_qty) }}</td>
                            <td class="text-right font-weight-bold text-success">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach([
        ['Doanh thu theo ngày', 'Ngày', 'date', $revenueByDate, 'd/m/Y'],
        ['Doanh thu theo tháng', 'Tháng', 'month', $revenueByMonth, 'm/Y'],
        ['Doanh thu theo năm', 'Năm', 'year', $revenueByYear, null],
    ] as [$title, $label, $field, $rows, $format])
        <div class="card border-0 shadow-sm rounded-lg mb-4">
            <div class="card-header bg-white py-3 border-bottom font-weight-bold text-dark">{{ $title }}</div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th>{{ $label }}</th>
                            <th class="text-right">Số đơn đã thanh toán</th>
                            <th class="text-right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $revenue)
                            <tr>
                                <td>{{ $format ? \Carbon\Carbon::parse($revenue->{$field}.($field === 'month' ? '-01' : ''))->format($format) : $revenue->{$field} }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($revenue->order_count) }}</td>
                                <td class="text-right font-weight-bold text-purple" style="color: #6366f1;">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
