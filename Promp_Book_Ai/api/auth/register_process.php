<?php
session_start();

include __DIR__ . "/../../config/connect.php";

$user = trim($_POST['username'] ?? '');
$pass = trim($_POST['password'] ?? '');

if ($user === '' || $pass === '') {
    echo "Thiếu tài khoản hoặc mật khẩu";
    exit;
}

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM users WHERE username = ?"
);

mysqli_stmt_bind_param($check, "s", $user);
mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {
    echo "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
    exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users(username, password) VALUES(?, ?)"
);

mysqli_stmt_bind_param($stmt, "ss", $user, $hash);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../../views/login.php");
    exit;
}

echo "Đăng ký thất bại";
?>