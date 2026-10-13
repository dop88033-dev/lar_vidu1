<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'fullname',
        'address',
        'phone',
        'total_price',
        'status',
        'shipping_status',
        // Thêm 4 trường bên dưới cho GHN:
        'ghn_order_code',
        'ghn_total_fee',
        'to_district_id',
        'to_ward_code',
        'note',
        'payment_method',
    ];

    // Accessor giúp đọc name hoặc fullname đồng nhất
    public function getNameAttribute($value)
    {
        return $value ?? $this->attributes['fullname'] ?? '';
    }

    // Một đơn hàng thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Một đơn hàng có nhiều mục sản phẩm
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Một đơn hàng có thể có nhiều giao dịch thanh toán
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Đơn hàng có nhiều đánh giá sản phẩm
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Đơn hàng có các tin nhắn liên quan
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Kiểm tra xem đơn hàng đã giao thành công chưa
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered' 
            || $this->shipping_status === 'delivered';
    }

    /**
     * Kiểm tra xem đơn hàng có thể hủy được hay không
     */
    public function canBeCancelled(): bool
    {
        // Đã hủy thì không hủy lại
        if ($this->status === 'cancelled' || $this->shipping_status === 'cancelled') {
            return false;
        }

        // Đã giao hàng thành công thì tuyệt đối không được hủy
        if ($this->isDelivered()) {
            return false;
        }

        // Đang vận chuyển, đã đóng gói, hoặc đã thanh toán thì không được hủy trực tiếp
        if (in_array($this->status, ['shipping', 'packaged', 'paid', 'paid_momo', 'cod_paid'])) {
            return false;
        }

        // Nếu trạng thái giao vận của GHN đã chuyển sang lấy hàng / đang giao
        if (in_array($this->shipping_status, ['picking', 'picked', 'storing', 'transporting', 'sorting', 'delivering', 'delivered', 'return'])) {
            return false;
        }

        return true;
    }

    /**
     * Kiểm tra xem khách hàng có được sửa thông tin đơn hay không
     */
    public function canBeEdited(): bool
    {
        return $this->canBeCancelled();
    }
}
