@extends('layouts.admin')

@section('content')
<div class="product-card">
    <div class="section-header">
        <h2 class="page-title"><i class="fas fa-edit text-warning mr-2"></i>Chỉnh Sửa Danh Mục: {{ $productCategory->name }}</h2>
        <a href="{{ route('admin.product-categories.index') }}" class="text-back-link font-weight-bold">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="fas fa-exclamation-circle mr-1"></i> Đã xảy ra lỗi:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <form action="{{ route('admin.product-categories.update', $productCategory->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4">
                    <label class="font-weight-bold text-dark h6">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name', $productCategory->name) }}" required>
                </div>

                <div class="form-group mb-4">
                    <label class="font-weight-bold text-dark h6">Mô tả danh mục</label>
                    <textarea name="description" class="form-control" rows="5" placeholder="Nhập mô tả cho danh mục này...">{{ old('description', $productCategory->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bottom-action-bar">
            <button type="submit" class="btn btn-save-product shadow">
                <i class="fas fa-save mr-2"></i> CẬP NHẬT DANH MỤC
            </button>
        </div>
    </form>
</div>
@endsection

