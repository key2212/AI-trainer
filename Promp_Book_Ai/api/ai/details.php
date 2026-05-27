<?php
include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode([
        "error" => "Thiếu id AI"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM ai_tools WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo json_encode([
        "error" => "Không tìm thấy AI"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>