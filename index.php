<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <?php
    include 'login-sistem.php';
    ?>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="login-box">
                <form method="POST">
                <div class="input-group">
                        <input name="username" type="text" placeholder="username">
                        <input type="password" name="password" placeholder="password">
                    </div>
                    <button id="login-btn" name="submit" type="submit" class="login-btn">Login</button>
                </form>
                    <div class="forgot-password">lupa password</div>
            </div>
        </div>
    </div>
</body>
</html>
