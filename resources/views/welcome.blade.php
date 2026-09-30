@extends('layouts.app')

@section('title', 'Trang chủ - PTShop')

@section('content')
<div class="container mt-2">
    <div class="jumbotron p-4 p-md-5 text-white rounded-lg mb-4 shadow-sm" style="background: linear-gradient(135deg, #1e293b, #334155); border-left: 5px solid #6366f1;">
        <div class="col-md-8 px-0">
            <h1 class="display-4 font-weight-bold">Chào mừng đến với PTShop</h1>
            <p class="lead my-3 text-light">Khám phá các sản phẩm chất lượng cao với giá ưu đãi tốt nhất.</p>
            <a href="#product-list" class="btn btn-purple px-4 py-2 mt-2">
                <i class="fas fa-shopping-bag mr-2"></i> Khám phá ngay
            </a>
        </div>
    </div>

    <!-- Phần Danh Mục Sản Phẩm (Dành cho Khách hàng) -->
    @php
        $categoriesList = \App\Models\ProductCategory::where('is_active', true)->get();
    @endphp
    @if(count($categoriesList) > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="font-weight-bold text-dark mb-0">
                    <i class="fas fa-th-large text-indigo mr-2" style="color: #6366f1;"></i> Danh mục nổi bật
                </h4>
                <a href="{{ Route::has('user.categories.index') ? route('user.categories.index') : url('/categories') }}" class="btn btn-outline-purple btn-sm">
                    Xem tất cả danh mục <i class="fas fa-chevron-right ml-1 small"></i>
                </a>
            </div>
            <div class="row">
                @foreach($categoriesList as $cat)
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ Route::has('user.categories.index') ? route('user.categories.index', ['category_id' => $cat->id]) : url('/categories?category_id=' . $cat->id) }}" class="text-decoration-none">
                            <div class="card card-custom border-0 shadow-sm p-3 text-center transition hover-shadow h-100" style="background: #ffffff;">
                                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2 shadow-sm" 
                                     style="width: 48px; height: 48px; background: #e0e7ff; color: #4338ca;">
                                    <i class="fas fa-folder font-size-18"></i>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1">{{ $cat->name }}</h6>
                                <span class="text-muted small">Xem sản phẩm <i class="fas fa-arrow-right ml-1" style="font-size: 10px;"></i></span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <h3 id="product-list" class="mb-4 font-weight-bold text-dark d-flex align-items-center">
        <i class="fas fa-boxes text-indigo mr-2" style="color: #6366f1;"></i> Tất cả Sản phẩm
    </h3>

    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card card-custom h-100 border-0 shadow-sm">
                    @if($product->main_image)
                        <img src="{{ \Illuminate\Support\Str::startsWith($product->main_image, ['http://', 'https://']) ? $product->main_image : asset($product->main_image) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light text-center py-5 text-muted" style="height: 200px; line-height: 100px;">Không có ảnh</div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title font-weight-bold text-dark mb-2">{{ $product->name }}</h5>
                        <p class="text-muted small mb-2">Danh mục: <span class="badge px-2 py-1" style="background: #e0e7ff; color: #4338ca;">{{ $product->category_name ?: 'Chung' }}</span></p>
                        <p class="card-text font-weight-bold h5 mb-3" style="color: #6366f1;">{{ number_format($product->price) }} đ</p>

                        @php
                            $imgUrl = $product->main_image ? (\Illuminate\Support\Str::startsWith($product->main_image, ['http://', 'https://']) ? $product->main_image : asset($product->main_image)) : '';
                            $stock = 0;
                            if ($product->colors && is_array($product->colors) && count($product->colors) > 0) {
                                foreach ($product->colors as $col) {
                                    $stock += intval($col['quantity'] ?? 0);
                                }
                            } else {
                                $stock = intval($product->quantity ?? 0);
                            }
                        @endphp

                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('user.categories.product-detail', $product->id) }}" class="btn btn-outline-secondary btn-sm rounded-lg px-3">
                                <i class="fas fa-eye mr-1"></i> Chi tiết
                            </a>
                            <button type="button" 
                                    class="btn btn-purple btn-sm rounded-lg px-3 font-weight-semibold"
                                    onclick="openVariantModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $imgUrl }}', {{ json_encode($product->colors ?: []) }}, {{ $stock }})">
                                <i class="fas fa-cart-plus mr-1"></i> Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted lead">Hiện chưa có sản phẩm nào trong hệ thống.</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($products, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection