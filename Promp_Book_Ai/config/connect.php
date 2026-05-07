<?php
$conn = mysqli_connect("localhost","root","","ai_platform");
if(!$conn){
    die("Lỗi kết nối DB");
}
mysqli_set_charset($conn,"utf8");
?>