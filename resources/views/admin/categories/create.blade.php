@extends('layouts.admin')

@section('content')
<h2 class="page-title">Thêm Sản Phẩm Mới</h2>

@if ($errors->any())
    <div class="alert alert-danger rounded-lg mb-4">
        <strong>Lỗi!</strong> Vui lòng kiểm tra lại thông tin nhập vào.<br><br>
        <ul class="mb-0 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- 1. Thông tin cơ bản -->
    <div class="product-card">
        <div class="section-header">
            <h5 class="section-title">1. Thông tin cơ bản</h5>
            <a href="{{ route('admin.categories.index') }}" class="text-back-link">- Quay lại</a>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-600">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên sản phẩm..." value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-600">Giá hiển thị gốc (đ)</label>
                    <input type="number" name="price" class="form-control" placeholder="VD: 500000" value="{{ old('price') }}" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-600">Mô tả sản phẩm</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Nhập mô tả sản phẩm...">{{ old('notes') }}</textarea>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label font-weight-600" for="is_active">
                        Hiển thị bán ngay
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-600">Danh mục sản phẩm</label>
                    <select name="category_name" class="form-control">
                        <option value="">-- Chọn danh mục --</option>
                        @if(isset($productCategories) && count($productCategories) > 0)
                            @foreach($productCategories as $pCat)
                                <option value="{{ $pCat->name }}" {{ old('category_name') == $pCat->name ? 'selected' : '' }}>{{ $pCat->name }}</option>
                            @endforeach
                        @else
                            <option value="Bóng bàn" {{ old('category_name') == 'Bóng bàn' ? 'selected' : '' }}>Bóng bàn</option>
                            <option value="Vợt bóng bàn" {{ old('category_name') == 'Vợt bóng bàn' ? 'selected' : '' }}>Vợt bóng bàn</option>
                            <option value="Cốt vợt" {{ old('category_name') == 'Cốt vợt' ? 'selected' : '' }}>Cốt vợt</option>
                            <option value="Mặt vợt" {{ old('category_name') == 'Mặt vợt' ? 'selected' : '' }}>Mặt vợt</option>
                            <option value="Phụ kiện" {{ old('category_name') == 'Phụ kiện' ? 'selected' : '' }}>Phụ kiện</option>
                        @endif
                    </select>
                    <small class="text-muted"><a href="{{ route('admin.categories.create') }}" target="_blank">+ Thêm danh mục mới vào hệ thống</a></small>
                </div>
                <div class="form-group">
                    <label class="font-weight-600">Ảnh đại diện chính</label>
                    <div class="custom-file-wrapper p-3 border rounded bg-light">
                        <input type="file" name="main_image" class="form-control-file mb-2" accept="image/*">
                        <div class="text-muted small font-italic mb-1">Hoặc dán URL link ảnh trực tiếp:</div>
                        <input type="text" name="main_image_url" class="form-control form-control-sm" placeholder="https://example.com/anh-san-pham.jpg" value="{{ old('main_image_url') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Phân màu sản phẩm -->
    <div class="product-card">
        <div class="section-header">
            <h5 class="section-title">2. Phân màu sản phẩm</h5>
            <button type="button" class="btn btn-add-color" id="btn-add-color">
                + Thêm màu mới
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless table-variant mb-0" id="table-colors">
                <thead>
                    <tr>
                        <th style="width: 20%;">Tên màu</th>
                        <th style="width: 20%;">Mã màu (Hex)</th>
                        <th style="width: 16%;">Giá riêng (đ)</th>
                        <th style="width: 14%;">Tồn kho</th>
                        <th style="width: 24%;">Ảnh màu này</th>
                        <th style="width: 6%; text-align: center;"></th>
                    </tr>
                </thead>
                <tbody id="color-rows-container">
                    <!-- Default Row 1: Đỏ -->
                    <tr class="color-row-item">
                        <td>
                            <input type="text" name="colors[0][name]" class="form-control" placeholder="VD: Đỏ" value="Đỏ">
                        </td>
                        <td>
                            <div class="color-picker-wrapper">
                                <input type="color" class="color-picker-input color-swatch-picker" value="#e63946" data-target="#hex-input-0">
                                <input type="text" name="colors[0][hex]" id="hex-input-0" class="form-control hex-text-input" value="#e63946" placeholder="#e63946">
                            </div>
                        </td>
                        <td>
                            <input type="number" name="colors[0][price]" class="form-control" placeholder="Giá riêng (đ)">
                        </td>
                        <td>
                            <input type="number" name="colors[0][quantity]" class="form-control" value="10" placeholder="10">
                        </td>
                        <td>
                            <input type="file" name="colors[0][image]" class="form-control-file mb-1" accept="image/*">
                            <input type="text" name="colors[0][image_url]" class="form-control form-control-sm" placeholder="Hoặc dán URL link ảnh...">
                        </td>
                        <td class="text-center align-middle">
                            <a href="javascript:void(0)" class="btn-delete-row">Xóa</a>
                        </td>
                    </tr>

                    <!-- Default Row 2: Xanh -->
                    <tr class="color-row-item">
                        <td>
                            <input type="text" name="colors[1][name]" class="form-control" placeholder="VD: Xanh" value="Xanh">
                        </td>
                        <td>
                            <div class="color-picker-wrapper">
                                <input type="color" class="color-picker-input color-swatch-picker" value="#1d3557" data-target="#hex-input-1">
                                <input type="text" name="colors[1][hex]" id="hex-input-1" class="form-control hex-text-input" value="#1d3557" placeholder="#1d3557">
                            </div>
                        </td>
                        <td>
                            <input type="number" name="colors[1][price]" class="form-control" placeholder="Giá riêng (đ)">
                        </td>
                        <td>
                            <input type="number" name="colors[1][quantity]" class="form-control" value="10" placeholder="10">
                        </td>
                        <td>
                            <input type="file" name="colors[1][image]" class="form-control-file mb-1" accept="image/*">
                            <input type="text" name="colors[1][image_url]" class="form-control form-control-sm" placeholder="Hoặc dán URL link ảnh...">
                        </td>
                        <td class="text-center align-middle">
                            <a href="javascript:void(0)" class="btn-delete-row">Xóa</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lưu sản phẩm -->
    <div class="bottom-action-bar">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary font-weight-bold px-4 mr-2" style="border-radius: 8px;">Hủy bỏ</a>
        <button type="submit" class="btn btn-save-product">
            <i class="fas fa-save mr-2"></i> Lưu Sản Phẩm
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        let colorIndex = 2;

        // Synchronize color swatch with text hex input
        $(document).on('input change', '.color-swatch-picker', function () {
            let targetSelector = $(this).data('target');
            $(targetSelector).val($(this).val());
        });

        $(document).on('input change', '.hex-text-input', function () {
            let hexVal = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(hexVal)) {
                let swatchPicker = $(this).closest('.color-picker-wrapper').find('.color-swatch-picker');
                swatchPicker.val(hexVal);
            }
        });

        // Add new color row
        $('#btn-add-color').click(function () {
            let defaultHex = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
            let newRow = `
                <tr class="color-row-item">
                    <td>
                        <input type="text" name="colors[${colorIndex}][name]" class="form-control" placeholder="VD: Tên màu">
                    </td>
                    <td>
                        <div class="color-picker-wrapper">
                            <input type="color" class="color-picker-input color-swatch-picker" value="${defaultHex}" data-target="#hex-input-${colorIndex}">
                            <input type="text" name="colors[${colorIndex}][hex]" id="hex-input-${colorIndex}" class="form-control hex-text-input" value="${defaultHex}" placeholder="${defaultHex}">
                        </div>
                    </td>
                    <td>
                        <input type="number" name="colors[${colorIndex}][price]" class="form-control" placeholder="Giá riêng (đ)">
                    </td>
                    <td>
                        <input type="number" name="colors[${colorIndex}][quantity]" class="form-control" value="10" placeholder="10">
                    </td>
                    <td>
                        <input type="file" name="colors[${colorIndex}][image]" class="form-control-file mb-1" accept="image/*">
                        <input type="text" name="colors[${colorIndex}][image_url]" class="form-control form-control-sm" placeholder="Hoặc dán URL link ảnh...">
                    </td>
                    <td class="text-center align-middle">
                        <a href="javascript:void(0)" class="btn-delete-row">Xóa</a>
                    </td>
                </tr>
            `;
            $('#color-rows-container').append(newRow);
            colorIndex++;
        });

        // Remove color row
        $(document).on('click', '.btn-delete-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection