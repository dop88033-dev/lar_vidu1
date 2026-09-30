@extends('layouts.admin')

@section('title', 'Thống kê tài chính')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0 small">
                <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Quản trị</a></li>
                <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">Thống kê tài chính</li>
            </ol>
        </nav>
    </div>

    <!-- Title & Subtitle -->
    <div class="mb-3">
        <h2 class="h4 font-weight-bold text-dark mb-1">Thống kê tài chính</h2>
        <p class="text-muted small mb-0">Tổng hợp giá trị thanh toán theo trạng thái và phương thức.</p>
    </div>

    <!-- Navigation Tabs / Pills -->
    <div class="card card-body p-2 mb-4 border-0 shadow-sm bg-white rounded-lg">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.index') }}" class="btn btn-sm font-weight-bold px-3 py-1 text-white" style="background-color: #1e293b; border-radius: 6px;">
                Thống kê chỉ số
            </a>
            <a href="{{ route('admin.finance.transactions') }}" class="btn btn-sm font-weight-bold px-3 py-1 text-dark bg-light ml-2" style="border-radius: 6px;">
                Giao dịch thanh toán
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-lg">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.finance.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Tìm đơn hàng</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Mã đơn, tên hoặc số điện thoại" value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Từ ngày tạo đơn</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="dd/mm/yyyy" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-secondary">Đến ngày tạo đơn</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" placeholder="dd/mm/yyyy" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold text-secondary">Số tiền từ (đ)</label>
                        <input type="number" name="min_amount" step="1000" class="form-control form-control-sm" placeholder="Không giới hạn" value="{{ $filters['min_amount'] ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold text-secondary">Số tiền đến (đ)</label>
                        <input type="number" name="max_amount" step="1000" class="form-control form-control-sm" placeholder="Không giới hạn" value="{{ $filters['max_amount'] ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold text-secondary">Phương thức</label>
                        <select name="gateway" class="form-control form-control-sm custom-select custom-select-sm">
                            <option value="">Tất cả</option>
                            @foreach($methods as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['gateway'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold text-secondary">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-control form-control-sm custom-select custom-select-sm">
                            <option value="">Tất cả</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['payment_status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold" style="background-color: #2563eb; border: none; border-radius: 6px;">
                        Áp dụng bộ lọc
                    </button>
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-secondary btn-sm px-3 ml-2 text-dark font-weight-bold bg-white" style="border-radius: 6px;">
                        Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter summary text -->
    <div class="text-muted small mb-3">
        Có <strong>{{ number_format($summary->order_count ?? 0) }}</strong> đơn phù hợp. Số tiền bao gồm phí vận chuyển, thống kê theo ngày tạo đơn trên toàn bộ kết quả lọc.
    </div>

    <!-- 8 Summary Cards Grid (2 rows x 4 columns) -->
    <div class="row mb-4">
        <!-- 1. Tổng giá trị đơn hàng -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Tổng giá trị đơn hàng</div>
                        <div class="h4 font-weight-bold text-dark mb-1">{{ number_format($summary->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($summary->order_count ?? 0) }} đơn, bao gồm đơn đã hủy</div>
                </div>
            </div>
        </div>

        <!-- 2. Chờ thanh toán -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Chờ thanh toán</div>
                        <div class="h4 font-weight-bold mb-1" style="color: #d97706;">{{ number_format($statusTotals->get('pending')?->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($statusTotals->get('pending')?->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 3. Đang chờ MoMo -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Đang chờ MoMo</div>
                        <div class="h4 font-weight-bold mb-1" style="color: #d97706;">{{ number_format($statusTotals->get('initiated')?->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($statusTotals->get('initiated')?->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 4. Đã thanh toán -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Đã thanh toán</div>
                        <div class="h4 font-weight-bold mb-1 text-success">{{ number_format($statusTotals->get('paid')?->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($statusTotals->get('paid')?->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 5. Thanh toán thất bại -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Thanh toán thất bại</div>
                        <div class="h4 font-weight-bold mb-1 text-danger">{{ number_format($statusTotals->get('failed')?->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($statusTotals->get('failed')?->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 6. Đã hủy -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Đã hủy</div>
                        <div class="h4 font-weight-bold mb-1 text-secondary">{{ number_format($statusTotals->get('cancelled')?->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($statusTotals->get('cancelled')?->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 7. Chờ hoàn tiền -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Chờ hoàn tiền</div>
                        <div class="h4 font-weight-bold mb-1 text-info">{{ number_format($statusTotals->get('refund_pending')?->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($statusTotals->get('refund_pending')?->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- 8. Đã hoàn tiền -->
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 rounded-lg">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold mb-2">Đã hoàn tiền</div>
                        <div class="h4 font-weight-bold mb-1 text-primary">{{ number_format($statusTotals->get('refunded')?->total_amount ?? 0) }} đ</div>
                    </div>
                    <div class="text-secondary small">{{ number_format($statusTotals->get('refunded')?->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table: Thống kê theo phương thức -->
    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="card-title h6 font-weight-bold text-dark mb-0">Thống kê theo phương thức</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="border-top-0 font-weight-bold">Phương thức</th>
                        <th class="border-top-0 font-weight-bold text-center">Số đơn</th>
                        <th class="border-top-0 font-weight-bold text-right">Tổng giá trị</th>
                        <th class="border-top-0 font-weight-bold text-right">Đã thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['cod' => 'COD', 'momo' => 'MoMo', 'unknown' => 'Chưa xác định'] as $key => $label)
                        @php
                            $mItem = $methodTotals->get($key);
                        @endphp
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $label }}</td>
                            <td class="text-center">{{ number_format($mItem?->order_count ?? 0) }}</td>
                            <td class="text-right">{{ number_format($mItem?->total_amount ?? 0) }} đ</td>
                            <td class="text-right">{{ number_format($mItem?->paid_amount ?? 0) }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
