<?php

session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: text/plain; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}

$user_id = $_SESSION['user_id'];

$title = "Đoạn chat mới";

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO conversations(user_id, title) VALUES(?, ?)"
);

mysqli_stmt_bind_param($stmt, "is", $user_id, $title);

if (mysqli_stmt_execute($stmt)) {
    echo mysqli_insert_id($conn);
} else {
    echo 0;
}

?>