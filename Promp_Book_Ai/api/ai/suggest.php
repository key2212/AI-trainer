<?php

session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json");

$q = strtolower(trim($_GET['q'] ?? ''));

$user_id = $_SESSION['user_id'] ?? 0;

$user = null;

if($user_id){

    $u = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE id='$user_id'"
    );

    $user = mysqli_fetch_assoc($u);
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM ai_tools"
);

$data = [];

$keywords = [

    "word" => [
        "word",
        "văn bản",
        "cv",
        "document",
        "báo cáo"
    ],

    "code" => [
        "code",
        "php",
        "web",
        "lập trình",
        "html",
        "javascript"
    ],

    "study" => [
        "học",
        "ôn thi",
        "bài tập",
        "ielts",
        "hsk"
    ],

    "writing" => [
        "viết",
        "content",
        "blog",
        "facebook"
    ],

    "research" => [
        "nghiên cứu",
        "phân tích",
        "thống kê"
    ]

];

while($row = mysqli_fetch_assoc($result)){
    $score = 0;
    foreach($keywords as $tag => $list){
        foreach($list as $kw){
            if(strpos($q,$kw) !== false){
                if(strpos($row['tags'],$tag) !== false){
                    $score += 10;
                }
            }
        }
    }

    // USER PREFERENCE
    if ($user && !empty($user['preference'])) {
        $pref = strtolower($user['preference']);
        $tags = explode(",", strtolower($row['tags']));

        foreach ($tags as $tag) {
            $tag = trim($tag);

            if ($tag !== "" && strpos($pref, $tag) !== false) {
                $score += 5;
                break;
            }
        }
    }

    // AI RATING
    $score += $row['rating'];

    // BONUS
    if($row['rating'] >= 9){
        $score += 3;
    }

    $row['score'] = $score;

    $data[] = $row;
}

usort($data,function($a,$b){

    return $b['score'] <=> $a['score'];

});

echo json_encode(
    array_slice($data,0,5)
);

?>