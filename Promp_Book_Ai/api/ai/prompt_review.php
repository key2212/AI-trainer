<?php
session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

$promptOriginal = trim($_POST['prompt'] ?? '');

if (function_exists('mb_strtolower')) {
    $prompt = mb_strtolower($promptOriginal, 'UTF-8');
} else {
    $prompt = strtolower($promptOriginal);
}

$score = 0;
$feedback = [];

if ($prompt === '') {
    echo json_encode([
        "score" => 0,
        "feedback" => ["Prompt rỗng"]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$promptLength = function_exists('mb_strlen')
    ? mb_strlen($prompt, 'UTF-8')
    : strlen($prompt);

if ($promptLength >= 30) {
    $score += 2;
} else {
    $feedback[] = "Prompt còn ngắn, nên mô tả rõ hơn.";
}

if (
    strpos($prompt, "đóng vai") !== false ||
    strpos($prompt, "vai trò") !== false ||
    strpos($prompt, "chuyên gia") !== false
) {
    $score += 2;
} else {
    $feedback[] = "Nên thêm vai trò cho AI, ví dụ: Hãy đóng vai giáo viên lập trình web.";
}

if (
    strpos($prompt, "mục tiêu") !== false ||
    strpos($prompt, "tôi muốn") !== false ||
    strpos($prompt, "giúp tôi") !== false
) {
    $score += 2;
} else {
    $feedback[] = "Nên nói rõ mục tiêu cần đạt.";
}

if (
    strpos($prompt, "ví dụ") !== false ||
    strpos($prompt, "minh họa") !== false
) {
    $score += 1;
} else {
    $feedback[] = "Nên yêu cầu AI đưa ví dụ.";
}

if (
    strpos($prompt, "từng bước") !== false ||
    strpos($prompt, "các bước") !== false
) {
    $score += 1;
} else {
    $feedback[] = "Nên yêu cầu AI hướng dẫn từng bước.";
}

if (
    strpos($prompt, "bảng") !== false ||
    strpos($prompt, "danh sách") !== false ||
    strpos($prompt, "bullet") !== false
) {
    $score += 1;
} else {
    $feedback[] = "Nên yêu cầu định dạng đầu ra rõ ràng.";
}

if (
    strpos($prompt, "bài tập") !== false ||
    strpos($prompt, "thực hành") !== false
) {
    $score += 1;
} else {
    $feedback[] = "Nên yêu cầu bài tập thực hành nếu mục tiêu là học tập.";
}

if ($score > 10) {
    $score = 10;
}

$feedbackText = implode(" | ", $feedback);

if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO prompt_history(user_id, prompt, score, feedback)
         VALUES(?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "isis",
        $user_id,
        $promptOriginal,
        $score,
        $feedbackText
    );

    mysqli_stmt_execute($stmt);
}

echo json_encode([
    "score" => $score,
    "feedback" => $feedback
], JSON_UNESCAPED_UNICODE);
?>