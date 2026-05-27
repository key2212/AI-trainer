<?php
session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$id = intval($_GET['id'] ?? 0);
$user_id = intval($_SESSION['user_id']);

$stmt = mysqli_prepare(
    $conn,
    "SELECT m.*
     FROM messages m
     JOIN conversations c ON m.conversation_id = c.id
     WHERE c.user_id = ?
     AND c.id = ?
     ORDER BY m.id ASC"
);

mysqli_stmt_bind_param($stmt, "ii", $user_id, $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>