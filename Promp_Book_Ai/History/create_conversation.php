<?php
include "connect.php";
$title = "Cuộc hội thoại mới";
mysqli_query($conn,"INSERT INTO conversations(title) VALUES('$title')");
echo mysqli_insert_id($conn);
?>