<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include __DIR__ . "/../config/connect.php";

$result = mysqli_query($conn, "SELECT * FROM ai_tools ORDER BY rating DESC");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kho AI</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            display: block;
        }

        .library-container {
            padding: 30px;
            width: 100%;
            overflow: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            background: #1e293b;
            border: 1px solid #334155;
            padding: 20px;
            border-radius: 15px;
        }

        .card h2 {
            margin-top: 0;
        }

        .card a {
            color: #60a5fa;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="library-container">
        <h1>📚 Kho AI</h1>
        
        <p>
            Đây là nơi lưu các AI phổ biến, hướng dẫn sử dụng, prompt mẫu và bài tập thực hành.
        </p>

        <div class="grid">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="card">
                    <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p>⭐ <?php echo htmlspecialchars($row['rating']); ?></p>

                    <p>
                        <b>Tags:</b>
                        <?php echo htmlspecialchars($row['tags']); ?>
                    </p>
                    <a class="card-btn" href="ai_workspace.php?id=<?php echo intval($row['id']); ?>">
                        Vào hướng dẫn →
                    </a>
                </div>
            <?php } ?>
        </div>
        <br>
        <a style="color:#60a5fa" href="giaoDien.php">← Quay lại chat</a>
    </div>
</body>
</html>