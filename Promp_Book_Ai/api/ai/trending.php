<?php
session_start();

include __DIR__ . "/../../config/connect.php";

header("Content-Type: application/json; charset=utf-8");

$sql = "
SELECT 
    id,
    name,
    logo_url,
    description,
    trend_feature,
    tags,
    rating,
    trend_score,
    trend_rank,
    official_url
FROM ai_tools
WHERE trend_rank > 0
ORDER BY trend_rank ASC
LIMIT 10
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "error" => mysqli_error($conn)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row['guide_url'] = "ai_workspace.php?id=" . intval($row['id']);
    $data[] = $row;
}

/*
    Nếu chưa gán trend_rank thì fallback theo rating.
    Nhờ vậy bảng AI thịnh hành không bị trống khi demo.
*/
if (count($data) === 0) {
    $fallbackSql = "
    SELECT 
        id,
        name,
        logo_url,
        description,
        trend_feature,
        tags,
        rating,
        trend_score,
        trend_rank,
        official_url
    FROM ai_tools
    ORDER BY rating DESC, id ASC
    LIMIT 10
    ";

    $fallbackResult = mysqli_query($conn, $fallbackSql);

    if (!$fallbackResult) {
        echo json_encode([
            "error" => mysqli_error($conn)
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $rank = 1;

    while ($row = mysqli_fetch_assoc($fallbackResult)) {
        $row['trend_rank'] = $rank;
        $row['trend_score'] = $row['trend_score'] > 0
            ? $row['trend_score']
            : $row['rating'];

        $row['guide_url'] = "ai_workspace.php?id=" . intval($row['id']);

        if (empty($row['trend_feature'])) {
            $row['trend_feature'] = $row['description'];
        }

        $data[] = $row;
        $rank++;
    }
}

echo json_encode([
    "items" => $data
], JSON_UNESCAPED_UNICODE);
?>