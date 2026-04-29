<!DOCTYPE html>
<html lang="vi">

<head><!--css -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #0f172a;
            color: white;
            display: flex;
            height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #020617;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .menu p {
            margin: 10px 0;
            cursor: pointer;
        }

        .history-box {
            margin-top: auto;
        }

        #historyList {
            list-style: none;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
        }

        #historyList li {
            background: #1e293b;
            padding: 8px;
            margin-bottom: 5px;
            border-radius: 6px;
            cursor: pointer;
        }

        /* CHAT */
        .chat {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .message {
            margin-bottom: 15px;
        }

        .user {
            text-align: right;
        }

        .bot {
            color: #94a3b8;
        }

        /* INPUT */
        .input-box {
            display: flex;
            padding: 15px;
            background: #020617;
        }

        input {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
        }

        button {
            margin-left: 10px;
            padding: 12px 20px;
            border: none;
            background: #3b82f6;
            color: white;
            border-radius: 8px;
        }
    </style>


<!--css đăng nhập,đăng kí -->
    <style>
        .top-bar {
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .menu-btn {
            font-size: 24px;
            cursor: pointer;
            padding: 5px 10px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 35px;
            background: #1e293b;
            border-radius: 8px;
            overflow: hidden;
            min-width: 120px;
        }

        .dropdown-menu p {
            margin: 0;
            padding: 10px;
            cursor: pointer;
        }

        .dropdown-menu p:hover {
            background: #334155;
        }
    </style>
</head>

<body>  
    <!-- SIDEBAR -->
    <div class="sidebar">
        <h1>AI Trainer</h1>
        <div class="menu">
            <p onclick="newChat()">Đoạn chat mới</p>
            <p>Kết quả</p>
            <p>Dự án mới</p>
        </div>

        <div class="history-box">
            <h3>Lịch sử</h3>
            <ul id="historyList"></ul>
        </div>
    </div>

    <div class="top-bar">
        <div class="menu-btn" onclick="toggleMenu()">⋮</div>

        <div class="dropdown-menu" id="userMenu">
            <p onclick="login()">Đăng nhập</p>
            <p onclick="register()">Đăng ký</p>
        </div>
    </div>

    <!-- CHAT -->
    <div class="chat">
        <div class="messages" id="messages">
            <div class="message bot">Chào mừng bạn đến với AI Trainer</div>
        </div>

        <div class="input-box">
            <input type="text" id="inputText" placeholder="Nhập câu hỏi...">
            <button onclick="send()">Gửi</button>
        </div>
    </div>
</body>
<script src="backEnd/back_end.js"></script>

</html>