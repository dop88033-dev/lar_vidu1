<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
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

        // 2. Kiểm tra nội dung trống
        if (empty(trim($messageText))) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống'], 400);
        }

        // 3. Xác định Admin nhận tin
        // Ưu tiên tìm user có role là admin, nếu không thấy thì mặc định lấy ID 1
        $admin = User::where('role', 'admin')->first();
        $receiverId = $admin ? $admin->id : 1;

        try {
            // 4. Lưu tin nhắn vào Database
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'content' => $messageText,
                'is_read' => false,
            ]);

            // Trả về dữ liệu tin nhắn vừa tạo để Frontend hiển thị ngay (nếu cần)
            return response()->json($message);
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

        // Tìm Admin để lọc tin nhắn qua lại
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        // Lấy toàn bộ hội thoại giữa 2 người
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId, $adminId) {
                // Tin nhắn User gửi cho Admin
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                // Tin nhắn Admin phản hồi cho User
                $q->where('sender_id', $adminId)
                  ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc') // Sắp xếp theo thứ tự thời gian tăng dần
            ->get();

        return response()->json($messages);
    }
}
