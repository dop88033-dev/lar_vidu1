@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục')

@section('content')
<div class="card-custom">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-1">Quản Lý Danh Mục Sản Phẩm</h3>
            <p class="text-muted small mb-0">Danh sách các danh mục loại sản phẩm trong hệ thống</p>
        </div>
        <a class="btn btn-purple font-weight-bold" href="{{ route('admin.product-categories.create') }}">
            <i class="fas fa-plus mr-1"></i> Thêm danh mục mới
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ $message }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th width="80px">ID</th>
                    <th width="30%">Tên danh mục</th>
                    <th>Mô tả</th>
                    <th width="180px" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                <tr>
                    <td class="font-weight-bold text-secondary">#{{ $cat->id }}</td>
                    <td class="font-weight-bold text-dark">{{ $cat->name }}</td>
                    <td class="text-muted">{{ $cat->description ?: 'Chưa có mô tả' }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                            <a class="btn btn-sm btn-info text-white font-weight-bold px-2 py-1" href="{{ route('admin.product-categories.show', $cat->id) }}">Xem</a>
                            <a class="btn btn-sm btn-primary font-weight-bold px-2 py-1" href="{{ route('admin.product-categories.edit', $cat->id) }}">Sửa</a>
                            <form action="{{ route('admin.product-categories.destroy', $cat->id) }}" method="POST" style="display:inline" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger font-weight-bold px-2 py-1" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-2x mb-3 text-secondary d-block"></i>
                        Chưa có danh mục nào trong hệ thống.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
