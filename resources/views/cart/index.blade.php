@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn - PTShop')

@section('content')
<div class="container py-3">
    <!-- Breadcrumb tối giản -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0 m-0 small">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-muted"><i class="fas fa-home mr-1"></i> Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>

    <!-- Tiêu đề trang -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <h3 class="font-weight-bold text-dark mb-1 d-flex align-items-center">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle mr-2 shadow-sm" 
                  style="width: 40px; height: 40px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4338ca;">
                <i class="fas fa-shopping-cart" style="font-size: 18px;"></i>
            </span>
            Giỏ hàng của bạn
        </h3>
        <span class="badge badge-pill badge-light border text-muted px-3 py-2 font-weight-medium" id="cart-item-summary">
            Đang tải dữ liệu...
        </span>
    </div>

    <!-- 1. GIAO DIỆN KHI GIỎ HÀNG TRỐNG (ẨN MẶC ĐỊNH, JS SẼ BẬT LÊN) -->
    <div id="cart-empty-view" style="display: none;">
        <div class="row justify-content-center py-5">
            <div class="col-md-7 col-lg-6 text-center">
                <div class="card card-custom border-0 shadow-sm p-4 p-md-5 text-center">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-4 shadow-sm" 
                         style="width: 90px; height: 90px; background: linear-gradient(135deg, #e0e7ff, #ede9fe); color: #6366f1;">
                        <i class="fas fa-shopping-bag" style="font-size: 40px;"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-2">Giỏ hàng của bạn đang trống</h4>
                    <p class="text-muted small mb-4 px-md-3">
                        Bạn chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá ngay các sản phẩm thể thao và phụ kiện chất lượng tại PTShop nhé!
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center" style="gap: 12px;">
                        <a href="{{ route('user.categories.index') }}" class="btn btn-purple px-4 py-2 font-weight-bold shadow-sm">
                            <i class="fas fa-th-large mr-2"></i> Khám phá sản phẩm ngay
                        </a>
                        <a href="{{ route('welcome') }}" class="btn btn-outline-secondary px-4 py-2 font-weight-bold">
                            <i class="fas fa-home mr-1"></i> Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. GIAO DIỆN KHI GIỎ HÀNG CÓ SẢN PHẨM -->
    <div class="row" id="cart-content-view" style="display: none;">
        <!-- Cột trái: Danh sách Sản phẩm -->
        <div class="col-lg-8 mb-4">
            <div class="card card-custom border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                        <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="selectAllCheckbox">
                            Chọn tất cả (<span id="selected-count">0</span> sản phẩm)
                        </label>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-lg" onclick="removeSelectedCartItems()">
                        <i class="fas fa-trash-alt mr-1"></i> Xóa mục đã chọn
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="text-center" style="width: 50px;">Chọn</th>
                                    <th>Sản phẩm</th>
                                    <th>Phân loại</th>
                                    <th>Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right">Thành tiền</th>
                                    <th class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items-tbody">
                                <!-- JS render các hàng sản phẩm ở đây -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Tóm tắt đơn hàng & Nút Thanh toán -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm rounded-lg sticky-top" style="top: 85px;">
                <div class="card-header text-white font-weight-bold py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <i class="fas fa-file-invoice-dollar mr-2" style="color: #818cf8;"></i> Tóm tắt đơn hàng
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Số sản phẩm đã chọn:</span>
                        <span class="font-weight-bold text-dark" id="summary-selected-qty">0 sản phẩm</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Phí vận chuyển tạm tính:</span>
                        <span class="text-success font-weight-bold">Tính khi thanh toán</span>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="font-weight-bold text-dark mb-0">Tổng thanh toán:</span>
                        <span class="font-weight-bold h4 mb-0" id="summary-total-price" style="color: #6366f1;">0 đ</span>
                    </div>

                    <button type="button" class="btn btn-purple btn-block py-3 font-weight-bold rounded-lg shadow-sm" id="btn-proceed-checkout" onclick="goToCheckout()">
                        <i class="fas fa-credit-card mr-2"></i> Tiến hành Thanh toán
                    </button>
                    <a href="{{ route('welcome') }}" class="btn btn-outline-secondary btn-block mt-2 rounded-lg font-weight-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Tiếp tục mua hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function renderCartPage() {
        const cart = getCart();
        const emptyView = document.getElementById('cart-empty-view');
        const contentView = document.getElementById('cart-content-view');
        const summaryBadge = document.getElementById('cart-item-summary');
        const tbody = document.getElementById('cart-items-tbody');

        if (!emptyView || !contentView) return;

        // Trường hợp 1: Giỏ hàng RỖNG -> Chỉ hiện khung Empty State đẹp mắt
        if (cart.length === 0) {
            emptyView.style.display = 'block';
            contentView.style.display = 'none';
            if (summaryBadge) {
                summaryBadge.innerText = '0 sản phẩm trong giỏ';
            }
            return;
        }

        // Trường hợp 2: Có sản phẩm -> Hiện bảng và cột thanh toán
        emptyView.style.display = 'none';
        contentView.style.display = 'flex';

        if (summaryBadge) {
            summaryBadge.innerText = `${cart.length} mặt hàng trong giỏ`;
        }

        let html = '';
        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            const checked = item.selected !== false ? 'checked' : '';
            const key = item.itemKey || item.id;
            const imgTag = item.image 
                ? `<img src="${item.image}" class="rounded mr-3 shadow-sm border" style="width:52px; height:52px; object-fit:cover;">`
                : `<div class="rounded mr-3 bg-secondary text-white d-inline-flex align-items-center justify-content-center shadow-sm" style="width:52px; height:52px;"><i class="fas fa-box"></i></div>`;

            html += `
                <tr class="border-bottom">
                    <td class="align-middle text-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input item-checkbox" id="cart_check_${index}" data-key="${key}" ${checked} onchange="onItemCheckChange('${key}', this.checked)">
                            <label class="custom-control-label cursor-pointer" for="cart_check_${index}"></label>
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            ${imgTag}
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">${item.name}</h6>
                                <span class="text-muted small">Mã SP: #${item.id}</span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <span class="badge badge-light border text-dark font-weight-medium px-2 py-1">${item.variant || 'Mặc định'}</span>
                    </td>
                    <td class="align-middle font-weight-medium">${formatMoney(item.price)}</td>
                    <td class="align-middle text-center">
                        <div class="btn-group btn-group-sm border rounded">
                            <button type="button" class="btn btn-light py-1 px-2 border-0" onclick="updateCartItemQty('${key}', -1)"><i class="fas fa-minus small"></i></button>
                            <span class="btn btn-white py-1 px-3 disabled font-weight-bold text-dark">${item.quantity}</span>
                            <button type="button" class="btn btn-light py-1 px-2 border-0" onclick="updateCartItemQty('${key}', 1)"><i class="fas fa-plus small"></i></button>
                        </div>
                    </td>
                    <td class="align-middle text-right font-weight-bold" style="color: #6366f1;">${formatMoney(subtotal)}</td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-sm text-danger border-0 bg-transparent" title="Xóa món này" onclick="removeSingleCartItem('${key}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        if (tbody) {
            tbody.innerHTML = html;
        }

        checkSelectAllState();
        updateSelectedSummary();
    }

    function onItemCheckChange(key, isChecked) {
        let cart = getCart();
        let item = cart.find(i => (i.itemKey && i.itemKey == key) || i.id == key);
        if (item) {
            item.selected = isChecked;
            saveCart(cart);
        }
        checkSelectAllState();
        updateSelectedSummary();
    }

    function toggleSelectAll(selectAllCheckbox) {
        const isChecked = selectAllCheckbox.checked;
        let cart = getCart();
        cart.forEach(item => {
            item.selected = isChecked;
        });
        saveCart(cart);
        renderCartPage();
    }

    function checkSelectAllState() {
        const cart = getCart();
        const selectAllCB = document.getElementById('selectAllCheckbox');
        if (!selectAllCB || cart.length === 0) return;
        const allSelected = cart.every(item => item.selected !== false);
        selectAllCB.checked = allSelected;
    }

    function updateSelectedSummary() {
        const cart = getCart();
        const selectedItems = cart.filter(item => item.selected !== false);
        const totalQty = selectedItems.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = selectedItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        const selCountEl = document.getElementById('selected-count');
        const selQtyEl = document.getElementById('summary-selected-qty');
        const totalEl = document.getElementById('summary-total-price');
        const btnCheckout = document.getElementById('btn-proceed-checkout');

        if (selCountEl) selCountEl.innerText = selectedItems.length;
        if (selQtyEl) selQtyEl.innerText = `${totalQty} sản phẩm`;
        if (totalEl) totalEl.innerText = formatMoney(totalPrice);

        if (btnCheckout) {
            if (selectedItems.length === 0) {
                btnCheckout.classList.add('opacity-75');
            } else {
                btnCheckout.classList.remove('opacity-75');
            }
        }
    }

    function updateCartItemQty(key, delta) {
        changeCartQuantity(key, delta);
        renderCartPage();
    }

    function removeSingleCartItem(key) {
        removeFromCart(key);
        renderCartPage();
    }

    function removeSelectedCartItems() {
        let cart = getCart();
        const selectedCount = cart.filter(i => i.selected !== false).length;
        if (selectedCount === 0) {
            alert('Bạn chưa chọn sản phẩm nào để xóa!');
            return;
        }
        if (confirm(`Bạn có chắc chắn muốn xóa ${selectedCount} sản phẩm đã chọn khỏi giỏ hàng?`)) {
            cart = cart.filter(i => i.selected === false);
            saveCart(cart);
            renderCartPage();
            if (typeof showToastNotification === 'function') {
                showToastNotification('Đã xóa các sản phẩm được chọn.');
            }
        }
    }

    function goToCheckout() {
        const cart = getCart();
        const selectedItems = cart.filter(item => item.selected !== false);
        if (selectedItems.length === 0) {
            alert('Vui lòng tích chọn ít nhất 1 sản phẩm để tiến hành thanh toán!');
            return;
        }
        window.location.href = "{{ route('user.checkout') }}";
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderCartPage();
    });
</script>
@endsection
