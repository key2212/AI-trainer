<?php
header('Content-Type: application/json');

// Nhận dữ liệu JSON từ JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$userPrompt = $input['prompt'] ?? '';

if (empty($userPrompt)) {
    echo json_encode(['error' => 'Chưa có ý chỉ nào được nhập!']);
    exit;
}

/**
 * Ở ĐÂY bạn CÓ THỂ GỌI API GEMINI/OPENAI QUA CURL
 * Hiện tại giả lập logic bookđể bạn chạy thử giao diện
 */

$score = rand(6, 9); // Chấm điểm ngẫu nhiên để thử nghiệm
$feedback = "Prompt của bạn cần thêm bối cảnh cụ thể và đối tượng mục tiêu.";
$optimized = "Với vai trò là [Chuyên gia], hãy giúp tôi [Nhiệm vụ] với các tiêu chí [Yêu cầu]...";

echo json_encode([
    'score' => $score,
    'comment' => $feedback,
    'optimized' => $optimized
]);
?>