<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Lấy danh sách những User đã từng nhắn tin với Admin
     */
    public function getUsers()
    {
        $adminId = Auth::id();
        $userIds = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($msg) use ($adminId) {
                return $msg->sender_id == $adminId ? $msg->receiver_id : $msg->sender_id;
            })
            ->unique()
            ->toArray();

        return User::whereIn('id', $userIds)
            ->where('id', '!=', $adminId)
            ->select('id', 'name', 'email')
            ->get();
    }

    /**
     * Lấy lịch sử tin nhắn của một User cụ thể
     */
    public function getMessages($userId)
    {
        $adminId = Auth::id();
        return Message::with(['sender', 'order'])
            ->where(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Lấy danh sách đơn hàng của một User cụ thể để Admin chọn hoặc tham chiếu
     */
    public function getUserOrders($userId)
    {
        $orders = Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->select('id', 'total_price', 'status', 'created_at')
            ->get();

        return response()->json($orders);
    }

    /**
     * Admin gửi tin nhắn phản hồi
     */
    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required',
            'order_id' => 'nullable|exists:orders,id'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->user_id,
            'order_id' => $request->order_id ?: null,
            'content' => $request->message,
            'is_read' => true
        ]);

        return response()->json($message->load('order'));
    }
}
