@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')

@section('content')
<div class="card-custom">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-1">Quản lý Sản phẩm</h3>
            <p class="text-muted small mb-0">Danh sách tất cả các sản phẩm và biến thể phân loại màu sắc</p>
        </div>
        <a class="btn btn-purple font-weight-bold" href="{{ route('admin.categories.create') }}">
            <i class="fas fa-plus mr-1"></i> Thêm sản phẩm
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
                    <th width="80px">Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá gốc</th>
                    <th>Màu sắc kèm giá</th>
                    <th class="text-center">Trạng thái</th>
                    <th width="180px" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr>
                    <td>
                        @if($category->main_image)
                            <img src="{{ \Illuminate\Support\Str::startsWith($category->main_image, ['http://', 'https://']) ? $category->main_image : asset($category->main_image) }}" style="width: 48px; height: 48px; object-fit: cover;" class="rounded border">
                        @else
                            <div class="rounded border bg-light text-center d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px; font-size: 10px;">No image</div>
                        @endif
                    </td>
                    <td class="font-weight-bold text-dark">{{ $category->name }}</td>
                    <td><span class="badge badge-light border px-2 py-1 text-secondary" style="font-weight: 500;">{{ $category->category_name ?: 'Chưa phân loại' }}</span></td>
                    <td class="text-danger font-weight-bold">{{ number_format($category->price) }} đ</td>
                    <td>
                        @if(is_array($category->colors) && count($category->colors) > 0)
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                @foreach($category->colors as $cItem)
                                    <span class="d-inline-block rounded-circle border shadow-sm mr-1" title="{{ $cItem['name'] ?? '' }}: {{ number_format($cItem['price'] ?? $category->price) }}đ" style="width: 18px; height: 18px; background-color: {{ $cItem['hex'] ?? '#000' }}; display: inline-block;"></span>
                                @endforeach
                                <span class="small text-muted ml-1">({{ count($category->colors) }} màu)</span>
                            </div>
                        @else
                            <span class="text-muted small">Mặc định</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-success px-2 py-1" style="background:#dcfce7; color:#15803d; font-weight: 600;">Đang bán</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                            <a class="btn btn-sm btn-info text-white font-weight-bold px-2 py-1" href="{{ route('admin.categories.show', $category->id) }}">Xem</a>
                            <a class="btn btn-sm btn-primary font-weight-bold px-2 py-1" href="{{ route('admin.categories.edit', $category->id) }}">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger font-weight-bold px-2 py-1" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-box-open fa-2x mb-3 text-secondary d-block"></i>
                        Chưa có sản phẩm nào trong hệ thống.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection