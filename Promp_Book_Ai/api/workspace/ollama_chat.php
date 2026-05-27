<?php
session_start();

include __DIR__ . "/../../config/connect.php";
include __DIR__ . "/../../config/env.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "error" => "Chưa đăng nhập"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$ai_tool_id = intval($_POST['ai_tool_id'] ?? 0);
$prompt = trim($_POST['prompt'] ?? '');

if ($ai_tool_id <= 0 || $prompt === '') {
    echo json_encode([
        "error" => "Thiếu AI hoặc nội dung câu hỏi"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT name, description, guide, prompt_template, use_cases, weaknesses
     FROM ai_tools
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $ai_tool_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$ai = mysqli_fetch_assoc($result);

if (!$ai) {
    echo json_encode([
        "error" => "Không tìm thấy AI"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$systemPrompt = "
Bạn là AI Trainer trong một website hướng dẫn sinh viên sử dụng AI.

Người dùng đang thực hành với AI: {$ai['name']}

Thông tin AI:
Mô tả: {$ai['description']}
Hướng dẫn: {$ai['guide']}
Prompt mẫu: {$ai['prompt_template']}
Use cases: {$ai['use_cases']}
Điểm yếu: {$ai['weaknesses']}

Yêu cầu trả lời:
- Trả lời bằng tiếng Việt.
- Giải thích rõ ràng, thực tế.
- Nếu người dùng hỏi cách dùng AI này, hãy hướng dẫn từng bước.
- Nếu người dùng gửi prompt, hãy góp ý và sửa prompt tốt hơn.
- Nếu phù hợp, hãy giao bài tập nhỏ để người dùng luyện.
- Không bịa rằng bạn đang gọi API thật của AI đó. Hiện tại bạn là Ollama đóng vai AI Trainer.
";

$fullPrompt = $systemPrompt . "\n\nCâu hỏi của người dùng:\n" . $prompt;

$data = [
    "model" => OLLAMA_MODEL,
    "prompt" => $fullPrompt,
    "stream" => false
];

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => OLLAMA_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE)
]);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode([
        "error" => "Không gọi được Ollama: " . curl_error($ch)
    ], JSON_UNESCAPED_UNICODE);
    curl_close($ch);
    exit;
}

curl_close($ch);

$ollama = json_decode($response, true);

if (!isset($ollama['response'])) {
    echo json_encode([
        "error" => "Ollama trả dữ liệu không hợp lệ",
        "raw" => $response
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    "reply" => trim($ollama['response'])
], JSON_UNESCAPED_UNICODE);
?>  