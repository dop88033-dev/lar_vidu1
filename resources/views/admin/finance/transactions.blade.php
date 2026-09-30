@extends('layouts.admin')

@section('title', 'Giao dịch thanh toán')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0 small">
                <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Quản trị</a></li>
                <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">Giao dịch thanh toán</li>
            </ol>
        </nav>
    </div>

    <!-- Title & Subtitle -->
    <div class="mb-3">
        <h2 class="h4 font-weight-bold text-dark mb-1">Giao dịch thanh toán</h2>
        <p class="text-muted small mb-0">Tra cứu thanh toán theo đơn hàng và cập nhật trạng thái COD.</p>
    </div>

    <!-- Navigation Tabs / Pills -->
    <div class="card card-body p-2 mb-4 border-0 shadow-sm bg-white rounded-lg">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.index') }}" class="btn btn-sm font-weight-bold px-3 py-1 text-dark bg-light" style="border-radius: 6px;">
                Thống kê chỉ số
            </a>
            <a href="{{ route('admin.finance.transactions') }}" class="btn btn-sm font-weight-bold px-3 py-1 text-white ml-2" style="background-color: #1e293b; border-radius: 6px;">
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i><strong>Không thể cập nhật trạng thái:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-lg">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.finance.transactions') }}">
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

                <div class="row align-items-center">
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold text-secondary">Sắp xếp</label>
                        <select name="sort" class="form-control form-control-sm custom-select custom-select-sm">
                            <option value="newest" {{ ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="amount_desc" {{ ($filters['sort'] ?? '') === 'amount_desc' ? 'selected' : '' }}>Số tiền giảm dần</option>
                            <option value="amount_asc" {{ ($filters['sort'] ?? '') === 'amount_asc' ? 'selected' : '' }}>Số tiền tăng dần</option>
                        </select>
                    </div>
                    <div class="col-md-9 mb-3 d-flex align-items-end gap-2" style="margin-top: 24px;">
                        <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold" style="background-color: #2563eb; border: none; border-radius: 6px;">
                            Áp dụng bộ lọc
                        </button>
                        <a href="{{ route('admin.finance.transactions') }}" class="btn btn-outline-secondary btn-sm px-3 ml-2 text-dark font-weight-bold bg-white" style="border-radius: 6px;">
                            Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter summary text -->
    <div class="text-muted small mb-3">
        Có <strong>{{ $orders->total() }}</strong> đơn phù hợp bộ lọc. Số tiền bao gồm phí vận chuyển; ngày lọc là ngày tạo đơn.
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-lg mb-4">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="card-title h6 font-weight-bold text-dark mb-1">Danh sách giao dịch ({{ $orders->total() }} đơn)</h5>
            <div class="text-muted small">COD: xác nhận thu tiền hoặc thất bại; đơn đã thu tiền có thể chuyển sang chờ hoàn tiền rồi xác nhận đã hoàn tiền.</div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="border-top-0 font-weight-bold">Đơn hàng</th>
                        <th class="border-top-0 font-weight-bold">Khách hàng</th>
                        <th class="border-top-0 font-weight-bold">Phương thức</th>
                        <th class="border-top-0 font-weight-bold text-right">Số tiền</th>
                        <th class="border-top-0 font-weight-bold">Thanh toán</th>
                        <th class="border-top-0 font-weight-bold text-center">Cập nhật COD</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $isCod = ($order->gateway === 'cod') || in_array($order->status, ['cod_ordered', 'cod_paid'], true);
                            $allowedTransitions = $codTransitions[$order->payment_status] ?? ['pending', 'paid', 'failed'];
                            $codStatusLabels = [
                                'pending' => 'Chờ thu tiền (Chờ thanh toán)',
                                'initiated' => 'Chờ thu tiền COD',
                                'paid' => 'Đã thu tiền (Đã thanh toán)',
                                'failed' => 'Thanh toán thất bại',
                                'refund_pending' => 'Chờ hoàn tiền',
                                'refunded' => 'Đã hoàn tiền',
                                'cancelled' => 'Đã hủy',
                            ];
                        @endphp
                        <tr>
                            <td class="font-weight-bold">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary text-decoration-none">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $order->name ?? 'Khách lẻ' }}</div>
                                <div class="small text-muted">{{ $order->phone ?? '---' }}</div>
                            </td>
                            <td>
                                @if($order->gateway === 'cod')
                                    <span>COD</span>
                                @elseif($order->gateway === 'momo')
                                    <span>MoMo</span>
                                @else
                                    <span class="text-muted">Chưa xác định</span>
                                @endif
                            </td>
                            <td class="text-right font-weight-bold text-dark">
                                {{ number_format($order->total_price) }} đ
                            </td>
                            <td>
                                @switch($order->payment_status)
                                    @case('paid')
                                        <span class="badge badge-success px-2 py-1">{{ $statuses['paid'] }}</span>
                                        @break
                                    @case('pending')
                                        @if($order->gateway === 'cod')
                                            <span class="badge badge-warning text-dark px-2 py-1">Chờ thu tiền COD</span>
                                        @else
                                            <span class="badge badge-warning text-dark px-2 py-1">{{ $statuses['pending'] }}</span>
                                        @endif
                                        @break
                                    @case('initiated')
                                        @if($order->gateway === 'cod')
                                            <span class="badge badge-warning text-dark px-2 py-1">Chờ thu tiền COD</span>
                                        @else
                                            <span class="badge badge-info px-2 py-1">Đang chờ MoMo</span>
                                        @endif
                                        @break
                                    @case('failed')
                                        <span class="badge badge-danger px-2 py-1">{{ $statuses['failed'] }}</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-secondary px-2 py-1">{{ $statuses['cancelled'] }}</span>
                                        @break
                                    @case('refund_pending')
                                        <span class="badge badge-dark px-2 py-1">{{ $statuses['refund_pending'] }}</span>
                                        @break
                                    @case('refunded')
                                        <span class="badge badge-outline-danger px-2 py-1" style="border:1px solid #ef4444; color:#ef4444;">{{ $statuses['refunded'] }}</span>
                                        @break
                                    @default
                                        <span class="badge badge-light text-dark px-2 py-1">{{ $order->payment_status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                @if($isCod)
                                    <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1 font-weight-bold" data-toggle="modal" data-target="#updateCodModal{{ $order->id }}" style="border-radius:6px;">
                                        Cập nhật
                                    </button>

                                    <!-- Update COD Status Modal -->
                                    <div class="modal fade text-left" id="updateCodModal{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalTitle{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius:10px;">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold text-dark h6" id="modalTitle{{ $order->id }}">
                                                        Cập Nhật Thanh Toán COD Đơn #{{ $order->id }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.finance.update-status', $order->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    
                                                    <input type="hidden" name="current_payment_status" value="{{ $order->payment_status }}">
                                                    <input type="hidden" name="current_order_status" value="{{ $order->status }}">
                                                    <input type="hidden" name="current_payment_id" value="{{ $order->payment_id ?? 0 }}">

                                                    <div class="modal-body p-4">
                                                        <div class="alert alert-light border py-2 px-3 small mb-3">
                                                            <strong>Khách hàng:</strong> {{ $order->name }} (SĐT: {{ $order->phone }})<br>
                                                            <strong>Tổng số tiền:</strong> {{ number_format($order->total_price) }} đ
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label class="small font-weight-bold text-secondary">Trạng Thái Thanh Toán Mới</label>
                                                            <select name="payment_status" class="form-control custom-select">
                                                                @foreach($allowedTransitions as $statusKey)
                                                                    <option value="{{ $statusKey }}" {{ $order->payment_status === $statusKey ? 'selected' : '' }}>
                                                                        {{ $codStatusLabels[$statusKey] ?? $statuses[$statusKey] ?? $statusKey }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn btn-primary btn-sm px-4" style="background-color: #2563eb; border:none;">
                                                            Lưu cập nhật
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Không có đơn hàng phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3">
                <div class="small text-muted">
                    Hiển thị <strong>{{ $orders->firstItem() }}</strong> đến <strong>{{ $orders->lastItem() }}</strong> trên <strong>{{ $orders->total() }}</strong> đơn
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
