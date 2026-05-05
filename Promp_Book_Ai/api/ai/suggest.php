<?php
include __DIR__ . "/../../config/connect.php";

$q = strtolower($_GET['q']);

$result = mysqli_query($conn,"SELECT * FROM ai_tools");

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $score = 0;

    if(strpos($q,"code") !== false && strpos($row['tags'],"code") !== false){
        $score += 3;
    }

    if(strpos($q,"word") !== false && strpos($row['tags'],"vanban") !== false){
        $score += 3;
    }

    $score += $row['rating'];

    $row['score'] = $score;
    $data[] = $row;
}

usort($data,function($a,$b){
    return $b['score'] <=> $a['score'];
});

echo json_encode(array_slice($data,0,5));
?>