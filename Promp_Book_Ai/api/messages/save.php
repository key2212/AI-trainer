<?php

session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: text/plain; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo "not_login";
    exit;
}

$user_id = $_SESSION['user_id'];

$conv_id = intval($_POST['conversation_id'] ?? 0);
$sender = $_POST['sender'] ?? '';
$content = $_POST['content'] ?? '';

if ($conv_id <= 0 || $sender === '' || $content === '') {
    echo "missing_data";
    exit;
}

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM conversations WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($check, "ii", $conv_id, $user_id);
mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) === 0) {
    echo "no_permission";
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO messages(conversation_id, sender, content) VALUES(?, ?, ?)"
);

mysqli_stmt_bind_param($stmt, "iss", $conv_id, $sender, $content);

if (mysqli_stmt_execute($stmt)) {
    echo "saved";
} else {
    echo "save_error";
}

?>