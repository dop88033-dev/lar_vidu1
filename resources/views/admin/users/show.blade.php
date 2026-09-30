@extends('layouts.admin')

@section('title', 'Thông tin người dùng')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold text-dark mb-0">Thông tin chi tiết người dùng</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-lg p-4">
        <div class="row align-items-center">
            <div class="col-md-3 text-center mb-3 mb-md-0">
                <div class="rounded-circle bg-purple text-white font-weight-bold d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px; background-color: #6366f1;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <div class="col-md-9">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="font-weight-bold text-muted" style="width: 120px;">ID:</td>
                        <td class="font-weight-bold">#{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Họ và tên:</td>
                        <td class="font-weight-bold text-dark h5 mb-0">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Email:</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Vai trò:</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge badge-danger px-3 py-1 font-weight-bold">Quản trị viên (Admin)</span>
                            @else
                                <span class="badge badge-info px-3 py-1 font-weight-bold">Người dùng (User)</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-muted">Ngày tham gia:</td>
                        <td class="text-muted">{{ $user->created_at ? $user->created_at->format('H:i d/m/Y') : 'Chưa xác định' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary font-weight-bold mr-2">
                <i class="fas fa-edit mr-1"></i> Chỉnh sửa thông tin
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary font-weight-bold">
                <i class="fas fa-list mr-1"></i> Danh sách người dùng
            </a>
        </div>
    </div>
</div>
@endsection
