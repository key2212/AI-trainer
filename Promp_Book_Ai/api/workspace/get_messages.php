<?php
session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$ai_tool_id = intval($_GET['ai_tool_id'] ?? 0);

if ($ai_tool_id <= 0) {
    echo json_encode([], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT sender, content, created_at
     FROM ai_practice_messages
     WHERE user_id = ?
     AND ai_tool_id = ?
     ORDER BY id ASC"
);

mysqli_stmt_bind_param($stmt, "ii", $user_id, $ai_tool_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>