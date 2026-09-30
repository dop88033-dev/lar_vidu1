@extends('layouts.app')

@section('content')
<div class="product-card">
    <div class="section-header">
        <h2 class="page-title"><i class="fas fa-folder-open text-warning mr-2"></i>Chi Tiết Danh Mục</h2>
        <div>
            <a href="{{ route('admin.product-categories.edit', $productCategory->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 mr-2">
                <i class="fas fa-edit mr-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.product-categories.index') }}" class="text-back-link font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="bg-light p-4 rounded-lg mb-4 border">
        <h3 class="font-weight-bold text-dark mb-3">{{ $productCategory->name }}</h3>
        <hr>
        <div class="mt-3">
            <h6 class="font-weight-bold text-secondary">Mô tả:</h6>
            <p class="mb-0 text-dark" style="white-space: pre-line; font-size: 1.05rem;">{{ $productCategory->description ?: 'Chưa có mô tả cho danh mục này.' }}</p>
        </div>
    </div>
    @extends('layouts.app')

@section('content')
    <h1>{{ $product->name }}</h1>

    <p>{{ $product->description }}</p>
    <p>Quantity: {{ $product->quantity }}</p>
    <p>Price: {{ $product->price }}</p>
    <p>Category: {{ $product->category->name }}</p>

    <a href="{{ route('welcome') }}">Back to list</a>
@endsection
</div>
@endsection

