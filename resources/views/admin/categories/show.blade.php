@extends('layouts.admin')

@section('content')
<div class="product-card">
    <div class="section-header">
        <h2 class="page-title mb-0">Chi tiết sản phẩm: {{ $category->name }}</h2>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.categories.index') }}"><i class="fas fa-arrow-left mr-1"></i> Quay lại</a>
    </div>

    <div class="row mt-4">
        <div class="col-md-4 text-center">
            @if($category->main_image)
                <img src="{{ \Illuminate\Support\Str::startsWith($category->main_image, ['http://', 'https://']) ? $category->main_image : asset($category->main_image) }}" alt="{{ $category->name }}" class="img-fluid rounded border shadow-sm" style="max-height: 280px; object-fit: cover;">
            @else
                <div class="p-5 bg-light rounded border text-muted">Không có ảnh đại diện</div>
            @endif
        </div>
        <div class="col-md-8">
            <h4 class="font-weight-bold text-dark">{{ $category->name }}</h4>
            <p class="text-muted">Danh mục: <span class="badge badge-info">{{ $category->category_name ?: 'Chưa phân loại' }}</span></p>
            <h4 class="text-danger font-weight-bold my-3">{{ number_format($category->price) }} VNĐ</h4>
            
            <p><strong>Trạng thái:</strong> 
                @if($category->is_active)
                    <span class="badge badge-success">Hiển thị bán ngay</span>
                @else
                    <span class="badge badge-secondary">Ẩn</span>
                @endif
            </p>
            <p><strong>Mô tả sản phẩm:</strong> {{ $category->notes ?: 'Không có mô tả' }}</p>
        </div>
    </div>
</div>

<div class="product-card">
    <h5 class="section-title mb-4">Danh sách phân loại màu sắc</h5>
    
    @if(is_array($category->colors) && count($category->colors) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Tên màu</th>
                        <th>Mã màu (Hex)</th>
                        <th>Giá riêng (đ)</th>
                        <th>Tồn kho</th>
                        <th>Ảnh màu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->colors as $color)
                        <tr>
                            <td class="font-weight-bold align-middle">{{ $color['name'] ?? 'Không tên' }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <span class="d-inline-block rounded border mr-2" style="width: 24px; height: 24px; background-color: {{ $color['hex'] ?? '#000' }};"></span>
                                    <code>{{ $color['hex'] ?? '#000000' }}</code>
                                </div>
                            </td>
                            <td class="align-middle text-danger font-weight-bold">
                                {{ number_format($color['price'] ?? $category->price) }} VNĐ
                            </td>
                            <td class="align-middle">{{ $color['quantity'] ?? 0 }}</td>
                            <td class="align-middle">
                                @if(!empty($color['image']))
                                    <img src="{{ \Illuminate\Support\Str::startsWith($color['image'], ['http://', 'https://']) ? $color['image'] : asset($color['image']) }}" style="height: 45px; width: 45px; object-fit: cover;" class="rounded border">
                                @else
                                    <span class="text-muted small">Không có ảnh</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-light border">Chưa có thông tin phân loại màu.</div>
    @endif
</div>
@endsection