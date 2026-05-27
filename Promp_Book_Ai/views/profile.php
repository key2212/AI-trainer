<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Mục tiêu học tập</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            display: block;
        }

        .profile-box {
            width: 500px;
            margin: 50px auto;
            background: #1e293b;
            padding: 30px;
            border-radius: 15px;
        }

        .profile-box input,
        .profile-box textarea,
        .profile-box select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>

    <div class="profile-box">
        <h1>🎯 Mục tiêu học tập</h1>
        <form action="../api/user/save_preference.php" method="POST">
            <input name="goal" placeholder="Ví dụ: học lập trình web, học IELTS, làm báo cáo..." required>

            <select name="level">
                <option value="beginner">Mới bắt đầu</option>
                <option value="intermediate">Trung bình</option>
                <option value="advanced">Nâng cao</option>
            </select>

            <textarea name="favorite_fields" placeholder="Lĩnh vực quan tâm: code, word, study, writing..."></textarea>

            <button type="submit">Lưu mục tiêu</button>
        </form>

        <br>
        <a style="color:#60a5fa" href="giaoDien.php">← Quay lại</a>
    </div>

</body>

</html>