<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PTShop - Mua sắm trực tuyến')</title>

    <!-- Bootstrap 4 CSS & Font Awesome Icons & Google Font Inter -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        body { 
            background-color: #f4f6f9; 
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navigation styling */
        .navbar-custom { 
            background-color: #1e293b;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 12px 0;
        }
        
        .navbar-custom .navbar-brand {
            color: #ffffff;
            font-weight: 700;
            font-size: 20px;
            display: flex;
            align-items: center;
        }

        .brand-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            margin-right: 10px;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
        }

        .navbar-custom .nav-link {
            color: #94a3b8 !important;
            font-weight: 500;
            font-size: 14.5px;
            padding: 8px 16px !important;
            transition: all 0.2s;
            border-radius: 6px;
        }

        .navbar-custom .nav-link:hover, 
        .navbar-custom .nav-link.active {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .btn-purple {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff !important;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
            transition: all 0.2s;
        }

        .btn-purple:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            box-shadow: 0 6px 12px -1px rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
        }

        .btn-outline-purple {
            border: 1px solid #6366f1;
            color: #6366f1 !important;
            font-weight: 600;
            border-radius: 8px;
            padding: 7px 18px;
            transition: all 0.2s;
        }

        .btn-outline-purple:hover {
            background: #6366f1;
            color: #ffffff !important;
        }

        main {
            flex: 1;
        }

        /* Card custom theme */
        .card-custom {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        footer {
            background: #1e293b;
            color: #94a3b8;
            font-size: 13px;
            border-top: 1px solid #334155;
        }
    </style>
</head>
<body>

    <!-- NAVBAR CHÍNH -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <span class="brand-logo-icon">P</span> PTShop
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar" 
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- MENU BÊN TRÁI -->
                <ul class="navbar-nav mr-auto">
                    {{-- 1. Menu dùng chung --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}" href="{{ route('welcome') }}">
                            <i class="fas fa-home mr-1"></i> Trang chủ
                        </a>
                    </li>

                    {{-- 2. Menu cho Khách hàng --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.categories.*') ? 'active' : '' }}" href="{{ url('/categories') }}">
                            <i class="fas fa-th-large mr-1"></i> Danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('user.cart.index') ? 'active' : '' }}" href="{{ route('user.cart.index') }}" id="cart-nav-link">
                            <i class="fas fa-shopping-cart mr-1"></i> Giỏ hàng
                            <span id="cart-count-badge" class="badge badge-pill badge-danger ml-1" style="display: none; background-color: #ef4444; font-size: 11px;">0</span>
                        </a>
                    </li>

                    @auth
                        @if(Auth::user()->role === 'customer' || Auth::user()->role === 'user')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ Route::has('user.orders.index') ? route('user.orders.index') : '#' }}">
                                    <i class="fas fa-box mr-1"></i> Lịch sử đơn hàng
                                </a>
                            </li>
                        @endif

                        {{-- 3. Menu Quản trị viên (Admin) --}}
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item dropdown ml-lg-2">
                                <a class="nav-link dropdown-toggle text-indigo font-weight-semibold" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #818cf8 !important;">
                                    <i class="fas fa-user-shield mr-1"></i> Khu vực Quản trị
                                </a>
                                <div class="dropdown-menu shadow-lg border-0 rounded-lg" aria-labelledby="adminDropdown">
                                    <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt text-indigo mr-2" style="color:#6366f1;"></i> Bảng điều khiển
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item py-2" href="{{ route('admin.categories.index') }}">
                                        <i class="fas fa-boxes text-info mr-2"></i> Quản lý Sản phẩm
                                    </a>
                                    <a class="dropdown-item py-2" href="{{ route('admin.product-categories.index') }}">
                                        <i class="fas fa-folder text-warning mr-2"></i> Quản lý Danh mục
                                    </a>
                                </div>
                            </li>
                        @endif
                    @endauth
                </ul>

                <!-- MENU BÊN PHẢI (TÀI KHOẢN) -->
                <ul class="navbar-nav ml-auto align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus mr-1"></i> Đăng ký
                            </a>
                        </li>
                        <li class="nav-item ml-lg-2">
                            <a class="btn btn-purple btn-sm px-3" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt mr-1"></i> Đăng nhập
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 32px; height: 32px; background: linear-gradient(135deg, #6366f1, #4f46e5); font-size: 13px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                {{ Auth::user()->name }}
                                <span class="badge badge-pill ml-2 px-2 py-1" style="background:#e0e7ff; color:#4338ca; font-size:11px;">
                                    {{ strtoupper(Auth::user()->role) }}
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 rounded-lg" aria-labelledby="userDropdown">
                                <div class="px-3 py-2 text-muted small border-bottom">
                                    <div class="font-weight-bold text-dark">{{ Auth::user()->name }}</div>
                                    <div class="text-truncate">{{ Auth::user()->email }}</div>
                                </div>
                                
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2 mt-1">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- NỘI DUNG TRANG CHÍNH -->
    <main class="container my-4">
        {{-- Hiển thị thông báo toàn cục --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert" style="background-color: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert" style="background-color: #fee2e2; color: #991b1b;">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- MODAL CHỌN PHÂN LOẠI SẢN PHẨM -->
    <div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-lg">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold" id="variantModalLabel">
                        <i class="fas fa-layer-group text-indigo mr-2" style="color: #818cf8;"></i> Chọn phân loại sản phẩm
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div id="vmodal-img-container"></div>
                        <div class="ml-3">
                            <h6 class="font-weight-bold text-dark mb-1" id="vmodal-name"></h6>
                            <span class="font-weight-bold h5 text-purple mb-0" id="vmodal-price" style="color: #6366f1;"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="font-weight-bold text-dark small text-uppercase mb-2">Phân loại / Màu sắc <span class="text-danger">*</span></label>
                        <div id="vmodal-variants-list" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small text-uppercase mb-2">Số lượng</label>
                        <div class="input-group" style="width: 140px;">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeVModalQty(-1)">-</button>
                            </div>
                            <input type="number" id="vmodal-qty" class="form-control text-center font-weight-bold" value="1" min="1" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeVModalQty(1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm rounded-lg" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-purple btn-sm px-4 rounded-lg font-weight-bold" onclick="confirmAddToCartFromModal()">
                        <i class="fas fa-cart-plus mr-1"></i> Xác nhận thêm vào giỏ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL GIỎ HÀNG (JAVASCRIPT) -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-lg">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold" id="cartModalLabel">
                        <i class="fas fa-shopping-cart text-indigo mr-2" style="color: #818cf8;"></i> Giỏ hàng của bạn
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" id="cart-modal-body">
                    <!-- Javascript sẽ render danh sách ở đây -->
                </div>
                <div class="modal-footer bg-light border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-lg" onclick="clearCart()">
                        <i class="fas fa-trash-alt mr-1"></i> Xóa tất cả
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm rounded-lg mr-2" data-dismiss="modal">Đóng</button>
                        <a href="{{ route('user.cart.index') }}" class="btn btn-outline-purple btn-sm rounded-lg mr-2">
                            <i class="fas fa-tasks mr-1"></i> Trang giỏ hàng
                        </a>
                        <button type="button" class="btn btn-purple btn-sm rounded-lg px-4" onclick="checkoutAlert()">
                            Thanh toán <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="py-4 mt-auto">
        <div class="container text-center">
            <span>© {{ date('Y') }} PTShop. All rights reserved. Built with Laravel & JavaScript.</span>
        </div>
    </footer>

    <!-- JavaScript Dependencies: Chỉ nạp DUY NHẤT một bộ thư viện chuẩn -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart & Variant Logic JavaScript -->
    <script>
        let currentVModalProduct = null;
        let currentVModalVariants = [];
        let selectedVariant = 'Mặc định';

        function openVariantModal(id, name, price, image, variants, stock = 99) {
            stock = (parseInt(stock) && parseInt(stock) > 0) ? parseInt(stock) : 99;
            currentVModalProduct = { id, name, price, image, stock };
            selectedVariant = 'Mặc định';

            let items = [];
            if (typeof variants === 'string') {
                try { items = JSON.parse(variants); } catch(e) { items = []; }
            } else if (Array.isArray(variants)) {
                items = variants;
            }
            currentVModalVariants = items;

            document.getElementById('vmodal-name').innerText = name;
            
            // Initial properties
            let initialPrice = price;
            let initialStock = stock;
            let initialImage = image;

            const listContainer = document.getElementById('vmodal-variants-list');
            let listHtml = '';

            if (items && items.length > 0) {
                items.forEach((item, index) => {
                    const vName = typeof item === 'object' ? (item.name || 'Màu ' + (index+1)) : item;
                    const isFirst = index === 0;
                    if (isFirst) {
                        selectedVariant = vName;
                        if (typeof item === 'object') {
                            if (item.price) initialPrice = parseFloat(item.price);
                            if (item.quantity !== undefined && item.quantity !== null && item.quantity !== '') {
                                initialStock = parseInt(item.quantity);
                            }
                            if (item.image) initialImage = item.image;
                        }
                    }
                    listHtml += `
                        <button type="button" class="btn btn-sm variant-btn mr-2 mb-2 ${isFirst ? 'btn-purple active' : 'btn-outline-secondary'}" 
                                onclick="selectVariantOption(this, '${vName.replace(/'/g, "\\'")}', ${index})">
                            <i class="fas fa-check-circle mr-1 check-icon ${isFirst ? '' : 'd-none'}"></i> ${vName}
                        </button>
                    `;
                });
            } else {
                selectedVariant = 'Tiêu chuẩn';
                listHtml = `
                    <button type="button" class="btn btn-sm btn-purple variant-btn active mr-2 mb-2">
                        <i class="fas fa-check-circle mr-1"></i> Tiêu chuẩn
                    </button>
                `;
            }

            currentVModalProduct.activePrice = initialPrice;
            currentVModalProduct.activeStock = initialStock;
            currentVModalProduct.activeImage = initialImage;

            document.getElementById('vmodal-price').innerText = formatMoney(initialPrice);
            document.getElementById('vmodal-qty').value = initialStock > 0 ? 1 : 0;

            const imgContainer = document.getElementById('vmodal-img-container');
            const activeImg = initialImage || image;
            imgContainer.innerHTML = activeImg 
                ? `<img src="${activeImg}" class="rounded shadow-sm" style="width:65px; height:65px; object-fit:cover;">`
                : `<div class="rounded bg-secondary text-white d-flex align-items-center justify-content-center" style="width:65px; height:65px;"><i class="fas fa-box fa-2x"></i></div>`;

            listHtml += `<div id="vmodal-stock-indicator" class="w-100 mt-1"></div>`;
            listContainer.innerHTML = listHtml;

            updateVModalStockIndicator();

            // Mở modal an toàn với cả jQuery và Bootstrap 4
            $('#variantModal').modal('show');
        }

        function updateVModalStockIndicator() {
            const indicator = document.getElementById('vmodal-stock-indicator');
            if (!indicator || !currentVModalProduct) return;
            
            const stock = currentVModalProduct.activeStock;
            if (stock > 0) {
                indicator.className = "w-100 text-success small font-weight-bold mt-1";
                indicator.innerHTML = `<i class="fas fa-check-circle mr-1"></i> Còn ${stock} sản phẩm trong kho`;
            } else {
                indicator.className = "w-100 text-danger small font-weight-bold mt-1";
                indicator.innerHTML = `<i class="fas fa-times-circle mr-1"></i> Sản phẩm tạm thời hết hàng`;
            }
        }

        function selectVariantOption(btn, variantName, index) {
            document.querySelectorAll('#vmodal-variants-list .variant-btn').forEach(b => {
                b.classList.remove('btn-purple', 'active');
                b.classList.add('btn-outline-secondary');
                const icon = b.querySelector('.check-icon');
                if (icon) icon.classList.add('d-none');
            });

            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-purple', 'active');
            const icon = btn.querySelector('.check-icon');
            if (icon) icon.classList.remove('d-none');

            selectedVariant = variantName;

            if (currentVModalVariants && currentVModalVariants[index]) {
                const item = currentVModalVariants[index];
                let price = currentVModalProduct.price;
                let stock = currentVModalProduct.stock;
                let image = currentVModalProduct.image;

                if (typeof item === 'object') {
                    if (item.price) price = parseFloat(item.price);
                    if (item.quantity !== undefined && item.quantity !== null && item.quantity !== '') {
                        stock = parseInt(item.quantity);
                    }
                    if (item.image) image = item.image;
                }

                currentVModalProduct.activePrice = price;
                currentVModalProduct.activeStock = stock;
                currentVModalProduct.activeImage = image;

                document.getElementById('vmodal-price').innerText = formatMoney(price);
                const input = document.getElementById('vmodal-qty');
                input.value = stock > 0 ? 1 : 0;

                const imgContainer = document.getElementById('vmodal-img-container');
                const activeImg = image || currentVModalProduct.image;
                imgContainer.innerHTML = activeImg 
                    ? `<img src="${activeImg}" class="rounded shadow-sm" style="width:65px; height:65px; object-fit:cover;">`
                    : `<div class="rounded bg-secondary text-white d-flex align-items-center justify-content-center" style="width:65px; height:65px;"><i class="fas fa-box fa-2x"></i></div>`;

                updateVModalStockIndicator();
            }
        }

        function changeVModalQty(delta) {
            if (!currentVModalProduct) return;
            const stock = currentVModalProduct.activeStock !== undefined ? currentVModalProduct.activeStock : currentVModalProduct.stock;
            if (stock <= 0) return;
            const input = document.getElementById('vmodal-qty');
            let val = parseInt(input.value) || 1;
            let newVal = val + delta;
            
            if (newVal > stock) {
                alert(`Số lượng vượt quá số hàng có trong kho! Kho hiện chỉ còn ${stock} sản phẩm.`);
                return;
            }
            if (newVal < 1) newVal = 1;
            input.value = newVal;
        }

        function confirmAddToCartFromModal() {
            if (!currentVModalProduct) return;
            const stock = currentVModalProduct.activeStock !== undefined ? currentVModalProduct.activeStock : currentVModalProduct.stock;
            const price = currentVModalProduct.activePrice !== undefined ? currentVModalProduct.activePrice : currentVModalProduct.price;
            const image = currentVModalProduct.activeImage !== undefined ? currentVModalProduct.activeImage : currentVModalProduct.image;

            if (stock <= 0) {
                alert('Sản phẩm đã hết hàng!');
                return;
            }
            const qty = parseInt(document.getElementById('vmodal-qty').value) || 1;
            addToCart(
                currentVModalProduct.id,
                currentVModalProduct.name,
                price,
                image,
                selectedVariant,
                qty,
                stock
            );
            $('#variantModal').modal('hide');
        }

        function getCart() {
            return JSON.parse(localStorage.getItem('ptshop_cart')) || [];
        }

        function saveCart(cart) {
            localStorage.setItem('ptshop_cart', JSON.stringify(cart));
            updateCartBadge();
            renderCartModal();
        }

        function updateCartBadge() {
            const cart = getCart();
            const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-count-badge');
            if (badge) {
                if (totalCount > 0) {
                    badge.innerText = totalCount;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        function addToCart(id, name, price, image, variant = 'Mặc định', qty = 1, stock = 99) {
            stock = (parseInt(stock) && parseInt(stock) > 0) ? parseInt(stock) : 99;
            let cart = getCart();
            let itemKey = id + '_' + variant;
            let existingItem = cart.find(item => item.itemKey === itemKey || (item.id == id && (item.variant || 'Mặc định') === variant));
            
            let currentQty = existingItem ? existingItem.quantity : 0;
            if (currentQty + qty > stock) {
                alert(`Không thể thêm! Số lượng trong giỏ (${currentQty + qty}) vượt quá số hàng có trong kho (tối đa ${stock} sản phẩm).`);
                return;
            }

            if (existingItem) {
                existingItem.quantity += qty;
                existingItem.stock = stock;
            } else {
                cart.push({
                    id: id,
                    itemKey: itemKey,
                    name: name,
                    price: parseFloat(price),
                    image: image,
                    variant: variant,
                    quantity: qty,
                    stock: stock,
                    selected: true
                });
            }
            saveCart(cart);
            showToastNotification(`Đã thêm "${name}" (${variant}) vào giỏ hàng!`);
        }

        function changeCartQuantity(itemKey, delta) {
            let cart = getCart();
            let item = cart.find(i => (i.itemKey && i.itemKey == itemKey) || i.id == itemKey);
            if (item) {
                if (delta > 0 && item.stock && item.quantity + delta > item.stock) {
                    alert(`Không thể tăng thêm! Số lượng tồn kho tối đa là ${item.stock} sản phẩm.`);
                    return;
                }
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => String(i.itemKey || i.id) !== String(itemKey));
                }
                saveCart(cart);
            }
        }

        function removeFromCart(itemKey) {
            let cart = getCart();
            cart = cart.filter(i => String(i.itemKey || i.id) !== String(itemKey));
            saveCart(cart);
            showToastNotification('Đã xóa sản phẩm khỏi giỏ hàng.');
        }

        function clearCart() {
            if (confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')) {
                localStorage.removeItem('ptshop_cart');
                updateCartBadge();
                renderCartModal();
                showToastNotification('Đã làm trống giỏ hàng.');
            }
        }

        function checkoutAlert() {
            const cart = getCart();
            if (cart.length === 0) {
                alert('Giỏ hàng của bạn đang trống!');
                return;
            }
            window.location.href = "{{ route('user.checkout') }}";
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
        }

        function renderCartModal() {
            const cart = getCart();
            const modalBody = document.getElementById('cart-modal-body');
            if (!modalBody) return;

            if (cart.length === 0) {
                modalBody.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-shopping-cart fa-3x mb-3 text-secondary opacity-50"></i>
                        <p class="h6 mb-1 font-weight-bold">Giỏ hàng của bạn đang trống</p>
                        <p class="small">Hãy chọn thêm sản phẩm từ trang chủ để tiếp tục mua sắm.</p>
                    </div>
                `;
                return;
            }

            let total = 0;
            let html = `
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Phân loại</th>
                                <th>Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-right">Thành tiền</th>
                                <th class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                const key = item.itemKey || item.id;
                const imgTag = item.image 
                    ? `<img src="${item.image}" class="rounded mr-2" style="width:45px; height:45px; object-fit:cover;">`
                    : `<div class="rounded mr-2 bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:45px; height:45px;"><i class="fas fa-box"></i></div>`;

                html += `
                    <tr class="border-bottom">
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                ${imgTag}
                                <div>
                                    <span class="font-weight-bold text-dark small d-block">${item.name}</span>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <span class="badge badge-light border text-muted px-2 py-1">${item.variant || 'Mặc định'}</span>
                        </td>
                        <td class="align-middle text-muted small">${formatMoney(item.price)}</td>
                        <td class="align-middle text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary py-0 px-2" onclick="changeCartQuantity('${key}', -1)">-</button>
                                <span class="btn btn-light py-0 px-3 disabled font-weight-bold">${item.quantity}</span>
                                <button type="button" class="btn btn-outline-secondary py-0 px-2" onclick="changeCartQuantity('${key}', 1)">+</button>
                            </div>
                        </td>
                        <td class="align-middle text-right font-weight-bold text-indigo" style="color: #6366f1;">${formatMoney(subtotal)}</td>
                        <td class="align-middle text-center">
                            <button class="btn btn-sm text-danger border-0 bg-transparent" onclick="removeFromCart('${key}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <span class="font-weight-bold h6 mb-0 text-dark">Tổng tiền thanh toán:</span>
                    <span class="font-weight-bold h4 mb-0 text-purple" style="color: #6366f1;">${formatMoney(total)}</span>
                </div>
            `;

            modalBody.innerHTML = html;
        }

        function showToastNotification(message) {
            let toast = document.getElementById('cart-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'cart-toast';
                toast.style.cssText = `
                    position: fixed;
                    bottom: 25px;
                    right: 25px;
                    background: #1e293b;
                    color: #ffffff;
                    padding: 12px 20px;
                    border-radius: 10px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
                    border-left: 5px solid #6366f1;
                    z-index: 9999;
                    font-size: 14px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    opacity: 0;
                    transform: translateY(20px);
                `;
                document.body.appendChild(toast);
            }
            toast.innerHTML = `<i class="fas fa-check-circle" style="color:#10b981; font-size: 18px;"></i> <span>${message}</span>`;
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';

            clearTimeout(toast.timeoutId);
            toast.timeoutId = setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCartBadge();
            renderCartModal();
        });
    </script>

    @auth
    <style>
        #chat-box {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }

        #chat-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-width: 210px;
            height: 64px;
            padding: 0 18px 0 12px;
            border: none;
            border-radius: 18px 18px 18px 0;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            box-shadow: 0 14px 24px rgba(15, 23, 42, 0.28);
            font-size: 16px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        #chat-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 28px rgba(15, 23, 42, 0.32);
        }

        #chat-toggle .chat-avatar {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            color: #ffffff;
            font-size: 18px;
            box-shadow: inset 0 0 0 2px rgba(255,255,255,0.18);
        }

        #chat-toggle .chat-label {
            font-size: 15px;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        #chat-popup {
            width: 330px;
            height: 430px;
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        #chat-messages {
            flex: 1;
            overflow-y: auto;
            max-height: 300px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .message-row {
            padding: 8px 12px;
            border-radius: 12px;
            max-width: 85%;
            font-size: 13.5px;
            line-height: 1.4;
            word-break: break-word;
        }

        .user-msg {
            margin-left: auto;
            background-color: #6366f1;
            color: #ffffff;
            border-bottom-right-radius: 2px;
        }

        .admin-msg {
            margin-right: auto;
            background-color: #f1f5f9;
            color: #1e293b;
            border-bottom-left-radius: 2px;
        }
    </style>

    <div id="chat-box">
        <button id="chat-toggle" type="button" class="shadow">
            <span class="chat-avatar">
                <i class="fas fa-comments"></i>
            </span>
            <span class="chat-label">Trò chuyện</span>
        </button>
        <div id="chat-popup" class="card shadow-lg" style="display:none;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                <span class="font-weight-bold"><i class="fas fa-headset mr-1"></i> Hỗ trợ khách hàng</span>
                <button id="chat-close" class="btn btn-sm btn-light py-0 px-2 font-weight-bold">X</button>
            </div>
            <div id="chat-messages" class="card-body">
                <small class="text-muted">Đang tải lịch sử...</small>
            </div>
            <div class="card-footer bg-white p-2">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Nhập tin nhắn..." autocomplete="off">
                    <div class="input-group-append">
                        <button id="send-btn" class="btn btn-success btn-sm px-3">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("chat-toggle");
        const chatPopup = document.getElementById("chat-popup");
        const closeBtn = document.getElementById("chat-close");
        const sendBtn = document.getElementById("send-btn");
        const input = document.getElementById("chat-input");
        const chatBox = document.getElementById("chat-messages");

        if (!toggleBtn) return;

        // --- MỞ / ĐÓNG CHAT ---
        toggleBtn.onclick = () => {
            chatPopup.style.display = "block";
            toggleBtn.style.display = "none";
            loadMessages();
        };

        closeBtn.onclick = () => {
            chatPopup.style.display = "none";
            toggleBtn.style.display = "block";
        };

        // --- LOAD TIN NHẮN ---
        function loadMessages() {
            fetch("{{ route('user.chat.messages') }}")
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    if(messages.length === 0) {
                        html = "<div class='text-center text-muted my-auto'><small>Bắt đầu cuộc trò chuyện với Admin</small></div>";
                    }
                    messages.forEach(msg => {
                        const isMe = msg.sender_id == "{{ Auth::id() }}";
                        html += `
                            <div class="message-row ${isMe ? 'user-msg' : 'admin-msg'}">
                                <strong>${isMe ? 'Bạn' : 'Admin'}:</strong> ${msg.content}
                            </div>
                        `;
                    });
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(err => console.error("Lỗi tải tin nhắn:", err));
        }

        // --- GỬI TIN NHẮN ---
        function sendMessage() {
            let message = input.value.trim();
            if (message === "") return;

            input.disabled = true;
            sendBtn.disabled = true;

            fetch("{{ route('user.chat.send') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                input.value = "";
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
                loadMessages();
            })
            .catch(err => {
                console.error("Lỗi gửi tin:", err);
                input.disabled = false;
                sendBtn.disabled = false;
            });
        }

        sendBtn.onclick = sendMessage;

        input.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                sendMessage();
            }
        });

        // --- AUTO REFRESH (3 giây/lần) ---
        setInterval(() => {
            if (chatPopup.style.display === "block") {
                loadMessages();
            }
        }, 3000);
    });
    </script>
    @endauth

    @yield('scripts')
</body>
</html>