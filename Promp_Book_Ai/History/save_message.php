<?php
include __DIR__ . "/connect.php";
$conv_id = $_POST['conv_id'];
$sender = $_POST['sender'];
$content = $_POST['content'];

mysqli_query($conn,"
INSERT INTO messages(conversation_id,sender,content)
VALUES('$conv_id','$sender','$content')
");

echo "OK";
?>