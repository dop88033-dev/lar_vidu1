@extends('layouts.app')

@section('title', $product->name . ' - Chi tiết sản phẩm')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white shadow-sm border rounded-lg">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" style="color: #6366f1;">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.categories.index') }}" style="color: #6366f1;">Danh mục</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="card card-custom border-0 shadow-lg rounded-lg overflow-hidden">
        <div class="row no-gutters">
            <div class="col-md-6 bg-light d-flex align-items-center justify-content-center p-4" style="min-height: 350px;">
                @if($product->main_image)
                    <img id="detail-main-image" src="{{ \Illuminate\Support\Str::startsWith($product->main_image, ['http://', 'https://']) ? $product->main_image : asset($product->main_image) }}" 
                         class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: cover;">
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-image fa-4x mb-3"></i>
                        <p class="mb-0">Không có hình ảnh hiển thị</p>
                    </div>
                @endif
            </div>
            <div class="col-md-6 p-4 p-md-5 d-flex flex-column justify-content-between">
                <div>
                    <div class="mb-2">
                        <span class="badge badge-pill px-3 py-1" style="background: #e0e7ff; color: #4338ca; font-size: 12px;">
                            {{ $product->category_name ?: 'Chung' }}
                        </span>
                    </div>
                    <h2 class="font-weight-bold text-dark mb-3">{{ $product->name }}</h2>
                    <h3 class="font-weight-bold mb-4" style="color: #6366f1;">
                        <span id="detail-price-val">{{ number_format($product->price) }}</span> VNĐ
                    </h3>

                    @if($product->notes)
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 12px;">Mô tả sản phẩm</h6>
                            <p class="text-secondary leading-relaxed">{{ $product->notes }}</p>
                        </div>
                    @endif

                    @if($product->colors && is_array($product->colors) && count($product->colors) > 0)
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 12px;">Chọn phân loại / Màu sắc <span class="text-danger">*</span></h6>
                            <div class="d-flex flex-wrap gap-2" id="detail-color-options">
                                @foreach($product->colors as $color)
                                    @php $cName = $color['name'] ?? 'Màu ' . ($loop->index + 1); @endphp
                                    <button type="button" 
                                            class="btn btn-sm detail-variant-btn mr-2 mb-2 {{ $loop->first ? 'btn-purple active' : 'btn-outline-secondary' }}"
                                            onclick="selectDetailVariant(this, '{{ addslashes($cName) }}', {{ $loop->index }})">
                                        <i class="fas fa-check-circle mr-1 detail-check {{ $loop->first ? '' : 'd-none' }}"></i>
                                        {{ $cName }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 12px;">Phân loại</h6>
                            <span class="badge badge-light border px-3 py-2 text-dark font-weight-medium">Tiêu chuẩn</span>
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <h6 class="font-weight-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 12px;">Số lượng</h6>
                        <div class="d-flex align-items-center">
                            <div class="input-group" style="width: 140px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeDetailQty(-1)">-</button>
                                </div>
                                <input type="number" id="detail-qty" class="form-control text-center font-weight-bold" value="1" min="1" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeDetailQty(1)">+</button>
                                </div>
                            </div>
                            <span class="ml-3">
                                <span class="badge badge-success px-2 py-1" id="detail-stock-indicator">
                                    <i class="fas fa-check-circle mr-1"></i> Đang tải...
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                @php
                    $imgUrl = $product->main_image ? (\Illuminate\Support\Str::startsWith($product->main_image, ['http://', 'https://']) ? $product->main_image : asset($product->main_image)) : '';
                @endphp
                <div class="pt-4 border-top d-flex gap-3">
                    <a href="{{ route('user.categories.index') }}" class="btn btn-outline-purple mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại danh mục
                    </a>
                    <button type="button" 
                            id="detail-add-to-cart-btn"
                            class="btn btn-purple px-4 font-weight-bold"
                            onclick="addDetailToCart()">
                        <i class="fas fa-shopping-cart mr-2"></i> Thêm vào giỏ hàng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews & Rating Section -->
    <div class="card card-custom border-0 shadow-lg rounded-lg mt-4 p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
            <div>
                <h4 class="font-weight-bold text-dark mb-1">
                    <i class="fas fa-star text-warning mr-2"></i> Đánh Giá & Phản Hồi Từ Khách Hàng
                </h4>
                <p class="text-muted small mb-0">Các nhận xét thực tế từ người dùng đã mua sản phẩm này</p>
            </div>
            @php
                $reviews = $product->reviews()->with('user')->latest()->get();
                $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 5.0;
            @endphp
            <div class="text-right">
                <div class="h2 font-weight-bold text-dark mb-0">
                    {{ $avgRating }} <span class="h5 text-warning"><i class="fas fa-star"></i></span>
                </div>
                <small class="text-muted">Tất cả {{ $reviews->count() }} đánh giá</small>
            </div>
        </div>

        @forelse($reviews as $rev)
            <div class="p-3 mb-3 bg-light rounded-lg border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-purple text-white font-weight-bold d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background-color: #6366f1;">
                            {{ strtoupper(substr($rev->user->name ?? 'K', 0, 1)) }}
                        </div>
                        <div>
                            <strong class="text-dark d-block mb-0">{{ $rev->user->name ?? 'Khách hàng' }}</strong>
                            <div class="text-warning small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                                <span class="text-dark font-weight-bold ml-1">({{ $rev->rating }}/5 sao)</span>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted"><i class="far fa-clock mr-1"></i> {{ $rev->created_at ? $rev->created_at->format('d/m/Y') : '' }}</small>
                </div>
                <p class="mb-0 text-secondary pl-5 font-italic">
                    "{{ $rev->comment ?: 'Khách hàng không để lại lời nhắn.' }}"
                </p>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="far fa-comment-dots fa-3x mb-2 text-secondary opacity-50"></i>
                <p class="mb-0 font-weight-medium">Chưa có đánh giá nào cho sản phẩm này.</p>
                <small>Hãy là người đầu tiên đặt hàng và đánh giá trải nghiệm sản phẩm nhé!</small>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    const detailColors = @json($product->colors ?: []);
    const productDetailBase = {
        price: {{ $product->price }},
        image: '{{ $imgUrl }}',
        stock: {{ (isset($product->quantity) && $product->quantity > 0) ? $product->quantity : 99 }}
    };

    let selectedDetailVariant = '{{ ($product->colors && is_array($product->colors) && count($product->colors) > 0) ? addslashes($product->colors[0]['name'] ?? 'Mặc định') : 'Tiêu chuẩn' }}';
    
    // Active values
    let activeDetailPrice = productDetailBase.price;
    let activeDetailStock = productDetailBase.stock;
    let activeDetailImage = productDetailBase.image;

    function selectDetailVariant(btn, variantName, index) {
        document.querySelectorAll('#detail-color-options .detail-variant-btn').forEach(b => {
            b.classList.remove('btn-purple', 'active');
            b.classList.add('btn-outline-secondary');
            const icon = b.querySelector('.detail-check');
            if (icon) icon.classList.add('d-none');
        });

        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-purple', 'active');
        const icon = btn.querySelector('.detail-check');
        if (icon) icon.classList.remove('d-none');

        selectedDetailVariant = variantName;

        // Update active product properties based on selected color variant
        if (detailColors && detailColors[index]) {
            const colorObj = detailColors[index];
            activeDetailPrice = colorObj.price ? parseFloat(colorObj.price) : productDetailBase.price;
            activeDetailStock = (colorObj.quantity !== undefined && colorObj.quantity !== null && colorObj.quantity !== '') ? parseInt(colorObj.quantity) : productDetailBase.stock;
            activeDetailImage = colorObj.image ? colorObj.image : productDetailBase.image;
        } else {
            activeDetailPrice = productDetailBase.price;
            activeDetailStock = productDetailBase.stock;
            activeDetailImage = productDetailBase.image;
        }

        updateDetailPageUI();
    }

    function updateDetailPageUI() {
        // Price
        const priceValEl = document.getElementById('detail-price-val');
        if (priceValEl) {
            priceValEl.innerText = formatMoney(activeDetailPrice).replace(' đ', '').replace(' VNĐ', '');
        }

        // Image
        const mainImgEl = document.getElementById('detail-main-image');
        if (mainImgEl && activeDetailImage) {
            mainImgEl.src = activeDetailImage.startsWith('http') ? activeDetailImage : '{{ asset("") }}' + activeDetailImage.replace(/^\/+/, '');
        }

        // Stock Badge and Add to Cart button
        const indicator = document.getElementById('detail-stock-indicator');
        const btnCart = document.getElementById('detail-add-to-cart-btn');
        const inputQty = document.getElementById('detail-qty');

        if (indicator) {
            if (activeDetailStock > 0) {
                indicator.className = "badge badge-success px-2 py-1";
                indicator.innerHTML = `<i class="fas fa-check-circle mr-1"></i> Còn ${activeDetailStock} sản phẩm trong kho`;
                if (btnCart) {
                    btnCart.disabled = false;
                    btnCart.innerHTML = `<i class="fas fa-shopping-cart mr-2"></i> Thêm vào giỏ hàng`;
                    btnCart.className = "btn btn-purple px-4 font-weight-bold";
                }
                if (parseInt(inputQty.value) < 1 || parseInt(inputQty.value) > activeDetailStock) {
                    inputQty.value = 1;
                }
            } else {
                indicator.className = "badge badge-danger px-2 py-1";
                indicator.innerHTML = `<i class="fas fa-times-circle mr-1"></i> Hết hàng`;
                if (btnCart) {
                    btnCart.disabled = true;
                    btnCart.innerHTML = `<i class="fas fa-ban mr-2"></i> Tạm hết hàng`;
                    btnCart.className = "btn btn-secondary px-4 font-weight-bold";
                }
                inputQty.value = 0;
            }
        }
    }

    function changeDetailQty(delta) {
        if (activeDetailStock <= 0) return;
        const input = document.getElementById('detail-qty');
        let val = parseInt(input.value) || 1;
        let newVal = val + delta;
        if (newVal > activeDetailStock) {
            alert(`Số lượng vượt quá số hàng có trong kho! Kho hiện chỉ còn ${activeDetailStock} sản phẩm.`);
            return;
        }
        if (newVal < 1) newVal = 1;
        input.value = newVal;
    }

    function addDetailToCart() {
        if (activeDetailStock <= 0) {
            alert('Sản phẩm đã hết hàng!');
            return;
        }
        const qty = parseInt(document.getElementById('detail-qty').value) || 1;
        addToCart(
            {{ $product->id }},
            '{{ addslashes($product->name) }}',
            activeDetailPrice,
            activeDetailImage,
            selectedDetailVariant,
            qty,
            activeDetailStock
        );
    }

    // Initialize UI on load
    document.addEventListener('DOMContentLoaded', () => {
        // Trigger first variant selection if variants list is not empty
        if (detailColors && detailColors.length > 0) {
            const firstBtn = document.querySelector('#detail-color-options .detail-variant-btn');
            if (firstBtn) {
                selectDetailVariant(firstBtn, selectedDetailVariant, 0);
            }
        } else {
            updateDetailPageUI();
        }
    });
</script>
@endsection
