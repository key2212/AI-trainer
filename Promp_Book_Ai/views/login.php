<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>AI Trainer - Đăng nhập</title>

    <link rel="stylesheet"
        href="../assets/css/style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #020617;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        .login-box {
            width: 400px;
            background: #0f172a;
            padding: 40px;
            border-radius: 20px;
            border: 1px solid #1e293b;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);
        }

        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .error-box {
            background: #7f1d1d;
            color: #fecaca;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #94a3b8;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #1e293b;
            color: white;
            outline: none;
            box-sizing: border-box;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: #2563eb;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
        }

        .login-btn:hover {
            background: #1d4ed8;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #94a3b8;
        }

        .register-link a {
            color: #60a5fa;
            text-decoration: none;
        }
    </style>

</head>

<body>

    <div class="login-box">

        <div class="logo">
            AI Trainer 🤖
        </div>

        <?php
        if (isset($_GET['error'])) {
            echo '
            <div class="error-box">
                Sai tài khoản hoặc mật khẩu
            </div>
            ';
        }
        ?>
        <form action="../api/auth/login.php"
            method="POST">

            <div class="input-group">

                <label>
                    Tên đăng nhập
                </label>
                <input
                    type="text"
                    name="username"
                    required>

            </div>
            <div class="input-group">

                <label>
                    Mật khẩu
                </label>

                <input
                    type="password"
                    name="password"
                    required>

            </div>

            <button class="login-btn"
                type="submit">
                Đăng nhập
            </button>

        </form>
        <div class="register-link">
            Chưa có tài khoản?
            <a href="register.php">
                Đăng ký
            </a>

        </div>

    </div>

</body>
</html>