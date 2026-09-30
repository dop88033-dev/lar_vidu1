<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'category_id',
        'product_name',
        'variant',
        'quantity',
        'price'
    ];

    // Một mục sản phẩm thuộc về một đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Một mục sản phẩm liên kết với 1 sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault(function() {
            return Product::find($this->category_id);
        });
    }

    // Liên kết với danh mục sản phẩm (Category)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
