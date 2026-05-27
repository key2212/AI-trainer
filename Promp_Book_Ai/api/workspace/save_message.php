<?php
session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "error" => "Chưa đăng nhập"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$ai_tool_id = intval($_POST['ai_tool_id'] ?? 0);
$sender = trim($_POST['sender'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($ai_tool_id <= 0 || $sender === '' || $content === '') {
    echo json_encode([
        "error" => "Thiếu dữ liệu lưu tin nhắn"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO ai_practice_messages(user_id, ai_tool_id, sender, content)
     VALUES (?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "iiss",
    $user_id,
    $ai_tool_id,
    $sender,
    $content
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "success" => true
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "error" => mysqli_error($conn)
    ], JSON_UNESCAPED_UNICODE);
}
?>