<?php
include __DIR__ . "/../../config/connect.php";

$id = $_GET['id'];

$result = mysqli_query($conn,"
SELECT * FROM messages WHERE conversation_id='$id'
");

$data = [];
while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

echo json_encode($data);
?>