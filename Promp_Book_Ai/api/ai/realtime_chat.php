<?php

session_start();

include __DIR__ . "/../../config/env.php";
include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "error" => "Chưa đăng nhập"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$prompt = trim($_POST['prompt'] ?? '');
$conversation_id = intval($_POST['conversation_id'] ?? 0);

if ($prompt === '') {
    echo json_encode([
        "error" => "Prompt rỗng"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($conversation_id <= 0) {
    echo json_encode([
        "error" => "Thiếu mã hội thoại"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Kiểm tra hội thoại có thuộc user không */
$check = mysqli_prepare(
    $conn,
    "SELECT id FROM conversations WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($check, "ii", $conversation_id, $user_id);
mysqli_stmt_execute($check);

$checkResult = mysqli_stmt_get_result($check);

if (mysqli_num_rows($checkResult) === 0) {
    echo json_encode([
        "error" => "Không có quyền với hội thoại này"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Lấy 10 tin nhắn gần nhất để tạo ngữ cảnh */
$stmt = mysqli_prepare(
    $conn,
    "SELECT sender, content
     FROM messages
     WHERE conversation_id = ?
     ORDER BY id DESC
     LIMIT 10"
);

mysqli_stmt_bind_param($stmt, "i", $conversation_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$historyRows = [];

while ($row = mysqli_fetch_assoc($result)) {
    $historyRows[] = $row;
}

$historyRows = array_reverse($historyRows);

$historyText = "";

foreach ($historyRows as $row) {
    $role = $row['sender'] === 'user' ? "Người dùng" : "AI";
    $historyText .= $role . ": " . $row['content'] . "\n";
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
Bạn là AI Trainer - trợ lý hướng dẫn sinh viên học cách sử dụng AI trong học tập.

Vai trò:
1. Giải thích dễ hiểu cho sinh viên 
2. Gợi ý công cụ AI phù hợp theo mục đích.
3. Hướng dẫn cách viết prompt.
4. Đưa ví dụ prompt mẫu.
5. Giao bài tập nhỏ để người dùng thực hành.
6. Nếu người dùng hỏi về ChatGPT, hãy hướng dẫn cách dùng ChatGPT, nhưng nói rằng hệ thống hiện đang chạy thử bằng Ollama local.
7. Trả lời bằng tiếng Việt.
8. Câu trả lời có cấu trúc rõ ràng.

Định dạng nên dùng:
- Giải thích ngắn gọn
- Các bước thực hiện
- Prompt mẫu
- Bài tập thực hành
- Mẹo nâng cao nếu cần

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

$finalPrompt =
$systemPrompt .
"\n\nLịch sử hội thoại gần đây:\n" .
$historyText .
"\nCâu hỏi mới của người dùng:\n" .
$prompt;

$data = [
    "model" => OLLAMA_MODEL,
    "prompt" => $finalPrompt,
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
        "error" => curl_error($ch)
    ], JSON_UNESCAPED_UNICODE);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode([
        "error" => "Ollama trả về HTTP code: " . $httpCode
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo $response;

?>