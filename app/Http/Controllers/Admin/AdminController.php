<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Thống kê tổng quan
        $totalProducts = Category::count();
        $totalCategories = ProductCategory::count();
        $totalUsers = User::count();

        // 2. Thống kê Doanh thu
        $totalRevenue = Order::whereIn('status', ['delivered', 'paid', 'cod_ordered'])->sum('total_price');
        $todayRevenue = Order::whereIn('status', ['delivered', 'paid', 'cod_ordered'])
            ->whereDate('created_at', now()->today())
            ->sum('total_price');
        $monthRevenue = Order::whereIn('status', ['delivered', 'paid', 'cod_ordered'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        // 3. Thống kê Đơn hàng theo trạng thái
        $totalOrders = Order::count();
        $orderCounts = [
            'pending'   => Order::where('status', 'pending')->count(),
            'packaged'  => Order::where('status', 'packaged')->count(),
            'shipping'  => Order::where('status', 'shipping')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // 4. Top sản phẩm bán chạy nhất
        $topProducts = OrderItem::select('category_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['delivered', 'paid', 'cod_ordered']);
            })
            ->groupBy('category_id', 'product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Load hình ảnh sản phẩm từ Category model
        foreach ($topProducts as $item) {
            $cat = Category::find($item->category_id);
            $item->main_image = $cat ? $cat->main_image : null;
        }

        // 5. Đơn hàng gần đây
        $recentOrders = Order::with('user')->orderByDesc('created_at')->take(5)->get();

        // 6. Dữ liệu biểu đồ doanh thu 6 tháng gần đây
        $chartMonths = [];
        $chartRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartMonths[] = 'Tháng ' . $date->format('m/Y');
            $chartRevenue[] = Order::whereIn('status', ['delivered', 'paid', 'cod_ordered'])
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_price');
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'totalRevenue',
            'todayRevenue',
            'monthRevenue',
            'totalOrders',
            'orderCounts',
            'topProducts',
            'recentOrders',
            'chartMonths',
            'chartRevenue'
        ));
    }

    public function products()
    {
        return app(CategoryController::class)->index();
    }

    public function categories()
    {
        return app(ProductCategoryController::class)->index();
    }
}