@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng - PTShop')

@section('content')
<div class="container py-4">
    <!-- SEO & Navigation Header -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white shadow-sm border rounded-lg">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-primary font-weight-bold"><i class="fas fa-home mr-1"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.cart.index') }}" class="text-primary font-weight-bold"><i class="fas fa-shopping-cart mr-1"></i> Giỏ hàng</a></li>
            <li class="breadcrumb-item active font-weight-semibold" aria-current="page">Thanh toán đơn hàng</li>
        </ol>
    </nav>

    <!-- User Auth Status Notice -->
    @auth
        <div class="alert alert-indigo border-0 shadow-sm rounded-lg d-flex align-items-center justify-content-between p-3 mb-4" style="background: linear-gradient(135deg, #e0e7ff, #eef2ff); color: #3730a3;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                    <i class="fas fa-user-check font-size-18"></i>
                </div>
                <div>
                    <h6 class="font-weight-bold mb-1">Tài khoản: {{ Auth::user()->name }} ({{ Auth::user()->email }})</h6>
                    <small class="text-muted">
                        Trạng thái xác thực email: 
                        @if(Auth::user()->hasVerifiedEmail())
                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Đã xác thực</span>
                        @else
                            <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i> Chưa xác thực</span>
                        @endif
                    </small>
                </div>
            </div>
            @if(!Auth::user()->hasVerifiedEmail())
                <a href="{{ route('verification.notice') }}" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
                    Xác thực ngay <i class="fas fa-arrow-right ml-1"></i>
                </a>
            @endif
        </div>
    @endauth

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-lg shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show rounded-lg shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-lg shadow-sm mb-4" role="alert">
            <strong class="d-block mb-1"><i class="fas fa-exclamation-triangle mr-2"></i> Vui lòng kiểm tra lại thông tin:</strong>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Form nhập thông tin & Chọn phương thức thanh toán -->
        <div class="col-lg-7 mb-4">
            <form action="{{ route('user.payment.process') }}" method="POST" id="checkout-form" onsubmit="return onProcessCheckoutSubmit(event)">
                @csrf
                <input type="hidden" name="cart_items" id="cart-items-input">

                <!-- 1. Thông tin giao hàng GHN -->
                <div class="card card-custom border-0 shadow-sm rounded-lg mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-map-marker-alt text-indigo mr-2" style="color: #6366f1;"></i> 1. Thông tin giao hàng GHN
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-bold text-dark">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control rounded-lg" value="{{ old('name', Auth::user()->name ?? '') }}" required placeholder="Nhập đầy đủ họ và tên">
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="font-weight-bold text-dark">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control rounded-lg" value="{{ old('phone') }}" required placeholder="Ví dụ: 0912345678" pattern="^0(3|5|7|8|9)\d{8}$">
                                <small class="text-muted">Định dạng: 10 chữ số bắt đầu bằng 03, 05, 07, 08, 09</small>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-dark">Địa chỉ Email</label>
                                <input type="email" class="form-control rounded-lg bg-light" value="{{ Auth::user()->email ?? '' }}" readonly>
                            </div>
                        </div>

                        <!-- Combobox Địa chỉ GHN -->
                        <div class="form-row mb-3">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="province_select" class="font-weight-bold text-dark">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                <select id="province_select" class="form-control rounded-lg" required>
                                    <option value="">-- Chọn Tỉnh/Thành --</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label for="district_select" class="font-weight-bold text-dark">Quận / Huyện <span class="text-danger">*</span></label>
                                <select id="district_select" class="form-control rounded-lg" required disabled>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ward_select" class="font-weight-bold text-dark">Phường / Xã <span class="text-danger">*</span></label>
                                <select id="ward_select" class="form-control rounded-lg" required disabled>
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="street_address" class="font-weight-bold text-dark">Số nhà, Tên đường <span class="text-danger">*</span></label>
                            <input type="text" id="street_address" class="form-control rounded-lg" required placeholder="Ví dụ: Số 123 đường Nguyễn Trãi">
                        </div>

                        <!-- Hidden fields passed to backend -->
                        <input type="hidden" name="address" id="full_address_input" required>
                        <input type="hidden" name="to_district_id" id="to_district_id_input">
                        <input type="hidden" name="to_ward_code" id="to_ward_code_input">

                        <!-- Full Address Preview -->
                        <div id="address-preview" class="alert alert-secondary border d-none p-3 mb-3 rounded-lg small" style="background-color: #f8fafc; color: #334155; border-left: 4px solid #6366f1 !important;">
                            <i class="fas fa-map-marker-alt text-indigo mr-1" style="color: #6366f1;"></i> 
                            <span class="font-weight-bold">Địa chỉ giao hàng hoàn chỉnh:</span> 
                            <span id="address-preview-text" class="text-dark font-weight-bold"></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Chọn Phương Thức Thanh Toán -->
                <div class="card card-custom border-0 shadow-sm rounded-lg mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-wallet text-indigo mr-2" style="color: #6366f1;"></i> 2. Chọn phương thức thanh toán
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Option COD -->
                        <div class="custom-control custom-radio border rounded-lg p-3 mb-3 payment-option active cursor-pointer" style="background-color: #f8fafc; transition: all 0.2s;" id="opt-cod" onclick="selectPaymentMethod('cod')">
                            <input type="radio" id="payment_cod" name="payment_method" value="cod" class="custom-control-input" checked onchange="togglePaymentMethod('cod')">
                            <label class="custom-control-label font-weight-bold text-dark cursor-pointer d-flex align-items-center justify-content-between" for="payment_cod">
                                <span>
                                    <i class="fas fa-truck text-success mr-2 font-size-18"></i> 
                                    Thanh toán khi nhận hàng (COD)
                                </span>
                                <span class="badge badge-light border">Miễn phí giao hàng COD</span>
                            </label>
                            <div class="pl-4 mt-2 text-muted small" id="cod-detail">
                                <i class="fas fa-info-circle text-info mr-1"></i> Bạn sẽ thanh toán trực tiếp cho nhân viên giao hàng GHN khi nhận sản phẩm.
                            </div>
                        </div>

                        <!-- Option 1: Thanh toán bằng thẻ ATM -->
                        <div class="custom-control custom-radio border rounded-lg p-3 mb-3 payment-option cursor-pointer" style="background-color: #ffffff; transition: all 0.2s;" id="opt-momo-atm" onclick="selectPaymentMethod('momo_atm')">
                            <input type="radio" id="payment_momo_atm" name="payment_method" value="momo_atm" class="custom-control-input" onchange="togglePaymentMethod('momo_atm')">
                            <label class="custom-control-label font-weight-bold text-dark cursor-pointer d-flex align-items-center justify-content-between" for="payment_momo_atm">
                                <span>
                                    <i class="fas fa-credit-card text-primary mr-2 font-size-18"></i> 
                                    Thanh toán bằng thẻ ATM
                                </span>
                                <span class="badge text-white px-2 py-1" style="background-color: #005baa;">Thẻ Nội Địa Napas (MoMo)</span>
                            </label>
                            <div class="pl-4 mt-3 d-none" id="momo-atm-detail">
                                <div class="bg-light p-3 rounded-lg border">
                                    <p class="font-weight-bold text-dark mb-2">
                                        <i class="fas fa-university text-primary mr-1"></i> Cổng thanh toán Thẻ ATM Nội Địa Napas (MoMo Sandbox).
                                    </p>
                                    <div class="text-muted small">
                                        Hệ thống sẽ chuyển tiếp bạn sang giao diện thanh toán an toàn của MoMo để thực hiện giao dịch.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Option 2: Thanh toán bằng VISA/Master/JCB -->
                        <div class="custom-control custom-radio border rounded-lg p-3 mb-3 payment-option cursor-pointer" style="background-color: #ffffff; transition: all 0.2s;" id="opt-momo-cc" onclick="selectPaymentMethod('momo_cc')">
                            <input type="radio" id="payment_momo_cc" name="payment_method" value="momo_cc" class="custom-control-input" onchange="togglePaymentMethod('momo_cc')">
                            <label class="custom-control-label font-weight-bold text-dark cursor-pointer d-flex align-items-center justify-content-between" for="payment_momo_cc">
                                <span>
                                    <i class="fab fa-cc-visa text-danger mr-1 font-size-18"></i> 
                                    <i class="fab fa-cc-mastercard text-warning mr-2 font-size-18"></i>
                                    Thanh toán bằng VISA/Master/JCB
                                </span>
                                <span class="badge text-white px-2 py-1" style="background-color: #a50064;">Thẻ Quốc Tế (MoMo)</span>
                            </label>
                            <div class="pl-4 mt-3 d-none" id="momo-cc-detail">
                                <div class="bg-light p-3 rounded-lg border">
                                    <p class="font-weight-bold text-dark mb-2">
                                        <i class="fas fa-globe text-danger mr-1"></i> Cổng thanh toán Thẻ Quốc Tế Visa/Mastercard/JCB (MoMo Sandbox).
                                    </p>
                                    <div class="text-muted small">
                                        Hệ thống sẽ chuyển tiếp bạn sang giao diện thanh toán an toàn của MoMo để thực hiện giao dịch.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="btn-submit-order" class="btn btn-purple btn-block py-3 font-weight-bold rounded-lg shadow-lg text-uppercase">
                    <i class="fas fa-check-circle mr-2"></i> Xác nhận Đặt hàng & Thanh toán
                </button>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-5">
            <div class="card card-custom border-0 shadow-sm rounded-lg sticky-top" style="top: 90px;">
                <div class="card-header bg-dark text-white font-weight-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-shopping-basket text-indigo mr-2" style="color: #818cf8;"></i> Tóm tắt đơn hàng</span>
                    <a href="{{ route('user.cart.index') }}" class="btn btn-sm btn-outline-light py-0">Sửa giỏ hàng</a>
                </div>
                <div class="card-body p-4">
                    <div id="checkout-items-list">
                        @if(!empty($cart))
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($cart as $item)
                                    <li class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between border-bottom">
                                        <div class="d-flex align-items-center">
                                            @if(!empty($item['image']))
                                                <img src="{{ $item['image'] }}" class="rounded mr-2" style="width:40px; height:40px; object-fit:cover;">
                                            @else
                                                <div class="rounded mr-2 bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:40px; height:40px;"><i class="fas fa-box"></i></div>
                                            @endif
                                            <div>
                                                <span class="font-weight-bold text-dark small d-block">{{ $item['name'] }}</span>
                                                <small class="text-muted">SL: {{ $item['quantity'] }} × {{ number_format($item['price'], 0, ',', '.') }} đ</small>
                                            </div>
                                        </div>
                                        <span class="font-weight-bold text-dark small">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} đ</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Tạm tính sản phẩm:</span>
                        <span class="font-weight-bold text-dark" id="subtotal-val">{{ number_format($totalPrice ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Phí vận chuyển GHN:</span>
                        <span class="text-success font-weight-bold" id="shipping_fee_text">Chọn địa chỉ để tính</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <span class="font-weight-bold text-dark h6 mb-0">Tổng thanh toán:</span>
                        <span class="font-weight-bold h4 mb-0 text-purple" id="final_total_text" style="color: #6366f1;">{{ number_format($totalPrice ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function initAddressComboboxes() {
        const provinceSelect = document.getElementById('province_select');
        const districtSelect = document.getElementById('district_select');
        const wardSelect = document.getElementById('ward_select');
        const streetInput = document.getElementById('street_address');
        const fullAddressInput = document.getElementById('full_address_input');
        const toDistrictInput = document.getElementById('to_district_id_input');
        const toWardInput = document.getElementById('to_ward_code_input');

        const shippingFeeText = document.getElementById('shipping_fee_text');
        const finalTotalText = document.getElementById('final_total_text');

        const districtsUrl = "{{ route('locations.districts', ['provinceId' => '__PROVINCE__']) }}";
        const wardsUrl = "{{ route('locations.wards', ['districtId' => '__DISTRICT__']) }}";

        if (!provinceSelect || !districtSelect || !wardSelect) return;

        function updateTotals(fee) {
            let currentSubtotal = 0;
            const subtotalEl = document.getElementById('subtotal-val');
            if (subtotalEl) {
                currentSubtotal = parseInt(subtotalEl.innerText.replace(/[^0-9]/g, '')) || 0;
            }
            if (shippingFeeText) {
                shippingFeeText.innerText = new Intl.NumberFormat('vi-VN').format(fee) + ' VNĐ';
            }
            const finalAmount = currentSubtotal + fee;
            if (finalTotalText) {
                finalTotalText.innerText = new Intl.NumberFormat('vi-VN').format(finalAmount) + ' VNĐ';
            }
        }

        // 1. Tải danh sách Tỉnh/Thành phố từ GHN
        fetch("{{ route('locations.provinces') }}")
            .then(res => res.json())
            .then(res => {
                if (res.data && Array.isArray(res.data) && res.data.length > 0) {
                    let options = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                    res.data.forEach(p => {
                        options += `<option value="${p.ProvinceID}">${p.ProvinceName}</option>`;
                    });
                    provinceSelect.innerHTML = options;
                }
            })
            .catch(err => {
                console.error("Lỗi tải tỉnh thành:", err);
            });

        // 2. Khi chọn Tỉnh -> Tải Quận/Huyện
        provinceSelect.addEventListener('change', function () {
            districtSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
            districtSelect.disabled = true;
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            wardSelect.disabled = true;
            updateTotals(0);
            updateFullAddress();

            if (!this.value) return;

            fetch(districtsUrl.replace('__PROVINCE__', this.value))
                .then(res => res.json())
                .then(res => {
                    if (res.data && Array.isArray(res.data) && res.data.length > 0) {
                        let options = '<option value="">-- Chọn Quận/Huyện --</option>';
                        res.data.forEach(d => {
                            options += `<option value="${d.DistrictID}">${d.DistrictName}</option>`;
                        });
                        districtSelect.innerHTML = options;
                        districtSelect.disabled = false;
                    } else {
                        districtSelect.innerHTML = '<option value="">-- Không tải được --</option>';
                    }
                })
                .catch(err => {
                    console.error("Lỗi load quận huyện:", err);
                });
        });

        // 3. Khi chọn Quận/Huyện -> Tải Phường/Xã
        districtSelect.addEventListener('change', function () {
            wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
            wardSelect.disabled = true;
            updateTotals(0);
            if (toDistrictInput) toDistrictInput.value = this.value;
            updateFullAddress();

            if (!this.value) return;

            fetch(wardsUrl.replace('__DISTRICT__', this.value))
                .then(res => res.json())
                .then(res => {
                    if (res.data && Array.isArray(res.data) && res.data.length > 0) {
                        let options = '<option value="">-- Chọn Phường/Xã --</option>';
                        res.data.forEach(w => {
                            options += `<option value="${w.WardCode}">${w.WardName}</option>`;
                        });
                        wardSelect.innerHTML = options;
                        wardSelect.disabled = false;
                    } else {
                        wardSelect.innerHTML = '<option value="">-- Không tải được --</option>';
                    }
                })
                .catch(err => {
                    console.error("Lỗi load phường xã:", err);
                });
        });

        // 4. Khi chọn Phường/Xã -> Tính cước vận chuyển GHN
        wardSelect.addEventListener('change', function () {
            if (toWardInput) toWardInput.value = this.value;
            updateFullAddress();

            if (!this.value || !districtSelect.value) return;

            if (shippingFeeText) shippingFeeText.innerText = 'Đang tính cước GHN...';

            fetch("{{ route('locations.fee') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    to_district_id: districtSelect.value,
                    to_ward_code: this.value
                })
            })
                .then(res => res.json())
                .then(res => {
                    if (res.code === 200 && res.data) {
                        const fee = parseInt(res.data.total) || 0;
                        updateTotals(fee);
                    } else {
                        if (shippingFeeText) shippingFeeText.innerText = '0 VNĐ (Không tính được)';
                        updateTotals(0);
                    }
                })
                .catch(err => {
                    console.error("Lỗi tính phí GHN:", err);
                    if (shippingFeeText) shippingFeeText.innerText = 'Lỗi tính phí';
                    updateTotals(0);
                });
        });

        if (streetInput) streetInput.addEventListener('input', updateFullAddress);

        function updateFullAddress() {
            const provinceOpt = provinceSelect.options[provinceSelect.selectedIndex];
            const districtOpt = districtSelect.options[districtSelect.selectedIndex];
            const wardOpt = wardSelect.options[wardSelect.selectedIndex];
            const street = streetInput ? streetInput.value.trim() : '';

            const provinceName = (provinceSelect.value && provinceOpt) ? (provinceOpt.text) : '';
            const districtName = (districtSelect.value && districtOpt) ? (districtOpt.text) : '';
            const wardName = (wardSelect.value && wardOpt) ? (wardOpt.text) : '';

            const parts = [];
            if (street) parts.push(street);
            if (wardName && !wardName.includes('--')) parts.push(wardName);
            if (districtName && !districtName.includes('--')) parts.push(districtName);
            if (provinceName && !provinceName.includes('--')) parts.push(provinceName);

            const fullAddr = parts.join(', ');
            if (fullAddressInput) fullAddressInput.value = fullAddr;

            const previewEl = document.getElementById('address-preview');
            const previewText = document.getElementById('address-preview-text');
            if (previewEl && previewText) {
                if (fullAddr) {
                    previewText.innerText = fullAddr;
                    previewEl.classList.remove('d-none');
                } else {
                    previewEl.classList.add('d-none');
                }
            }
        }
    }

    function selectPaymentMethod(method) {
        const radio = document.getElementById('payment_' + method);
        if (radio) {
            radio.checked = true;
            togglePaymentMethod(method);
        }
    }

    function togglePaymentMethod(method) {
        const codDetail = document.getElementById('cod-detail');
        const momoAtmDetail = document.getElementById('momo-atm-detail');
        const momoCcDetail = document.getElementById('momo-cc-detail');

        const optCod = document.getElementById('opt-cod');
        const optMomoAtm = document.getElementById('opt-momo-atm');
        const optMomoCc = document.getElementById('opt-momo-cc');

        if (codDetail) codDetail.classList.add('d-none');
        if (momoAtmDetail) momoAtmDetail.classList.add('d-none');
        if (momoCcDetail) momoCcDetail.classList.add('d-none');

        if (optCod) optCod.style.backgroundColor = '#ffffff';
        if (optMomoAtm) optMomoAtm.style.backgroundColor = '#ffffff';
        if (optMomoCc) optMomoCc.style.backgroundColor = '#ffffff';

        if (method === 'cod') {
            if (codDetail) codDetail.classList.remove('d-none');
            if (optCod) optCod.style.backgroundColor = '#f8fafc';
        } else if (method === 'momo_atm') {
            if (momoAtmDetail) momoAtmDetail.classList.remove('d-none');
            if (optMomoAtm) optMomoAtm.style.backgroundColor = '#eff6ff';
        } else if (method === 'momo_cc') {
            if (momoCcDetail) momoCcDetail.classList.remove('d-none');
            if (optMomoCc) optMomoCc.style.backgroundColor = '#fff1f2';
        }
    }

    let isOrderSubmitting = false;

    function onProcessCheckoutSubmit(e) {
        if (isOrderSubmitting) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            return false;
        }

        const nameInput = document.getElementById('name');
        const phoneInput = document.getElementById('phone');
        const provinceSelect = document.getElementById('province_select');
        const districtSelect = document.getElementById('district_select');
        const wardSelect = document.getElementById('ward_select');
        const streetInput = document.getElementById('street_address');

        const nameVal = nameInput?.value.trim();
        const phoneVal = phoneInput?.value.trim();
        const provinceVal = provinceSelect?.value;
        const districtVal = districtSelect?.value;
        const wardVal = wardSelect?.value;
        const streetVal = streetInput?.value.trim();

        let isValid = true;
        let firstInvalidField = null;

        // Reset invalid styles
        [nameInput, phoneInput, provinceSelect, districtSelect, wardSelect, streetInput].forEach(el => {
            if (el) el.classList.remove('is-invalid');
        });

        if (!nameVal) { isValid = false; if (nameInput) { nameInput.classList.add('is-invalid'); firstInvalidField = firstInvalidField || nameInput; } }
        const phoneRegex = /^0(3|5|7|8|9)\d{8}$/;
        if (!phoneVal || !phoneRegex.test(phoneVal)) {
            isValid = false;
            if (phoneInput) {
                phoneInput.classList.add('is-invalid');
                firstInvalidField = firstInvalidField || phoneInput;
            }
        }
        if (!provinceVal) { isValid = false; if (provinceSelect) { provinceSelect.classList.add('is-invalid'); firstInvalidField = firstInvalidField || provinceSelect; } }
        if (!districtVal) { isValid = false; if (districtSelect) { districtSelect.classList.add('is-invalid'); firstInvalidField = firstInvalidField || districtSelect; } }
        if (!wardVal) { isValid = false; if (wardSelect) { wardSelect.classList.add('is-invalid'); firstInvalidField = firstInvalidField || wardSelect; } }
        if (!streetVal) { isValid = false; if (streetInput) { streetInput.classList.add('is-invalid'); firstInvalidField = firstInvalidField || streetInput; } }

        if (!isValid) {
            isOrderSubmitting = false;
            if (e) e.preventDefault();
            if (firstInvalidField) {
                firstInvalidField.focus();
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            alert('Vui lòng điền đầy đủ Họ tên, Số điện thoại hợp lệ (10 số, đầu 03/05/07/08/09), địa chỉ GHN (Tỉnh/Thành, Quận/Huyện, Phường/Xã, Số nhà) trước khi bấm thanh toán!');
            return false;
        }

        // Always sync hidden location fields before submit
        const toDistrictInput = document.getElementById('to_district_id_input');
        const toWardInput = document.getElementById('to_ward_code_input');
        const fullAddressInput = document.getElementById('full_address_input');

        if (toDistrictInput) toDistrictInput.value = districtVal;
        if (toWardInput) toWardInput.value = wardVal;
        if (fullAddressInput && typeof updateFullAddress === 'function') updateFullAddress();

        if (typeof getCart === 'function') {
            const cart = getCart();
            const selectedItems = cart.filter(item => item.selected !== false);
            if (selectedItems.length > 0) {
                document.getElementById('cart-items-input').value = JSON.stringify(selectedItems);
            }
        }

        isOrderSubmitting = true;

        // Check payment method
        const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value;
        const submitBtn = document.getElementById('btn-submit-order');

        if (submitBtn) {
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.opacity = '0.7';
            if (selectedPayment === 'momo_atm' || selectedPayment === 'momo_cc' || selectedPayment === 'momo') {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang kết nối cổng MoMo...';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý đơn hàng, vui lòng đợi...';
            }
            setTimeout(function() {
                submitBtn.disabled = true;
            }, 50);
        }

        return true;
    }

    function syncLocalStorageCartSummary() {
        if (typeof getCart !== 'function') return;
        const cart = getCart();
        const selectedItems = cart.filter(item => item.selected !== false);
        if (selectedItems.length === 0) return;

        const cartInput = document.getElementById('cart-items-input');
        if (cartInput) {
            cartInput.value = JSON.stringify(selectedItems);
        }

        const itemsList = document.getElementById('checkout-items-list');
        const subtotalEl = document.getElementById('subtotal-val');
        const finalTotalEl = document.getElementById('final_total_text');

        let subtotal = 0;
        let html = '<ul class="list-group list-group-flush mb-3">';

        selectedItems.forEach(item => {
            const itemSubtotal = item.price * item.quantity;
            subtotal += itemSubtotal;
            const imgTag = item.image 
                ? `<img src="${item.image}" class="rounded mr-2" style="width:40px; height:40px; object-fit:cover;">`
                : `<div class="rounded mr-2 bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:40px; height:40px;"><i class="fas fa-box"></i></div>`;

            html += `
                <li class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between border-bottom">
                    <div class="d-flex align-items-center">
                        ${imgTag}
                        <div>
                            <span class="font-weight-bold text-dark small d-block">${item.name}</span>
                            <small class="text-muted">SL: ${item.quantity} × ${new Intl.NumberFormat('vi-VN').format(item.price)} đ</small>
                        </div>
                    </div>
                    <span class="font-weight-bold text-dark small">${new Intl.NumberFormat('vi-VN').format(itemSubtotal)} đ</span>
                </li>
            `;
        });
        html += '</ul>';

        if (itemsList && (!itemsList.children.length || !itemsList.querySelector('ul'))) {
            itemsList.innerHTML = html;
        }

        if (subtotalEl && (subtotalEl.innerText.trim() === '0 đ' || subtotalEl.innerText.trim() === '0 VNĐ')) {
            subtotalEl.innerText = new Intl.NumberFormat('vi-VN').format(subtotal) + ' VNĐ';
        }
        if (finalTotalEl && (finalTotalEl.innerText.trim() === '0 đ' || finalTotalEl.innerText.trim() === '0 VNĐ')) {
            finalTotalEl.innerText = new Intl.NumberFormat('vi-VN').format(subtotal) + ' VNĐ';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initAddressComboboxes();
        syncLocalStorageCartSummary();
    });
</script>
@endsection
