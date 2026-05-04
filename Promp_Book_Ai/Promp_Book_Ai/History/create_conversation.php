<?php
include __DIR__ . "/connect.php"; //Là đường dẫn đến thư mục hiện tại
mysqli_query($conn,"INSERT INTO conversations(title) VALUES('')");
echo mysqli_insert_id($conn);
?>