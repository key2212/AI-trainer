<?php
include __DIR__ . "/connect.php";
$result = mysqli_query($conn,"SELECT * FROM conversations ORDER BY id DESC");
$data=[];
while($row=mysqli_fetch_assoc($result)){
    $data[]=$row;
}
echo json_encode($data);
?>