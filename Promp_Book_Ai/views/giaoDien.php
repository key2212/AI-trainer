<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Trainer 🤖 | Royal Edition</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #0f172a;
            --bg-sidebar: #020617;
            --bg-item-hover: #1e293b;
            --accent-blue: #3b82f6;
            --text-gray: #94a3b8;
            --glass-bg: rgba(30, 41, 59, 0.5);
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            color: #f8fafc;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 280px;
            background: var(--bg-sidebar);
            padding: 24px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .sidebar h1 {
            font-size: 1.5rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            padding: 12px 16px;
            margin-bottom: 8px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 500;
            color: var(--text-gray);
        }

        .nav-item:hover {
            background: var(--bg-item-hover);
            color: white;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: var(--glass-bg);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: white;
        }

        .history-box {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .history-box h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 15px;
        }

        #historyList {
            list-style: none;
            padding: 0;
            max-height: 250px;
            overflow-y: auto;
        }

        #historyList li {
            padding: 10px;
            font-size: 0.9rem;
            color: var(--text-gray);
            cursor: pointer;
            border-radius: 6px;
        }

        #historyList li:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        /* --- CHAT AREA --- */
        .chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Top Bar Menu */
        .top-bar-container {
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .menu-icon {
            font-size: 24px;
            cursor: pointer;
            color: var(--text-gray);
            transition: 0.3s;
        }

        .menu-icon:hover { color: white; }

        .user-menu {
            display: none;
            position: absolute;
            top: 60px;
            right: 30px;
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            z-index: 100;
            overflow: hidden;
        }

        .user-menu div {
            padding: 12px 25px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .user-menu div:hover { background: #334155; }

        /* Messages */
        .messages {
            flex: 1;
            padding: 20px 15% 40px 15%;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .message-row {
            display: flex;
            margin-bottom: 25px;
            gap: 15px;
            animation: fadeIn 0.4s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .bubble {
            max-width: 80%;
            padding: 14px 18px;
            border-radius: 15px;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .bot-bubble {
            background: var(--glass-bg);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .user-row { justify-content: flex-end; }
        .user-bubble {
            background: var(--accent-blue);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }

        /* Input Area */
        .input-container {
            padding: 30px 15%;
            background: linear-gradient(transparent, var(--bg-main) 30%);
        }

        .input-box {
            display: flex;
            background: #1e293b;
            padding: 8px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            transition: 0.3s;
        }

        .input-box:focus-within {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        input {
            flex: 1;
            background: transparent;
            border: none;
            color: white;
            padding: 10px 15px;
            outline: none;
            font-size: 1rem;
        }

        button#sendBtn {
            background: var(--accent-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s;
        }

        button#sendBtn:hover {
            background: #2563eb;
            transform: scale(1.05);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h1>AI Trainer 🤖</h1>
    <div class="nav-item active" onclick="newChat()">✨ Đoạn chat mới</div>
    <div class="nav-item" onclick="newProject()">📂 Dự án mới</div>
    <div class="nav-item" onclick="analysis()">📊 Phân tích dữ liệu</div>
    <div class="nav-item" onclick="buildModel()">🛠 Xây dựng mô hình</div>

    <div class="history-box">
        <h3>Lịch sử</h3>
        <ul id="historyList">
            <li>*Dự án mới*</li>
            <li>*Phân tích Dữ liệu*</li>
            <li>*Xây dựng Mô hình*</li>
        </ul>
    </div>
</div>

<div class="chat">
    <div class="top-bar-container">
        <div class="menu-icon" id="menuBtn">⋮</div>
        <div id="userMenu" class="user-menu">
            <div onclick="goLogin()">Đăng nhập</div>
            <div onclick="goRegister()">Đăng ký</div>
        </div>
    </div>

    <div id="messages" class="messages">
        <!-- Bot Greeting -->
        <div class="message-row">
            <div class="avatar">🤖</div>
            <div class="bubble bot-bubble">Rất vui được gặp bạn, Tôi có thể giúp gì cho bạn hôm nay?</div>
        </div>
    </div>

    <div class="input-container">
        <div class="input-box">
            <input id="inputText" placeholder="Nhập câu hỏi hoặc yêu cầu của bạn..." autocomplete="off">
            <button id="sendBtn">Gửi</button>
        </div>
    </div>
</div>

<script>
    // Logic ẩn hiện menu 
    const menuBtn = document.getElementById('menuBtn');
    const userMenu = document.getElementById('userMenu');
    
    menuBtn.onclick = (e) => {
        e.stopPropagation();
        userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
    };

    window.onclick = () => { userMenu.style.display = 'none'; };

    // Các hàm này giờ đây sẽ được điều khiển bởi back_end.js
</script>
<script src="../assets/js/back_end.js"></script>
<script src="back_end.js"></script>

</body>
</html>