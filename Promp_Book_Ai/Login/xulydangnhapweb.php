<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "Web")
    or die("Không kết nối được");

if (isset($_POST['txtuser']) && isset($_POST['txtpass'])) {

    $tendangnhap = $_POST['txtuser'];
    $matkhau = $_POST['txtpass'];

    $sql = "SELECT * FROM xulydangnhapweb 
            WHERE username='$tendangnhap' 
            AND password='$matkhau'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['user'] = $tendangnhap;
        header("Location: ../giaoDien.php"); // quay lại trang sau
        exit(); // bắt buộc
    } else {
        echo "Tài khoản hoặc mật khẩu không đúng";
    }
}
?>

