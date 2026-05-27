<?php

session_start();

include __DIR__ . "/../../config/connect.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../../views/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$goal = trim($_POST['goal'] ?? '');
$level = trim($_POST['level'] ?? '');
$favorite_fields = trim($_POST['favorite_fields'] ?? '');

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO user_preferences(user_id, goal, level, favorite_fields)
     VALUES(?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "isss",
    $user_id,
    $goal,
    $level,
    $favorite_fields
);

mysqli_stmt_execute($stmt);

$preference = $goal . " " . $level . " " . $favorite_fields;

$stmt2 = mysqli_prepare(
    $conn,
    "UPDATE users SET preference = ? WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt2,
    "si",
    $preference,
    $user_id
);

mysqli_stmt_execute($stmt2);

header("Location: ../../views/giaoDien.php");
exit;

?>