<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password</title>
    <style>
        * { box-sizing: border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
        body { background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 320px; text-align: center; }
        h2 { color: #007bff; margin-bottom: 20px; }
        p { font-size: 14px; color: #555; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; }
        button { width: 100%; padding: 14px; background-color: #007bff; color: #fff; font-size: 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        button:hover { background-color: #0056b3; }
        .back-link { margin-top: 15px; font-size: 13px; display:block; }
        .back-link a { text-decoration: none; color: #007bff; }
        .back-link a:hover { text-decoration: underline; }
        .message { margin-top: 10px; font-size: 14px; color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lupa Password</h2>
        <p>Masukkan username atau email Anda untuk reset password</p>
        
        <form method="post" action="reset_password.php">
            <input type="text" name="username" placeholder="Username atau Email" required>
            <button type="submit">Reset Password</button>
        </form>

        <div class="back-link">
            <a href="index.php">&larr; Kembali ke Login</a>
        </div>
    </div>
</body>
</html>