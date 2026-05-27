<?php
include __DIR__ . "/../config/connect.php";

$id = intval($_GET['id'] ?? 0);

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM ai_tools WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$ai = mysqli_fetch_assoc($result);

if (!$ai) {
    echo "Không tìm thấy AI";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($ai['name']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            margin: 0;
            background: #0f172a;
            color: white;
            font-family: Arial;
            padding: 30px;
        }

        .box {
            background: #1e293b;
            padding: 25px;
            border-radius: 15px;
        }

        pre {
            background: #020617;
            padding: 15px;
            border-radius: 10px;
            overflow: auto;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>
            <?php echo htmlspecialchars($ai['name']); ?>
        </h1>

        <p>
            <?php echo htmlspecialchars($ai['description']); ?>
        </p>

        <hr>
        <h2>📘 Hướng dẫn sử dụng</h2>
        <p>
            <?php echo nl2br(htmlspecialchars($ai['guide'])); ?>
        </p>

        <h2>✨ Prompt mẫu</h2>
        <pre>
            Hãy đóng vai chuyên gia Word.
            Tạo cho tôi:
            - Mục lục
            - Format chuyên nghiệp
            - Font đẹp
            - Header/Footer
        </pre>
        <h2> Mẹo tối ưu</h2>
        <ul>
            <li>Mô tả bối cảnh</li>
            <li>Viết prompt rõ mục tiêu</li>
            <li>Đưa ví dụ cụ thể</li>
            <li>Yêu cầu AI theo từng bước</li>
            <li>Đưa ra kết quả mà bản thân đang cần</li>
        </ul>
        <h2>🎯 Trường hợp nên dùng</h2>
        <p>
            <?php echo nl2br(htmlspecialchars($ai['use_cases'] ?? 'Chưa có dữ liệu')); ?>
        </p>

        <h2>🧩 Prompt mẫu riêng cho AI này</h2>
        <pre><?php echo htmlspecialchars($ai['prompt_template'] ?? 'Chưa có prompt mẫu'); ?></pre>

        <h2> Điểm yếu cần chú ý</h2>
        <p>
            <?php echo nl2br(htmlspecialchars($ai['weaknesses'] ?? 'Chưa có dữ liệu')); ?>
        </p>

        <h2>Bài tập thực hành</h2>
        <div class="exercise-box">
            <p>
                <?php echo nl2br(htmlspecialchars($ai['exercises'] ?? 'Chưa có bài tập')); ?>
            </p>

            <form action="../api/exercises/save_result.php" method="POST">
                <input type="hidden" name="ai_tool_id" value="<?php echo intval($ai['id']); ?>">
                <input type="hidden" name="exercise_title" value="Bài tập thực hành với <?php echo htmlspecialchars($ai['name']); ?>">

                <textarea
                    name="user_answer"
                    placeholder="Nhập bài làm của bạn..."
                    style="width:100%;height:120px;border-radius:10px;padding:10px;"
                    required></textarea>

                <button class="small-btn" type="submit">
                    Nộp bài tập
                </button>
            </form>
        </div>

        <?php if (!empty($ai['official_url'])) { ?>
            <h2>🔗 Trang chính thức</h2>
            <a style="color:#60a5fa" href="<?php echo htmlspecialchars($ai['official_url']); ?>" target="_blank">
                Mở trang AI
            </a>
        <?php } ?>
    </div>
</body>

</html>