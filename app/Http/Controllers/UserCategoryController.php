<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class UserCategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục và sản phẩm cho Khách hàng (có tìm kiếm & lọc)
     */
    public function index(Request $request)
    {
        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $selectedCategoryId = $request->query('category_id');
        $selectedCategory = null;
        $keyword = trim($request->query('keyword', ''));
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $sort = $request->query('sort', 'newest');

        $query = Category::query();

        // 1. Lọc theo danh mục nếu có
        if ($selectedCategoryId) {
            $selectedCategory = ProductCategory::find($selectedCategoryId);
            if ($selectedCategory) {
                $query->where('category_name', $selectedCategory->name);
            }
        }

        // 2. Tìm kiếm theo từ khóa (tên sản phẩm, mô tả hoặc danh mục)
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('notes', 'like', "%{$keyword}%")
                  ->orWhere('category_name', 'like', "%{$keyword}%");
            });
        }

        // 3. Lọc theo khoảng giá
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', floatval($minPrice));
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', floatval($maxPrice));
        }

        // 4. Sắp xếp sản phẩm
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // 5. Phân trang 12 sản phẩm/trang kèm giữ tham số query
        $products = $query->paginate(12)->withQueryString();

        return view('user.categories.index', compact(
            'categories', 
            'products', 
            'selectedCategory', 
            'keyword', 
            'sort', 
            'minPrice', 
            'maxPrice'
        ));
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
