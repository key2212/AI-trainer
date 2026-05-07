<?php
include __DIR__ . "/../../config/connect.php";
header('Content-Type: application/json');

$query = isset($_GET['q']) ? $_GET['q'] : '';

// Truy vấn lấy Top 5 dựa trên điểm số (score) và từ khóa (tags)
$sql = "SELECT id, name, score, description, tags FROM ai_tools 
        WHERE tags LIKE ? OR description LIKE ? 
        ORDER BY score DESC LIMIT 5";

$stmt = $conn->prepare($sql);
$searchTerm = "%$query%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$aiList = [];
while($row = $result->fetch_assoc()) {
    $aiList[] = $row;
}

echo json_encode($aiList);
?>