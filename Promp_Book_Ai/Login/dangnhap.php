<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #36d1dc, #5b86e5);
        }

        .container {
            background: #111;
            padding: 35px;
            border-radius: 15px;
            width: 350px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            text-align: center;
        }

        h2 {
            color: white;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            outline: none;
            background: #333;
            color: white;
            font-size: 14px;
        }

        input::placeholder {
            color: #aaa;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #4facfe;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #007bff;
            transform: scale(1.05);
        }

        .footer {
            margin-top: 15px;
            color: #aaa;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Đăng nhập</h2>
    <form action="xulydangnhapweb.php" method="POST">
        <div class="input-group">
            <input type="text" name="txtuser" placeholder="Tên đăng nhập" required>
        </div>

        <div class="input-group">
            <input  type="password" name="txtpass" placeholder="Mật khẩu" required>
        </div>

        <button type="submit">Đăng nhập</button>
    </form>

    <div class="footer">
        © 2026 - Bài tập làm web    
    </div>
</div>

</body>
</html>


