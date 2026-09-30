<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('id', 'desc')->get();
        return view('admin.product_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.product_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không vượt quá 255 ký tự.',
        ]);

        ProductCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Thêm mới danh mục thành công!');
    }

    public function show(ProductCategory $productCategory)
    {
        return view('admin.product_categories.show', compact('productCategory'));
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('admin.product_categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không vượt quá 255 ký tự.',
        ]);

        $productCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Xóa danh mục thành công!');
    }
}