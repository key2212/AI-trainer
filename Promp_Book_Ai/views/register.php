<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>

    <style>
        body{
            margin:0;
            background:#0f172a;
            font-family:Arial;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            color:white;
        }

        .box{
            width:350px;
            background:#1e293b;
            padding:30px;
            border-radius:15px;
        }

        input{
            width:100%;
            padding:12px;
            margin-bottom:15px;
            border:none;
            border-radius:10px;
        }

        button{
            width:100%;
            padding:12px;
            border:none;
            border-radius:10px;
            background:#2563eb;
            color:white;
            cursor:pointer;
        }

        a{
            color:#60a5fa;
            text-decoration:none;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Đăng ký</h2>

    <form action="../api/auth/register_process.php" method="POST">

        <input type="text"
               name="username"
               placeholder="Tên đăng nhập"
               required>

        <input type="password"
               name="password"
               placeholder="Mật khẩu"
               required>

        <button type="submit">
            Đăng ký
        </button>
    </form>

    <br>
    <a href="login.php">
        Đã có tài khoản? Đăng nhập
    </a>
</div>

</body>
</html>