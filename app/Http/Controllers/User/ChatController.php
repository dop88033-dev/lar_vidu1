<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Gửi tin nhắn từ User tới Admin
     */
    public function send(Request $request)
    {
        // 1. Lấy nội dung từ request JSON
        $messageText = $request->input('message');
        $orderId = $request->input('order_id');

        // 2. Kiểm tra nội dung trống
        if (empty(trim($messageText))) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống'], 400);
        }

        // 3. Xác định Admin nhận tin
        $admin = User::where('role', 'admin')->first();
        $receiverId = $admin ? $admin->id : 1;

        // Nếu có truyền order_id, kiểm tra xem đơn hàng đó có thuộc về user không
        if (!empty($orderId)) {
            $order = Order::where('id', $orderId)->where('user_id', Auth::id())->first();
            if (!$order) {
                $orderId = null;
            }
        }

        try {
            // 4. Lưu tin nhắn vào Database
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'order_id' => $orderId ?: null,
                'content' => $messageText,
                'is_read' => false,
            ]);

            return response()->json($message->load('order'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể gửi tin nhắn: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Lấy lịch sử chat giữa User hiện tại và Admin
     */
    public function getMessages()
    {
        $userId = Auth::id();

        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        $messages = Message::with(['sender', 'receiver', 'order'])
            ->where(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)
                  ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Lấy danh sách đơn hàng của người dùng hiện tại để chọn khi chat
     */
    public function getOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->select('id', 'total_price', 'status', 'created_at')
            ->get();

        return response()->json($orders);
    }
}
