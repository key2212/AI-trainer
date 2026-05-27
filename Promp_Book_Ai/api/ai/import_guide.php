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

$user_id = intval($_SESSION['user_id']);
$ai_tool_id = intval($_POST['ai_tool_id'] ?? 0);

if ($ai_tool_id <= 0) {
    echo json_encode([
        "error" => "Thiếu mã AI"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_FILES['guide_file'])) {
    echo json_encode([
        "error" => "Chưa chọn file hướng dẫn"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['guide_file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "error" => "Upload file thất bại"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$originalName = $file['name'];
$tmpPath = $file['tmp_name'];
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

$allowed = ['txt', 'md', 'csv', 'json', 'html', 'docx'];

if (!in_array($extension, $allowed)) {
    echo json_encode([
        "error" => "Hiện chỉ hỗ trợ file txt, md, csv, json, html, docx. PDF sẽ nâng cấp sau."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function read_docx_text($filePath) {
    if (!class_exists('ZipArchive')) {
        return "";
    }

    $zip = new ZipArchive();

    if ($zip->open($filePath) !== true) {
        return "";
    }

    $xml = $zip->getFromName("word/document.xml");
    $zip->close();

    if (!$xml) {
        return "";
    }

    $xml = str_replace("</w:p>", "\n", $xml);
    $text = strip_tags($xml);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, "UTF-8");

    return trim($text);
}

if ($extension === 'docx') {
    $rawContent = read_docx_text($tmpPath);
} else {
    $rawContent = file_get_contents($tmpPath);
}

$rawContent = trim($rawContent);

if ($rawContent === '') {
    echo json_encode([
        "error" => "Không đọc được nội dung file. Nếu là docx, hãy bật extension zip trong PHP."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/*
    Giới hạn nội dung gửi cho Ollama để tránh quá dài.
    Sau này có thể nâng cấp chia chunk.
*/
$rawContent = mb_substr($rawContent, 0, 12000, 'UTF-8');

$stmtAI = mysqli_prepare(
    $conn,
    "SELECT name, description, tags
     FROM ai_tools
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmtAI, "i", $ai_tool_id);
mysqli_stmt_execute($stmtAI);

$resultAI = mysqli_stmt_get_result($stmtAI);
$ai = mysqli_fetch_assoc($resultAI);

if (!$ai) {
    echo json_encode([
        "error" => "Không tìm thấy AI trong kho"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$aiName = $ai['name'];

$prompt = "
Bạn là chuyên gia xây dựng tài liệu hướng dẫn sử dụng AI cho sinh viên.

Nhiệm vụ:
Từ tài liệu bên dưới, hãy hệ thống hóa thành một bản hướng dẫn hoàn chỉnh cho AI: {$aiName}.

Yêu cầu trả về DUY NHẤT JSON hợp lệ, không markdown, không giải thích thêm.

Cấu trúc JSON bắt buộc:
{
  \"guide\": \"Bản hướng dẫn đầy đủ, có chia mục rõ ràng: Tổng quan, Khi nào nên dùng, Cách dùng cơ bản, Cách dùng nâng cao, Quy trình thực hành, Lỗi thường gặp, Mẹo tối ưu.\",
  \"prompt_template\": \"Các mẫu prompt tốt, có thể dùng ngay.\",
  \"use_cases\": \"Danh sách các trường hợp nên dùng AI này trong học tập và công việc.\",
  \"exercises\": \"Các bài tập thực hành từ dễ đến khó.\",
  \"weaknesses\": \"Điểm yếu, giới hạn và lưu ý khi sử dụng AI này.\",
  \"trend_feature\": \"Một câu ngắn nêu điểm nổi bật của AI này.\"
}

Tài liệu nguồn:
{$rawContent}
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
    CURLOPT_TIMEOUT => 180,
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

$generated = trim($ollama['response']);

$generated = str_replace("```json", "", $generated);
$generated = str_replace("```JSON", "", $generated);
$generated = str_replace("```", "", $generated);
$generated = trim($generated);

$firstBrace = strpos($generated, "{");
$lastBrace = strrpos($generated, "}");

if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
    $generated = substr($generated, $firstBrace, $lastBrace - $firstBrace + 1);
}

$json = json_decode($generated, true);

if (!$json) {
    echo json_encode([
        "error" => "Ollama chưa trả JSON hợp lệ",
        "raw" => $generated
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$guide = trim($json['guide'] ?? '');
$prompt_template = trim($json['prompt_template'] ?? '');
$use_cases = trim($json['use_cases'] ?? '');
$exercises = trim($json['exercises'] ?? '');
$weaknesses = trim($json['weaknesses'] ?? '');
$trend_feature = trim($json['trend_feature'] ?? '');

if ($guide === '') {
    echo json_encode([
        "error" => "Ollama không tạo được guide"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmtUpdate = mysqli_prepare(
    $conn,
    "UPDATE ai_tools
     SET guide = ?,
         prompt_template = ?,
         use_cases = ?,
         exercises = ?,
         weaknesses = ?,
         trend_feature = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmtUpdate,
    "ssssssi",
    $guide,
    $prompt_template,
    $use_cases,
    $exercises,
    $weaknesses,
    $trend_feature,
    $ai_tool_id
);

if (!mysqli_stmt_execute($stmtUpdate)) {
    echo json_encode([
        "error" => mysqli_error($conn)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmtSource = mysqli_prepare(
    $conn,
    "INSERT INTO ai_guide_sources(
        ai_tool_id,
        user_id,
        file_name,
        file_type,
        raw_content,
        generated_guide
    )
    VALUES (?, ?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmtSource,
    "iissss",
    $ai_tool_id,
    $user_id,
    $originalName,
    $extension,
    $rawContent,
    $guide
);

mysqli_stmt_execute($stmtSource);

echo json_encode([
    "success" => true,
    "message" => "Đã nhập file và tạo hướng dẫn hoàn chỉnh",
    "guide" => $guide
], JSON_UNESCAPED_UNICODE);
?>