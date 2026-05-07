<?php
include __DIR__ . "/../../config/connect.php";

// Lấy tiêu đề từ tham số GET, nếu không có thì để mặc định
$title = isset($_GET['title']) ? $_GET['title'] : "Cuộc trò chuyện mới";

// Sử dụng Prepared Statement để bảo mật, tránh phá hoại
$stmt = $conn->prepare("INSERT INTO conversations (title) VALUES (?)");
$stmt->bind_param("s", $title);
$stmt->execute();

echo $conn->insert_id;
?>