<?php
$conn = mysqli_connect("localhost","root","","historyweb");
mysqli_set_charset($conn,"utf8");
if(!$conn){
    die("Can't open data base");
}
?>