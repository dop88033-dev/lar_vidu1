@extends('layouts.app')

@section('title', 'Danh mục sản phẩm - PTShop')

@section('content')
<div class="container py-3">
    <!-- Header Danh mục -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div>
            <h2 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-th-large text-indigo mr-2" style="color: #6366f1;"></i> 
                {{ $selectedCategory ? 'Danh mục: ' . $selectedCategory->name : 'Tất cả Danh mục Sản phẩm' }}
            </h2>
            <p class="text-muted small mb-0">Khám phá các sản phẩm đa dạng thuộc các danh mục của PTShop</p>
        </div>
        @if($selectedCategory)
            <a href="{{ route('user.categories.index') }}" class="btn btn-outline-purple btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Xem tất cả sản phẩm
            </a>
        @endif
    </div>

    <!-- Thanh Lọc & Tìm Kiếm Nâng Cao -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('user.categories.index') }}" method="GET" class="row align-items-center">
                {{-- Ô Tìm kiếm từ khóa --}}
                <div class="col-lg-4 col-md-6 mb-2 mb-lg-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="keyword" class="form-control border-left-0" 
                               placeholder="Tìm tên sản phẩm, ghi chú..." 
                               value="{{ request('keyword', $keyword ?? '') }}">
                    </div>
                </div>

                {{-- Chọn Danh mục --}}
                <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <select name="category_id" class="form-control custom-select">
                        <option value="">-- Tất cả danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (request('category_id') == $cat->id || (isset($selectedCategory) && $selectedCategory && $selectedCategory->id == $cat->id)) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sắp xếp --}}
                <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <select name="sort" class="form-control custom-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A - Z</option>
                    </select>
                </div>

                {{-- Nút bấm Lọc & Reset --}}
                <div class="col-lg-2 col-md-6 d-flex">
                    <button type="submit" class="btn btn-purple btn-block mr-1">
                        <i class="fas fa-filter mr-1"></i> Lọc
                    </button>
                    @if(request()->hasAny(['keyword', 'category_id', 'sort', 'min_price', 'max_price']))
                        <a href="{{ route('user.categories.index') }}" class="btn btn-light border" title="Xóa bộ lọc">
                            <i class="fas fa-undo text-muted"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Thanh lọc Danh mục dạng Pill Badge -->
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2 align-items-center bg-white p-3 rounded-lg border shadow-sm" style="gap: 10px;">
            <span class="font-weight-bold text-muted mr-2"><i class="fas fa-tags mr-1"></i> Danh mục nhanh:</span>
            <a href="{{ route('user.categories.index') }}" 
               class="btn btn-sm font-weight-medium rounded-pill px-3 py-2 {{ !$selectedCategory ? 'btn-purple' : 'btn-light text-dark' }}">
                Tất cả
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('user.categories.index', array_merge(request()->except(['category_id', 'page']), ['category_id' => $cat->id])) }}" 
                   class="btn btn-sm font-weight-medium rounded-pill px-3 py-2 {{ $selectedCategory && $selectedCategory->id == $cat->id ? 'btn-purple' : 'btn-light text-dark' }}">
                    <i class="fas fa-folder mr-1 opacity-75"></i> {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Danh sách Danh mục sản phẩm (Nổi bật) -->
    @if(!$selectedCategory && count($categories) > 0)
        <div class="row mb-5">
            <div class="col-12 mb-3">
                <h4 class="font-weight-bold text-dark"><i class="fas fa-layer-group text-warning mr-2"></i> Danh mục nổi bật</h4>
            </div>
            @foreach($categories as $category)
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card card-custom h-100 border-0 shadow-sm text-center p-3 hover-shadow transition">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" 
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4338ca;">
                                <i class="fas fa-boxes font-size-20" style="font-size: 24px;"></i>
                            </div>
                            <h5 class="font-weight-bold text-dark mb-2">{{ $category->name }}</h5>
                            <p class="text-muted small mb-3 text-truncate max-w-100" style="max-width: 100%;">
                                {{ $category->description ?: 'Không có mô tả cho danh mục này.' }}
                            </p>
                            <a href="{{ route('user.categories.index', ['category_id' => $category->id]) }}" class="btn btn-purple btn-sm w-100 mt-auto">
                                Khám phá ngay <i class="fas fa-chevron-right ml-1 small"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Danh sách Sản phẩm -->
    <div class="row">
        <div class="col-12 mb-3 d-flex flex-wrap align-items-center justify-content-between">
            <h4 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-box-open text-indigo mr-2" style="color: #6366f1;"></i> 
                @if(!empty($keyword))
                    Kết quả tìm kiếm cho: "<span class="text-primary">{{ $keyword }}</span>"
                @elseif($selectedCategory)
                    Sản phẩm trong: {{ $selectedCategory->name }}
                @else
                    Tất cả sản phẩm
                @endif
                <span class="badge badge-pill badge-light border text-muted ml-2">{{ $products->total() }} sản phẩm</span>
            </h4>
            @if(!empty($keyword) || $selectedCategory || request('sort'))
                <a href="{{ route('user.categories.index') }}" class="btn btn-link btn-sm text-muted">
                    <i class="fas fa-times-circle mr-1"></i> Xóa tất cả bộ lọc
                </a>
            @endif
        </div>

        @forelse($products as $product)
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card card-custom h-100 border-0 shadow-sm">
                    @if($product->main_image)
                        <img src="{{ \Illuminate\Support\Str::startsWith($product->main_image, ['http://', 'https://']) ? $product->main_image : asset($product->main_image) }}" 
                             class="card-img-top" style="height: 220px; object-fit: cover;">
                    @else
                        <div class="bg-light text-center py-5 text-muted" style="height: 220px; line-height: 120px;">
                            <i class="fas fa-image fa-2x mr-2"></i> Không có ảnh
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <span class="badge badge-pill px-3 py-1 mb-2 align-self-start" style="background: #e0e7ff; color: #4338ca; font-size: 11px;">
                            {{ $product->category_name ?: 'Chung' }}
                        </span>
                        <h5 class="card-title font-weight-bold text-dark mb-2">{{ $product->name }}</h5>
                        @if($product->notes)
                            <p class="text-muted small mb-3 text-truncate">{{ $product->notes }}</p>
                        @endif
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
                        <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="font-weight-bold h5 mb-0" style="color: #6366f1;">{{ number_format($product->price) }} đ</span>
                            <div class="btn-group">
                                <a href="{{ route('user.categories.product-detail', $product->id) }}" class="btn btn-outline-purple btn-sm">
                                    Chi tiết
                                </a>
                                <button type="button" class="btn btn-purple btn-sm" onclick="openVariantModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $imgUrl }}', {{ json_encode($product->colors ?: []) }}, {{ $stock }})">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="py-5 bg-white rounded-lg border shadow-sm">
                    <i class="fas fa-search-minus text-muted mb-3" style="font-size: 48px; color: #94a3b8;"></i>
                    <h5 class="font-weight-bold text-dark mb-2">Không tìm thấy sản phẩm phù hợp</h5>
                    <p class="text-muted small mb-3">Vui lòng thử tìm kiếm bằng từ khóa khác hoặc điều chỉnh lại bộ lọc.</p>
                    <a href="{{ route('user.categories.index') }}" class="btn btn-purple btn-sm px-4">
                        <i class="fas fa-arrow-left mr-1"></i> Xem tất cả sản phẩm
                    </a>
                </div>
            </div>
        @endforelse

        {{-- Phân trang Bootstrap --}}
        @if($products->hasPages())
            <div class="col-12 d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
