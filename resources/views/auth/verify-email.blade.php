@extends('layouts.app')

@section('title', 'Xác thực Email')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-6">
        <div class="card card-custom border-0 shadow-lg text-center p-4">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow" style="width: 64px; height: 64px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                    <i class="fas fa-envelope-open-text font-size-24" style="font-size: 28px;"></i>
                </div>
                <h3 class="font-weight-bold text-dark mb-2">Xác thực vị trí Email</h3>
                <p class="text-muted mb-4">Vui lòng kiểm tra hòm thư Email để xác thực tài khoản của bạn trước khi tiếp tục.</p>

                @if (session('message'))
                    <div class="alert alert-success border-0 rounded-lg mb-4" role="alert" style="background-color: #d1fae5; color: #065f46;">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('message') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-purple font-weight-bold px-4 py-2">
                        <i class="fas fa-paper-plane mr-2"></i> Gửi lại email xác thực
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection