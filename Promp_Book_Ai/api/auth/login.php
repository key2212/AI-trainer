<?php
session_start();

include __DIR__ . "/../../config/connect.php";

$user = ($_POST['username'] ?? '');
$pass = ($_POST['password'] ?? '');

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM users WHERE username = ?"
);

mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    if(password_verify($pass, $row['password'])){
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: ../../views/giaoDien.php");
        exit;
    }
}
header("Location: ../../views/login.php?error=1");
exit;
?>