<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>AI Trainer🤖</title>

<style>
body {margin:0;font-family:Arial;background:#0f172a;color:white;display:flex;height:100vh;}
.sidebar {width:250px;background:#020617;padding:20px;display:flex;flex-direction:column;}
.history-box {margin-top:auto;}
#historyList {list-style:none;padding:0;max-height:200px;overflow-y:auto;}
#historyList li {background:#1e293b;padding:8px;margin-bottom:5px;border-radius:6px;cursor:pointer;}
.chat {flex:1;display:flex;flex-direction:column;}
.messages {flex:1;padding:20px;overflow-y:auto;}
.message {margin-bottom:15px;}
.user {text-align:right;}
.bot {color:#94a3b8;}
.input-box {display:flex;padding:15px;background:#020617;}
input {flex:1;padding:12px;border:none;border-radius:8px;}
button {margin-left:10px;padding:12px 20px;background:#3b82f6;color:white;border:none;border-radius:8px;}
.top-bar {position:absolute;right:20px;top:15px;font-size:22px;cursor:pointer;}
.menu {display:none;position:absolute;right:20px;top:50px;background:#222;border-radius:8px;}
.menu div {padding:10px 20px;cursor:pointer;}
.menu div:hover {background:#444;}
</style>
</head>

<body>

<div class="sidebar">
    <h1>AI Trainer 🤖</h1>
    <div onclick="newChat()">Đoạn chat mới</div>
    <div onclick="newProject()">Dự án mới</div>

    <div class="history-box">
        <h3>Lịch sử</h3>
        <ul id="historyList"></ul>
    </div>
</div>

<div class="top-bar" id="menuBtn">⋮</div>
<div id="userMenu" class="menu">
    <div onclick="goLogin()">Đăng nhập</div>
    <div onclick="goRegister()">Đăng ký</div>
</div>

<div class="chat">
    <div id="messages" class="messages">
        <div class="message bot">Rất vui được gặp bạn</div>
    </div>

    <div class="input-box">
        <input id="inputText" placeholder="Nhập câu hỏi...">
        <button id="sendBtn">Gửi</button>
    </div>
</div>


<script src="backEnd/back_end.js"></script>
</body>
</html>