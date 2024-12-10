<?php

require_once('../db.php');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$login_error = false;

if (empty($username) || empty($password)) {
    $login_error = true;
} else {
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        $login_error = true;
    } else {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];

            setcookie('username', $user['username'], time() + 3600, '/', 'localhost', false, true);
        } else {
            $login_error = true;
        }
    }
}

if ($login_error) {
    header('Location: ../index.php?page=login&error');
    exit;
}

if (isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit;
} else {
    header('Location: ../index.php?page=login&error');
    exit;
}