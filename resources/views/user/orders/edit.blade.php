@extends('layouts.app')

@section('title', 'Chỉnh sửa đơn hàng #' . $order->id . ' - PTShop')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white shadow-sm border rounded-lg">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-primary font-weight-bold"><i class="fas fa-home mr-1"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.orders.index') }}" class="text-primary font-weight-bold"><i class="fas fa-box mr-1"></i> Lịch sử đơn hàng</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.orders.show', $order) }}" class="text-primary font-weight-bold">Đơn hàng #{{ $order->id }}</a></li>
            <li class="breadcrumb-item active font-weight-semibold" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card card-custom border-0 shadow-lg rounded-lg overflow-hidden">
                <div class="card-header bg-dark text-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="font-weight-bold mb-0">
                        <i class="fas fa-edit text-indigo mr-2" style="color: #818cf8;"></i> Chỉnh sửa thông tin đơn hàng #{{ $order->id }}
                    </h5>
                    <span class="badge badge-purple px-3 py-1" style="background: linear-gradient(135deg, #6366f1, #4f46e5); font-size: 12px;">
                        {{ number_format($order->total_price, 0, ',', '.') }} VNĐ
                    </span>
                </div>

                <form action="{{ route('user.orders.update', $order) }}" method="POST" class="card-body p-4">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-info border-0 rounded-lg small mb-4" style="background-color: #e0f2fe; color: #0369a1;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Bạn có thể cập nhật tên người nhận, số điện thoại và địa chỉ giao hàng trước khi đơn hàng được vận chuyển.
                    </div>

                    <div class="form-group mb-3">
                        <label for="name" class="font-weight-bold text-dark small text-uppercase">
                            Tên người nhận <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-user text-muted"></i></span>
                            </div>
                            <input type="text" name="name" id="name" class="form-control border-left-0 @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $order->name) }}" required placeholder="Nhập họ và tên">
                        </div>
                        @error('name')
                            <small class="text-danger font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone" class="font-weight-bold text-dark small text-uppercase">
                            Số điện thoại <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-phone text-muted"></i></span>
                            </div>
                            <input type="text" name="phone" id="phone" class="form-control border-left-0 @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $order->phone) }}" required placeholder="Ví dụ: 0987654321">
                        </div>
                        @error('phone')
                            <small class="text-danger font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="address" class="font-weight-bold text-dark small text-uppercase">
                            Địa chỉ nhận hàng <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                            </div>
                            <input type="text" name="address" id="address" class="form-control border-left-0 @error('address') is-invalid @enderror" 
                                   value="{{ old('address', $order->address) }}" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành">
                        </div>
                        @error('address')
                            <small class="text-danger font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="note" class="font-weight-bold text-dark small text-uppercase">Ghi chú đơn hàng (Tùy chọn)</label>
                        <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3" 
                                  placeholder="Ghi chú thêm cho người giao hàng...">{{ old('note', $order->note) }}</textarea>
                        @error('note')
                            <small class="text-danger font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="{{ route('user.orders.show', $order) }}" class="btn btn-outline-secondary font-weight-bold rounded-lg px-4">
                            <i class="fas fa-arrow-left mr-1"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-purple font-weight-bold rounded-lg px-4 py-2">
                            <i class="fas fa-save mr-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
