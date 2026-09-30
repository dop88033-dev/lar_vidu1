@extends('layouts.admin')

@section('title', 'Danh sách người dùng')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-users text-primary mr-2"></i>Danh sách người dùng
        </h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success font-weight-bold shadow-sm">
            <i class="fas fa-user-plus mr-1"></i> + Thêm người dùng
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light small text-uppercase font-weight-bold text-muted">
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th class="text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="font-weight-bold">#{{ $user->id }}</td>
                            <td class="font-weight-bold text-dark">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge badge-danger px-2 py-1">Quản trị (Admin)</span>
                                @else
                                    <span class="badge badge-info px-2 py-1">Người dùng (User)</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm font-weight-bold mr-1">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm font-weight-bold mr-1">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Không có người dùng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
