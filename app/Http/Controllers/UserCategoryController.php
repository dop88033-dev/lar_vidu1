<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class UserCategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục và sản phẩm cho Khách hàng
     */
    public function index(Request $request)
    {
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $selectedCategoryId = $request->query('category_id');
        $selectedCategory = null;

        $query = Category::query();

        if ($selectedCategoryId) {
            $selectedCategory = ProductCategory::find($selectedCategoryId);
            if ($selectedCategory) {
                $query->where('category_name', $selectedCategory->name);
            }
        }

        $products = $query->orderBy('id', 'desc')->get();

        return view('user.categories.index', compact('categories', 'products', 'selectedCategory'));
    }

    /**
     * Xem chi tiết sản phẩm dành cho Khách hàng
     */
    public function showProduct($id)
    {
        $product = Category::findOrFail($id);
        return view('user.categories.product_detail', compact('product'));
    }
}
