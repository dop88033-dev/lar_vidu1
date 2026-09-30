@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn - PTShop')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white shadow-sm border rounded-lg">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" style="color: #6366f1;">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-shopping-cart text-indigo mr-2" style="color: #6366f1;"></i> Giỏ hàng của bạn
        </h3>
        <span class="text-muted small" id="cart-item-summary">Đang tải giỏ hàng...</span>
    </div>

    <div class="row">
        <!-- Bảng Giỏ hàng với Checkbox -->
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

                <div class="card-body p-0" id="cart-page-body">
                    <!-- JavaScript sẽ render danh sách giỏ hàng kèm Checkbox ở đây -->
                </div>
            </div>
        </div>

        <!-- Tóm tắt đơn hàng & Nút Thanh toán -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm rounded-lg sticky-top" style="top: 90px;">
                <div class="card-header bg-dark text-white font-weight-bold py-3">
                    <i class="fas fa-file-invoice-dollar mr-2 text-indigo" style="color: #818cf8;"></i> Tóm tắt đơn hàng
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Số sản phẩm đã chọn:</span>
                        <span class="font-weight-bold text-dark" id="summary-selected-qty">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Phí vận chuyển:</span>
                        <span class="text-success font-weight-bold">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="font-weight-bold text-dark h6 mb-0">Tổng thanh toán:</span>
                        <span class="font-weight-bold h4 mb-0 text-purple" id="summary-total-price" style="color: #6366f1;">0 đ</span>
                    </div>

                    <button type="button" class="btn btn-purple btn-block py-3 font-weight-bold rounded-lg shadow-sm" onclick="goToCheckout()">
                        <i class="fas fa-credit-card mr-2"></i> Tiến hành Thanh toán
                    </button>
                    <a href="{{ route('welcome') }}" class="btn btn-outline-secondary btn-block mt-2 rounded-lg">
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
        const container = document.getElementById('cart-page-body');
        const summaryText = document.getElementById('cart-item-summary');
        
        if (!container) return;

        if (cart.length === 0) {
            summaryText.innerText = 'Giỏ hàng rỗng (0 sản phẩm)';
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-shopping-bag fa-4x mb-3 text-secondary opacity-50"></i>
                    <h5 class="font-weight-bold text-dark">Giỏ hàng của bạn đang trống</h5>
                    <p class="small text-muted mb-4">Chưa có sản phẩm nào được thêm vào giỏ hàng.</p>
                    <a href="{{ route('welcome') }}" class="btn btn-purple px-4 rounded-lg">Khám phá sản phẩm ngay</a>
                </div>
            `;
            updateSelectedSummary();
            return;
        }

        summaryText.innerText = `Có tổng cộng ${cart.length} mặt hàng trong giỏ`;

        let html = `<div class="table-responsive"><table class="table align-middle mb-0"><thead class="bg-light text-muted small text-uppercase"><tr>
            <th class="text-center" style="width: 50px;">Chọn</th>
            <th>Sản phẩm</th>
            <th>Phân loại</th>
            <th>Đơn giá</th>
            <th class="text-center">Số lượng</th>
            <th class="text-right">Thành tiền</th>
            <th class="text-center">Hành động</th>
        </tr></thead><tbody>`;

        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            const checked = item.selected !== false ? 'checked' : '';
            const key = item.itemKey || item.id;
            const imgTag = item.image 
                ? `<img src="${item.image}" class="rounded mr-3" style="width:50px; height:50px; object-fit:cover;">`
                : `<div class="rounded mr-3 bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:50px; height:50px;"><i class="fas fa-box"></i></div>`;

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
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary py-0 px-2" onclick="updateCartItemQty('${key}', -1)">-</button>
                            <span class="btn btn-light py-0 px-3 disabled font-weight-bold">${item.quantity}</span>
                            <button type="button" class="btn btn-outline-secondary py-0 px-2" onclick="updateCartItemQty('${key}', 1)">+</button>
                        </div>
                    </td>
                    <td class="align-middle text-right font-weight-bold text-indigo" style="color: #6366f1;">${formatMoney(subtotal)}</td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-sm text-danger border-0 bg-transparent" onclick="removeSingleCartItem('${key}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.innerHTML = html;

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

        document.getElementById('selected-count').innerText = selectedItems.length;
        document.getElementById('summary-selected-qty').innerText = totalQty + ' sản phẩm';
        document.getElementById('summary-total-price').innerText = formatMoney(totalPrice);
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
            showToastNotification('Đã xóa các sản phẩm được chọn.');
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
