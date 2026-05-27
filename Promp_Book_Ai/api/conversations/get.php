<?php
session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM conversations
     WHERE user_id = ?
     ORDER BY id DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>