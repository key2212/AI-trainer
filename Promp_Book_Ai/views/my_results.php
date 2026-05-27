<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include __DIR__ . "/../config/connect.php";

$user_id = intval($_SESSION['user_id']);

$stmt = mysqli_prepare(
    $conn,
    "SELECT r.*, a.name AS ai_name
     FROM user_exercise_results r
     JOIN ai_tools a ON r.ai_tool_id = a.id
     WHERE r.user_id = ?
     ORDER BY r.created_at DESC"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kết quả học tập</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            display: block;
        }

        .result-container {
            padding: 30px;
            width: 100%;
            overflow: auto;
        }

        .result-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .result-card h2 {
            margin-top: 0;
        }

        .score {
            color: #facc15;
            font-size: 22px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="result-container">
        <h1>📊 Kết quả học tập của tôi</h1>

        <?php if (mysqli_num_rows($result) === 0) { ?>
            <p>Bạn chưa nộp bài tập nào.</p>
        <?php } ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <div class="result-card">
                <h2>
                    <?php echo htmlspecialchars($row['exercise_title']); ?>
                </h2>

                <p>
                    <b>AI:</b>
                    <?php echo htmlspecialchars($row['ai_name']); ?>
                </p>

                <p class="score">
                    Điểm: <?php echo htmlspecialchars($row['score']); ?>/10
                </p>

                <p>
                    <b>Bài làm:</b><br>
                    <?php echo nl2br(htmlspecialchars($row['user_answer'])); ?>
                </p>

                <p>
                    <b>Góp ý:</b><br>
                    <?php echo nl2br(htmlspecialchars($row['feedback'])); ?>
                </p>

                <p>
                    <b>Ngày nộp:</b>
                    <?php echo htmlspecialchars($row['created_at']); ?>
                </p>
            </div>

        <?php } ?>

        <br>
        <a style="color:#60a5fa" href="giaoDien.php">← Quay lại chat</a>
    </div>

</body>

</html>