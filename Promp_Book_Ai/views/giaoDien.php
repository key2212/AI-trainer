<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AI Trainer</title>

    <link rel="stylesheet" href="../assets/css/style.css?v=11">
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            AI Trainer 🤖
        </div>

        <div class="menu-item" onclick="newChat()">
            ✨ Đoạn chat mới
        </div>

        <div class="menu-item" onclick="showTrendingAI()">
            🏆 Các AI thịnh hành hiện nay
        </div>

        <div class="menu-item" onclick="suggestTopAI()">
            🎯 Gợi ý Top 5 AI theo nhu cầu
        </div>

        <div class="menu-item link-menu" onclick="reviewPrompt()">
            ✍️ Đánh giá prompt
        </div>

        <a class="menu-item link-menu" href="ai_library.php">
            📚 Kho AI
        </a>

        <a class="menu-item link-menu" href="my_results.php">
            📊 Kết quả học tập
        </a>

        <div class="history-title">
            Lịch sử
        </div>

        <ul id="historyList"></ul>

    </div>

    <!-- CHAT -->
    <div class="chat-container">
        <!-- TOP BAR -->
        <div class="top-bar">
            <div class="user-dropdown">
                <button class="user-btn">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </button>
                <div class="dropdown-content">
                    <a href="profile.php">
                        Mục tiêu học tập
                    </a>

                    <a href="../api/auth/logout.php">
                        Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <!-- MESSAGE -->
        <div class="messages" id="messages">
            <div class="bot-message">
                Xin chào bạn <br>
                Hãy mô tả yêu cầu của bạn, tôi sẽ gợi ý AI phù hợp nhất.
            </div>

        </div>

        <!-- INPUT -->
        <div class="input-box">
            <input
                type="text"
                id="inputText"
                placeholder="Ví dụ: AI tốt nhất để làm Word...">

            <button id="sendBtn">
                Gửi
            </button>
        </div>
    </div>

    <script src="../assets/js/app.js?v=9"></script>
</body>

</html>