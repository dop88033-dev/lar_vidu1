<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Bảng Quản Trị')</title>

    <!-- Bootstrap 4.5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        body {
            background-color: #f4f6f9;
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #1e293b;
            color: #94a3b8;
            z-index: 1000;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        #sidebar .sidebar-brand {
            padding: 20px 24px;
            background: #0f172a;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #334155;
        }

        .brand-logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            margin-right: 12px;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
        }

        #sidebar .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            padding: 20px 24px 10px;
        }

        #sidebar .nav-link {
            padding: 12px 24px;
            color: #94a3b8;
            font-size: 14.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        #sidebar .nav-link i {
            width: 24px;
            font-size: 16px;
            margin-right: 12px;
            text-align: center;
        }

        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
            border-left-color: #6366f1;
        }

        /* Main Content Styling */
        #main-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            background: #ffffff;
            height: 65px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #e2e8f0;
        }

        .page-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .content-body {
            padding: 30px;
            flex: 1;
        }

        /* Modern Card & Table Design */
        .card-custom {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }

        .btn-purple {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
            transition: all 0.2s;
        }

        .btn-purple:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #fff;
            box-shadow: 0 6px 12px -1px rgba(99, 102, 241, 0.4);
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            border-top: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 16px;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .footer-text {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }

        /* Action buttons and Form Cards */
        .product-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section-title {
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .bottom-action-bar {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .btn-save-product {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-save-product:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
        }
        .btn-add-color {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 7px 16px;
            transition: all 0.2s;
        }
        .btn-add-color:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo-icon">E</div>
            <div>
                <div class="h6 font-weight-bold text-white mb-0">Admin</div>
                <div class="small text-muted" style="font-size: 11px;">E-Commerce System</div>
            </div>
        </div>

        <div class="section-title">Quản lý hệ thống</div>

        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Tổng quan
            </a>
            <a href="{{ route('admin.product-categories.index') }}" class="nav-link {{ request()->routeIs('admin.product-categories.*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i> Danh mục
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-box-open"></i> Sản phẩm
            </a>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
            </a>
            <a href="{{ route('admin.finance.index') }}" class="nav-link {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> Thống kê tài chính
            </a>
            <a href="{{ route('admin.finance.transactions') }}" class="nav-link {{ request()->routeIs('admin.finance.transactions') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i> Giao dịch thanh toán
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Báo cáo
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Quản lý người dùng
            </a>
        </nav>

        <div class="mt-auto p-3 border-top border-secondary">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-block btn-sm">
                    <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div id="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <h1 class="page-header-title">Bảng Quản Trị</h1>
            <div class="d-flex align-items-center">
                <div class="mr-3 text-right">
                    <div class="font-weight-bold text-dark mb-0" style="font-size: 14px;">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <span class="badge badge-purple px-2" style="background:#e0e7ff; color:#4338ca; font-size:11px;">Quản trị viên</span>
                </div>
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center font-weight-bold shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #4f46e5) !important;">
                    <i class="fas fa-user font-size-14"></i>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer-text">
            © 2026 E-Commerce Management System. Built with Laravel 10.
        </footer>
    </div>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    @auth
    <!-- ADMIN CHAT POPUP -->
    <style>
        #admin-chat-box {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 9999;
        }
        #admin-chat-box #chat-popup {
            width: 380px;
            height: 480px;
            display: none;
            flex-direction: column;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #cbd5e1;
        }
        #user-list {
            max-height: 130px;
            overflow-y: auto;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .user-item {
            padding: 8px 14px;
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 500;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }
        .user-item:hover {
            background-color: #e2e8f0;
        }
        .user-item.active {
            background-color: #6366f1;
            color: #ffffff;
            font-weight: 600;
        }
        #admin-chat-box #chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
        }
        .msg-row {
            margin-bottom: 8px;
            font-size: 13.5px;
            line-height: 1.4;
            word-break: break-word;
        }
    </style>

    <div id="admin-chat-box">
        <button id="chat-toggle" class="btn btn-dark shadow rounded-pill px-3 py-2 font-weight-bold">💬 Chat Khách hàng</button>
        <div id="chat-popup" class="card shadow-lg">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2">
                <strong><i class="fas fa-comments mr-1"></i> Hỗ trợ trực tuyến</strong>
                <button id="chat-close" class="btn btn-sm btn-light py-0 px-2 font-weight-bold">X</button>
            </div>
            <div id="user-list">
                <div class="p-2 text-center text-muted"><small>Đang tải danh sách...</small></div>
            </div>

            <div id="chat-messages">
                <div class="text-center mt-5 text-muted"><small>Chọn một khách hàng để xem tin nhắn</small></div>
            </div>
            <div class="card-footer bg-white p-2">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Nhập câu trả lời...">
                    <div class="input-group-append">
                        <button id="send-btn" class="btn btn-success btn-sm px-3">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let currentUserId = null;
        const chatPopup = document.getElementById("chat-popup");
        const chatMessages = document.getElementById("chat-messages");
        const chatInput = document.getElementById("chat-input");
        const chatToggle = document.getElementById("chat-toggle");
        const chatClose = document.getElementById("chat-close");
        const sendBtn = document.getElementById("send-btn");

        if (!chatToggle) return;

        // Mở/Đóng popup
        chatToggle.onclick = () => {
            chatPopup.style.display = "flex";
            chatToggle.style.display = "none";
            loadUsers();
        };
        chatClose.onclick = () => {
            chatPopup.style.display = "none";
            chatToggle.style.display = "block";
        };

        // 1. Load danh sách User đã từng nhắn tin
        function loadUsers() {
            fetch("{{ route('admin.chat.users') }}")
                .then(res => res.json())
                .then(users => {
                    let html = "";
                    users.forEach(user => {
                        let activeClass = (currentUserId == user.id) ? 'active' : '';
                        html += `<div class="user-item ${activeClass}" data-id="${user.id}">
                            ${user.name}
                        </div>`;
                    });
                    const userListEl = document.getElementById("user-list");
                    userListEl.innerHTML = html || '<div class="p-2 text-muted text-center"><small>Chưa có hội thoại</small></div>';

                    // Attach click handlers
                    userListEl.querySelectorAll('.user-item').forEach(el => {
                        el.addEventListener('click', function() {
                            const uId = this.getAttribute('data-id');
                            selectUser(uId, this);
                        });
                    });
                })
                .catch(err => console.error("Lỗi tải danh sách user:", err));
        }

        // 2. Chọn User để chat
        function selectUser(userId, element) {
            currentUserId = userId;
            // Highlight user được chọn
            document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');
            loadMessages();
        }

        // 3. Load tin nhắn của User đang được chọn
        function loadMessages() {
            if (!currentUserId) return;
            fetch(`/admin/chat/messages/${currentUserId}`)
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    messages.forEach(msg => {
                        let senderName = msg.sender_id == "{{ Auth::id() }}" ? "Bạn" : (msg.sender ? msg.sender.name : "Khách hàng");
                        let color = msg.sender_id == "{{ Auth::id() }}" ? "#4f46e5" : "#0f172a";
                        html += `<div class="msg-row" style="color: ${color}">
                            <strong>${senderName}:</strong> ${msg.content}
                        </div>`;
                    });
                    chatMessages.innerHTML = html || '<div class="text-center text-muted my-auto"><small>Chưa có tin nhắn nào</small></div>';
                    chatMessages.scrollTop = chatMessages.scrollHeight; // Cuộn xuống cuối
                })
                .catch(err => console.error("Lỗi tải tin nhắn:", err));
        }

        // 4. Gửi tin nhắn cho User
        function sendMessage() {
            let message = chatInput.value.trim();
            if (!message || !currentUserId) return;

            chatInput.disabled = true;
            sendBtn.disabled = true;

            fetch("{{ route('admin.chat.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    user_id: currentUserId
                })
            })
            .then(res => res.json())
            .then(data => {
                chatInput.value = "";
                chatInput.disabled = false;
                sendBtn.disabled = false;
                chatInput.focus();
                loadMessages();
            })
            .catch(err => {
                console.error("Lỗi gửi tin:", err);
                chatInput.disabled = false;
                sendBtn.disabled = false;
            });
        }

        sendBtn.onclick = sendMessage;
        chatInput.onkeypress = (e) => { if(e.key === 'Enter') sendMessage(); };

        // 5. Polling (Tự động cập nhật mỗi 3 giây)
        setInterval(() => {
            if (chatPopup.style.display === "flex") {
                loadMessages();
                loadUsers(); // Cập nhật danh sách nếu có người mới nhắn
            }
        }, 3000);
    });
    </script>
    @endauth

    @yield('scripts')
</body>
</html>
