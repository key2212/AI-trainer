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

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode([
        "error" => "Thiếu nhu cầu người dùng"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$result = mysqli_query(
    $conn,
    "SELECT 
        id,
        name,
        description,
        tags,
        rating,
        use_cases,
        weaknesses,
        official_url
     FROM ai_tools
     ORDER BY rating DESC"
);

$tools = [];

while ($row = mysqli_fetch_assoc($result)) {
    $tools[] = $row;
}

if (count($tools) === 0) {
    echo json_encode([
        "error" => "Kho AI chưa có dữ liệu"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$toolText = "";

foreach ($tools as $tool) {
    $toolText .= "ID: " . $tool['id'] . "\n";
    $toolText .= "Tên: " . $tool['name'] . "\n";
    $toolText .= "Mô tả: " . $tool['description'] . "\n";
    $toolText .= "Tags: " . $tool['tags'] . "\n";
    $toolText .= "Rating: " . $tool['rating'] . "\n";
    $toolText .= "Use cases: " . $tool['use_cases'] . "\n";
    $toolText .= "Điểm yếu: " . $tool['weaknesses'] . "\n";
    $toolText .= "-----\n";
}

$prompt = "
Bạn là hệ thống gợi ý AI cho sinh viên.

Nhiệm vụ:
Dựa vào nhu cầu của người dùng và danh sách AI có sẵn trong kho, hãy chọn đúng TOP 5 AI phù hợp nhất.

Yêu cầu trả về:
Chỉ trả về JSON hợp lệ, không giải thích ngoài JSON.

Định dạng JSON:
{
  \"items\": [
    {
      \"id\": 1,
      \"reason\": \"Lý do phù hợp ngắn gọn\",
      \"score\": 95
    }
  ]
}

Quy tắc:
- Chỉ chọn AI có trong danh sách.
- ID phải đúng theo danh sách.
- score từ 1 đến 100.
- TOP 1 là AI phù hợp nhất.
- Nếu nhu cầu là học code, ưu tiên AI mạnh về code, học tập, giải thích.
- Nếu nhu cầu là Word, báo cáo, văn bản, ưu tiên AI mạnh về văn bản.
- Nếu nhu cầu là nghiên cứu, ưu tiên AI mạnh về phân tích, tổng hợp, nghiên cứu.
- Nếu nhu cầu là học ngoại ngữ, ưu tiên AI mạnh về học tập, luyện tập, phản hồi.

Nhu cầu người dùng:
$q

Danh sách AI:
$toolText
";

$data = [
    "model" => OLLAMA_MODEL,
    "prompt" => $prompt,
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

$ollamaData = json_decode($response, true);

if (!isset($ollamaData['response'])) {
    echo json_encode([
        "error" => "Ollama không trả response hợp lệ",
        "raw" => $response
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = trim($ollamaData['response']);

$raw = str_replace("```json", "", $raw);
$raw = str_replace("```JSON", "", $raw);
$raw = str_replace("```", "", $raw);
$raw = trim($raw);

/* Nếu Ollama nói lan man, cố gắng cắt lấy phần JSON */
$firstBrace = strpos($raw, "{");
$lastBrace = strrpos($raw, "}");

if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
    $raw = substr($raw, $firstBrace, $lastBrace - $firstBrace + 1);
}

$rankData = json_decode($raw, true);

if (!$rankData || !isset($rankData['items'])) {
    echo json_encode([
        "error" => "Ollama trả JSON không hợp lệ",
        "raw" => $raw
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$toolMap = [];

foreach ($tools as $tool) {
    $toolMap[$tool['id']] = $tool;
}

$final = [];

foreach ($rankData['items'] as $item) {
    $id = intval($item['id'] ?? 0);

    if (!isset($toolMap[$id])) {
        continue;
    }

    $tool = $toolMap[$id];

    $final[] = [
        "id" => $tool['id'],
        "name" => $tool['name'],
        "description" => $tool['description'],
        "tags" => $tool['tags'],
        "rating" => $tool['rating'],
        "official_url" => $tool['official_url'],
        "reason" => $item['reason'] ?? "Phù hợp với nhu cầu của người dùng.",
        "score" => $item['score'] ?? 0,
        "guide_url" => "ai_workspace.php?id=" . $tool['id']
    ];
}

echo json_encode([
    "items" => array_slice($final, 0, 5)
], JSON_UNESCAPED_UNICODE);
?>