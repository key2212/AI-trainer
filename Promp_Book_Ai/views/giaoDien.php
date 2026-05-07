<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Trainer 🤖 | Royal Edition</title>
    <!-- Liên kết file CSS đã tách -->
    <link rel="stylesheet" href="../assets/css/background.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
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
            <ul id="historyList"></ul>
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
            <!-- Nơi hiển thị kết quả kiểm duyệt Prompt -->
            <div id="aiFeedback"></div>
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

        window.onclick = () => {
            userMenu.style.display = 'none';
        };
    </script>
    <script src="../assets/js/back_end.js"></script>
    <script src="../assets/js/practice.js"></script>

</body>

</html>