<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCU Admin Login</title>
    <link rel="icon" type="image/png" href="qcu_logo_circular.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
  
    <div id="loading-screen" class="loading-screen">
        <div class="qcu-text">QCU</div>
    </div>

    <div id="main-content" class="main-content hidden">
        <div class="login-container">
            <div class="login-card">
                <div class="logo-section">
                    <img src="logo.jpg" alt="QCU Logo" class="logo">
                    <h1>Quezon City University</h1>
                    <p>Admin Login</p>
                </div>

           
                <form id="loginForm" class="login-form" method="POST" action="login">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div id="error-message" class="error-message hidden"></div>

                    <button type="submit" class="login-btn">Login</button>
                </form>

                <div class="footer">
                    <p>&copy; 2025 Quezon City University</p>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
