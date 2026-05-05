<?php
include __DIR__ . "/../../config/connect.php";

$id = $_POST['id'];
$title = $_POST['title'];

mysqli_query($conn,"UPDATE conversations SET title='$title' WHERE id='$id'");

echo "OK";
?>