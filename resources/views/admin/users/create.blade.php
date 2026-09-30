@extends('layouts.admin')

@section('title', 'Thêm người dùng')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold text-dark mb-0">Thêm người dùng mới</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-lg">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-lg p-4">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label class="font-weight-bold">Tên người dùng <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Nhập tên..." required>
            </div>

            <div class="form-group mb-3">
                <label class="font-weight-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nhapemail@example.com" required>
            </div>

            <div class="form-group mb-3">
                <label class="font-weight-bold">Mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự..." required>
            </div>

            <div class="form-group mb-4">
                <label class="font-weight-bold">Vai trò hệ thống <span class="text-danger">*</span></label>
                <select name="role" class="form-control font-weight-bold" required>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Người dùng (Customer / User)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light font-weight-bold mr-2">Hủy</a>
                <button type="submit" class="btn btn-success font-weight-bold px-4">
                    <i class="fas fa-save mr-1"></i> Lưu người dùng
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
