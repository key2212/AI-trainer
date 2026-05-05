<?php
include __DIR__ . "/../../config/connect.php";

$sql = "INSERT INTO conversations(title) VALUES('')";
mysqli_query($conn,$sql);

echo mysqli_insert_id($conn);
?>