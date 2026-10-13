@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - PTShop')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-5">
        <div class="card card-custom border-0 shadow-lg rounded-lg">
            <div class="card-header text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <div class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle mb-2 shadow-sm" style="width: 48px; height: 48px;">
                    <i class="fas fa-lock-open font-size-20" style="color: #6366f1; font-size: 20px;"></i>
                </div>
                <h4 class="mb-0 font-weight-bold">Tạo mật khẩu mới</h4>
                <p class="small mb-0 text-white-50 mt-1">Nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>
            <div class="card-body p-4">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-lg" role="alert" style="background-color: #fee2e2; color: #991b1b;">
                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-lg" style="background-color: #fee2e2; color: #991b1b;">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    {{-- Hidden token --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email --}}
                    <div class="form-group mb-3">
                        <label for="email" class="font-weight-semibold text-dark">Địa chỉ Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                            </div>
                            <input type="email" name="email" id="email" class="form-control border-left-0" 
                                   placeholder="vi_du@gmail.com" value="{{ $email ?? old('email') }}" required autofocus readonly>
                        </div>
                    </div>

                    {{-- Mật khẩu mới --}}
                    <div class="form-group mb-3">
                        <label for="password" class="font-weight-semibold text-dark">Mật khẩu mới</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                            </div>
                            <input type="password" name="password" id="password" class="form-control border-left-0" 
                                   placeholder="Tối thiểu 6 ký tự" required>
                        </div>
                    </div>

                    {{-- Xác nhận mật khẩu mới --}}
                    <div class="form-group mb-4">
                        <label for="password_confirmation" class="font-weight-semibold text-dark">Xác nhận mật khẩu mới</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-shield-alt text-muted"></i></span>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-left-0" 
                                   placeholder="Nhập lại mật khẩu mới" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-purple btn-block font-weight-bold py-2 shadow-sm">
                        <i class="fas fa-check-circle mr-2"></i> Lưu mật khẩu mới & Đăng nhập
                    </button>
                </form>
            </div>
            <div class="card-footer text-center bg-light border-top-0 py-3">
                <a href="{{ route('login') }}" class="font-weight-bold text-muted small">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại trang Đăng nhập
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
