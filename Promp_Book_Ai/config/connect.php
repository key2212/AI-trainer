<?php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "ai_platform"
);

if(!$conn){
    die("Lỗi kết nối database");
}

mysqli_set_charset($conn,"utf8mb4");
?>