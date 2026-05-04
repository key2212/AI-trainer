<?php
include __DIR__ . "/connect.php";

$id = $_POST['id'];
$title = $_POST['title'];

$sql = "UPDATE conversations SET title='$title' WHERE id='$id'";

if(mysqli_query($conn, $sql)){
    echo "OK";
}else{
    echo "ERROR: " . mysqli_error($conn);
}
?>