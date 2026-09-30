<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserCategoryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\CartController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\User\ReviewController as UserReviewController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\FinanceController;

// 1. Trang chủ công khai cho khách hàng
Route::get('/', function () {
    $products = \App\Models\Category::all();
    return view('welcome', compact('products'));
})->name('welcome');

// Danh mục dành cho Khách hàng
Route::get('/categories', [UserCategoryController::class, 'index'])->name('user.categories.index');
Route::get('/categories/product/{id}', [UserCategoryController::class, 'showProduct'])->name('user.categories.product-detail');

// Giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('user.cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| 🚚 THIRD-PARTY WEBHOOKS & CALLBACKS (GHN, MOMO IPN)
|--------------------------------------------------------------------------
| NOTE:
| - Không dùng middleware 'auth' vì bên thứ 3 (GHN, MoMo) gọi sang tự động.
| - Đã được bypass CSRF trong VerifyCsrfToken.
|--------------------------------------------------------------------------
*/
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');

// User Payment & Order Routes (Auth & Verified)
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/payment', [OrderController::class, 'index'])->name('payment.index');
    Route::post('/payment/process', [OrderController::class, 'processPayment'])->name('payment.process');
    Route::get('/checkout', [OrderController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [OrderController::class, 'processPayment'])->name('checkout.process');
    
    Route::get('/orders', [OrderController::class, 'orderHistory'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/push-ghn', [OrderController::class, 'pushGhn'])->name('orders.push_ghn');
    Route::get('/orders/{order}/pay/momo/{type}', [MomoController::class, 'payAgain'])->name('orders.momo.pay')->where('type', 'atm|cc');
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');

    // User gửi tin
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    // User lấy tin
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');

    // Đánh giá sản phẩm
    Route::post('/reviews', [UserReviewController::class, 'store'])->name('reviews.store');
});

// Location & GHN Routes
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [OrderController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [OrderController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [OrderController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [OrderController::class, 'getShippingFee'])->name('fee');
});

// 2. Xác thực và Đăng ký / Đăng nhập / Đăng xuất
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Hiển thị thông báo xác thực email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Xử lý link xác nhận (từ email)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('welcome');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Gửi lại email xác nhận
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 3. Khu vực dành riêng cho Quản trị viên (Admin) - Yêu cầu Đăng nhập & Quyền Admin
Route::middleware(['auth', 'admin'])->group(function () {
    
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Quản lý Sản phẩm
    Route::resource('admin/categories', CategoryController::class)->names([
        'index'   => 'admin.categories.index',
        'create'  => 'admin.categories.create',
        'store'   => 'admin.categories.store',
        'show'    => 'admin.categories.show',
        'edit'    => 'admin.categories.edit',
        'update'  => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

    Route::get('admin/products', [CategoryController::class, 'index'])->name('admin.products.index');

    // Quản lý Danh mục sản phẩm
    Route::resource('product-categories', ProductCategoryController::class)->names([
        'index'   => 'admin.product-categories.index',
        'create'  => 'admin.product-categories.create',
        'store'   => 'admin.product-categories.store',
        'show'    => 'admin.product-categories.show',
        'edit'    => 'admin.product-categories.edit',
        'update'  => 'admin.product-categories.update',
        'destroy' => 'admin.product-categories.destroy',
    ]);

    // Livechat Admin
    Route::get('/admin/chat/users', [AdminChatController::class, 'getUsers'])->name('admin.chat.users');
    Route::get('/admin/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('admin.chat.messages');
    Route::post('/admin/chat/send', [AdminChatController::class, 'send'])->name('admin.chat.send');

    // Quản lý Đơn hàng (Admin Order Management)
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update_status');
    Route::delete('/admin/orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');

    // Báo cáo doanh thu & Biểu đồ (Admin Reports)
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/charts', [AdminReportController::class, 'charts'])->name('admin.reports.charts');

    // Quản lý Tài chính & Giao dịch (Finance)
    Route::get('/admin/finance', [FinanceController::class, 'index'])->name('admin.finance.index');
    Route::get('/admin/finance/transactions', [FinanceController::class, 'transactions'])->name('admin.finance.transactions');
    Route::patch('/admin/finance/{order}/status', [FinanceController::class, 'updateStatus'])->name('admin.finance.update-status');

    // Quản lý người dùng (Admin User Management)
    Route::resource('admin/users', AdminUserController::class)->names([
        'index'   => 'admin.users.index',
        'create'  => 'admin.users.create',
        'store'   => 'admin.users.store',
        'show'    => 'admin.users.show',
        'edit'    => 'admin.users.edit',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    // Health Check Route cho Render
Route::get('/up', function () {
    return response('OK', 200);
});

// Route xóa sạch config cache trực tiếp
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return 'Config and application cache cleared successfully!';
});
// Route nạp dữ liệu mẫu trực tiếp
Route::get('/run-seed', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return 'Seed chạy thành công: <br><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Lỗi khi seed: ' . $e->getMessage();
    }
});

});