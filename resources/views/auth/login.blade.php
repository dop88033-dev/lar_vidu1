@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-md-5">
        <div class="card card-custom border-0 shadow-lg rounded-lg">
            <div class="card-header text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <div class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle mb-2 shadow-sm" style="width: 48px; height: 48px;">
                    <i class="fas fa-sign-in-alt font-size-20" style="color: #6366f1; font-size: 20px;"></i>
                </div>
                <h4 class="mb-0 font-weight-bold">Đăng nhập hệ thống</h4>
                <p class="small mb-0 text-white-50 mt-1">Truy cập vào tài khoản PTShop của bạn</p>
            </div>
            <div class="card-body p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 rounded-lg" role="alert" style="background-color: #d1fae5; color: #065f46;">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

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

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="email" class="font-weight-semibold text-dark">Địa chỉ Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                            </div>
                            <input type="email" name="email" id="email" class="form-control border-left-0" placeholder="nhapemail@example.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="font-weight-semibold text-dark">Mật khẩu</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                            </div>
                            <input type="password" name="password" id="password" class="form-control border-left-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-purple btn-block font-weight-bold py-2 shadow-sm">
                        <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
                    </button>
                </form>
            </div>
            <div class="card-footer text-center bg-light border-top-0 py-3">
                <span class="text-muted">Chưa có tài khoản?</span> 
                <a href="{{ route('register') }}" class="font-weight-bold" style="color: #6366f1;">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>
@endsection
