<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $productCategories = \App\Models\ProductCategory::where('is_active', true)->get();
        return view('admin.categories.create', compact('productCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'main_image_url' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Bạn chưa nhập tên sản phẩm.',
            'price.required' => 'Vui lòng nhập giá hiển thị gốc.',
            'price.numeric' => 'Giá bán bắt buộc phải là số.',
            'price.min' => 'Giá bán không được nhỏ hơn 0.',
        ]);

        $data = [
            'name' => $request->name,
            'category_name' => $request->category_name,
            'price' => $request->price,
            'quantity' => $request->quantity ?? 0,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
            'main_image' => null,
        ];

        // Xử lý ảnh chính 
        if ($request->hasFile('main_image')) {
            $image = $request->file('main_image');
            $imageName = time() . '_main_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $data['main_image'] = 'uploads/products/' . $imageName;
        } elseif ($request->filled('main_image_url')) {
            $data['main_image'] = trim($request->main_image_url);
        }

        // Xử lý mảng màu sắc phân loại
        $colorsData = [];
        if ($request->has('colors') && is_array($request->colors)) {
            foreach ($request->colors as $index => $colorInput) {
                if (empty($colorInput['name']) && empty($colorInput['hex'])) {
                    continue;
                }
                
                $colorItem = [
                    'name' => $colorInput['name'] ?? '',
                    'hex' => $colorInput['hex'] ?? '#000000',
                    'price' => isset($colorInput['price']) && $colorInput['price'] !== '' ? (float)$colorInput['price'] : (float)$request->price,
                    'quantity' => isset($colorInput['quantity']) && $colorInput['quantity'] !== '' ? (int)$colorInput['quantity'] : 10,
                    'image' => null
                ];

                // Ưu tiên file upload -> sau đó đến link URL
                if (isset($colorInput['image']) && $request->hasFile("colors.{$index}.image")) {
                    $cImage = $request->file("colors.{$index}.image");
                    $cImageName = time() . '_color_' . $index . '_' . uniqid() . '.' . $cImage->getClientOriginalExtension();
                    $cImage->move(public_path('uploads/products/colors'), $cImageName);
                    $colorItem['image'] = 'uploads/products/colors/' . $cImageName;
                } elseif (!empty($colorInput['image_url'])) {
                    $colorItem['image'] = trim($colorInput['image_url']);
                }

                $colorsData[] = $colorItem;
            }
        }
        $data['colors'] = $colorsData;

        Category::create($data);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Thêm mới sản phẩm thành công!');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $productCategories = \App\Models\ProductCategory::where('is_active', true)->get();
        return view('admin.categories.edit', compact('category', 'productCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_name' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'main_image_url' => 'nullable|string|max:1000',
        ]);

        $data = [
            'name' => $request->name,
            'category_name' => $request->category_name,
            'price' => $request->price,
            'quantity' => $request->quantity ?? 0,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
            'main_image' => $category->main_image,
        ];

        if ($request->hasFile('main_image')) {
            $image = $request->file('main_image');
            $imageName = time() . '_main_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $data['main_image'] = 'uploads/products/' . $imageName;
        } elseif ($request->filled('main_image_url')) {
            $data['main_image'] = trim($request->main_image_url);
        }

        // Xử lý màu sắc
        $colorsData = [];
        if ($request->has('colors') && is_array($request->colors)) {
            foreach ($request->colors as $index => $colorInput) {
                if (empty($colorInput['name']) && empty($colorInput['hex'])) {
                    continue;
                }

                $colorItem = [
                    'name' => $colorInput['name'] ?? '',
                    'hex' => $colorInput['hex'] ?? '#000000',
                    'price' => isset($colorInput['price']) && $colorInput['price'] !== '' ? (float)$colorInput['price'] : (float)$request->price,
                    'quantity' => isset($colorInput['quantity']) && $colorInput['quantity'] !== '' ? (int)$colorInput['quantity'] : 10,
                    'image' => $colorInput['old_image'] ?? null
                ];

                if (isset($colorInput['image']) && $request->hasFile("colors.{$index}.image")) {
                    $cImage = $request->file("colors.{$index}.image");
                    $cImageName = time() . '_color_' . $index . '_' . uniqid() . '.' . $cImage->getClientOriginalExtension();
                    $cImage->move(public_path('uploads/products/colors'), $cImageName);
                    $colorItem['image'] = 'uploads/products/colors/' . $cImageName;
                } elseif (!empty($colorInput['image_url'])) {
                    $colorItem['image'] = trim($colorInput['image_url']);
                }

                $colorsData[] = $colorItem;
            }
        }
        $data['colors'] = $colorsData;

        $category->update($data);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Cập nhật thông tin sản phẩm thành công!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã xóa sản phẩm thành công!');
    }
}