<?php

session_start();

include __DIR__ . "/../../config/connect.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../../views/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$ai_tool_id = intval($_POST['ai_tool_id'] ?? 0);
$exercise_title = trim($_POST['exercise_title'] ?? '');
$user_answer = trim($_POST['user_answer'] ?? '');

if($ai_tool_id <= 0 || $user_answer === ''){
    echo "Thiếu dữ liệu bài tập";
    exit;
}

$score = 0;
$feedback = [];

$answerLength = function_exists('mb_strlen')
    ? mb_strlen($user_answer, 'UTF-8')
    : strlen($user_answer);

if($answerLength >= 50){
    $score += 4;
}else{
    $feedback[] = "Bài làm còn ngắn.";
}

if(strpos(strtolower($user_answer), "prompt") !== false){
    $score += 2;
}else{
    $feedback[] = "Nên thể hiện rõ prompt hoặc cách dùng AI.";
}

if(
    strpos(strtolower($user_answer), "mục tiêu") !== false ||
    strpos(strtolower($user_answer), "giúp tôi") !== false
){
    $score += 2;
}else{
    $feedback[] = "Nên nêu rõ mục tiêu.";
}

if(
    strpos(strtolower($user_answer), "ví dụ") !== false ||
    strpos(strtolower($user_answer), "từng bước") !== false
){
    $score += 2;
}else{
    $feedback[] = "Nên có ví dụ hoặc yêu cầu từng bước.";
}

if($score > 10){
    $score = 10;
}

$feedback_text = implode(" ", $feedback);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO user_exercise_results(
        user_id,
        ai_tool_id,
        exercise_title,
        user_answer,
        score,
        feedback
    ) VALUES (?, ?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "iissds",
    $user_id,
    $ai_tool_id,
    $exercise_title,
    $user_answer,
    $score,
    $feedback_text
);

if(mysqli_stmt_execute($stmt)){
    echo "
    <h2>Đã nộp bài tập</h2>
    <p>Điểm: $score/10</p>
    <p>Góp ý: $feedback_text</p>
    <a href='../../views/ai_workspace.php?id=$ai_tool_id'>Quay lại workspace</a>
    ";
}else{
    echo "Lưu bài tập thất bại";
}

?>