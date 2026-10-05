<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'    => 'required|exists:orders,id',
            'category_id' => 'required|exists:categories,id',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá (1 - 5 sao).',
            'rating.min'      => 'Đánh giá tối thiểu là 1 sao.',
            'rating.max'      => 'Đánh giá tối đa là 5 sao.',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền đánh giá đơn hàng này.');
        }

        if ($order->status !== 'delivered' || $order->shipping_status !== 'delivered') {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đã nhận hàng thành công.');
        }

        Review::updateOrCreate(
            [
                'user_id'     => Auth::id(),
                'order_id'    => $order->id,
                'category_id' => $request->category_id,
            ],
            [
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá và nhận xét sản phẩm!');
    }
}
