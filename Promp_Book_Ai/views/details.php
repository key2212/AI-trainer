<?php
include __DIR__ . "/../config/connect.php";

$keyword = trim($_GET['q'] ?? '');
$search = "%" . $keyword . "%";

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM ai_tools
     WHERE tags LIKE ?
     ORDER BY rating DESC
     LIMIT 5"
);

mysqli_stmt_bind_param($stmt, "s", $search);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kết quả AI</title>

<style>
body{
    margin:0;
    background:#0f172a;
    color:white;
    font-family:Arial;
    padding:30px;
}

.ai-card{
    background:#1e293b;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}

.rank{
    color:#facc15;
    font-size:22px;
}

a{
    color:#60a5fa;
    text-decoration:none;
}
</style>
</head>

<body>

<h1>TOP AI phù hợp 👑</h1>

<?php
$rank = 1;

if (mysqli_num_rows($result) === 0) {
    echo "<p>Không tìm thấy AI phù hợp.</p>";
}

while($row = mysqli_fetch_assoc($result)){
?>

<div class="ai-card">
    <div class="rank">
        #<?php echo $rank; ?>
    </div>

    <h2>
        <?php echo htmlspecialchars($row['name']); ?>
    </h2>

    <p>
        <?php echo htmlspecialchars($row['description']); ?>
    </p>

    <a href="ai_detail.php?id=<?php echo intval($row['id']); ?>">
        Xem hướng dẫn sử dụng →
    </a>
</div>

<?php
$rank++;
}
?>

<br>
<a href="giaoDien.php">← Quay lại chat</a>

</body>
</html>