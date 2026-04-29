<?php
include "connect.php";

$conv_id = $_POST['conv_id'];
$sender = $_POST['sender'];
$content = $_POST['content'];

if (empty($conv_id) || empty($content)) {
    die("Thiếu dữ liệu");
}

$sql = "INSERT INTO messages(conversation_id, sender, content)
        VALUES('$conv_id','$sender','$content')";

if (mysqli_query($conn, $sql)) {
    echo "OK";
} else {
    echo "Lỗi: " . mysqli_error($conn);
}
    