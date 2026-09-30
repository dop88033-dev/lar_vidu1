@extends('layouts.admin')

@section('title', 'Chỉnh sửa người dùng')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold text-dark mb-0">Chỉnh sửa người dùng</h2>
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
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label class="font-weight-bold">Tên người dùng <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label class="font-weight-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
            </div>

            <div class="form-group mb-4">
                <label class="font-weight-bold">Vai trò <span class="text-danger">*</span></label>
                <select name="role" class="form-control font-weight-bold" required>
                    <option value="user" @selected($user->role === 'user')>Người dùng (Customer / User)</option>
                    <option value="admin" @selected($user->role === 'admin')>Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light font-weight-bold mr-2">Hủy</a>
                <button type="submit" class="btn btn-primary font-weight-bold px-4">
                    <i class="fas fa-sync-alt mr-1"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
