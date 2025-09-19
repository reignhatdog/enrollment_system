<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}


$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($username) || empty($password)) {
    header('Location: index.php?error=empty');
    exit;
}

// system pass and user
$admin_username = 'reign';
$admin_password = 'admin';

if ($username === $admin_username && $password === $admin_password) {

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    $_SESSION['login_time'] = time();

    header('Location: main.php');
    exit;
} else {
    
    header('Location: index.php?error=invalid');
    exit;
}
?>
