<?php

session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: text/plain; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo "not_login";
    exit;
}

$user_id = $_SESSION['user_id'];

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');

if ($id <= 0 || $title === '') {
    echo "missing_data";
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE conversations SET title = ? WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($stmt, "sii", $title, $id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error";
}

?>