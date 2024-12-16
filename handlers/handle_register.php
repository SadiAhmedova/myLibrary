<?php

require_once('../db.php');

$error = '';

$username = $_POST['username'] ?? ''; 
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$repeat_password = $_POST['repeat_password'] ?? '';

foreach ($_POST as $key => $value) {
    if (empty($value)) {
        $error = 'Моля попълнете всички полета!';
        break;
    }
}
if(!empty($error)){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Невалиден имейл!';
    }
    if (mb_strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = 'Паролата трябва да е поне 8 символа, да съдържа поне една главна буква и цифра!';
    }
    if ($password != $repeat_password) {
        $error = 'Паролите не съвпадат!';
    } 
}else{
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = $error;
    $_SESSION['flash']['data'] = $_POST;
    header('Location: ../index.php?page=register');
    exit;
}

    
    $query = "SELECT id FROM users WHERE email = ? OR username = ? ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email, $username]);
    $user = $stmt->fetch();

    if ($user) {
        $error = 'Имейлът или потребителското име вече съществуват! Опитайте отново.';
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = $error;
        $_SESSION['flash']['data'] = $_POST;
        header('Location: ../index.php?page=register');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, email, `password`) VALUES (:username, :email, :hash)";
    $stmt = $pdo->prepare($query);
    $params = [
        ':username' => $username,
        ':email' => $email,
        ':hash' => $hash
    ];

    if ($stmt->execute($params)) {
        $_SESSION['flash']['message']['type'] = 'success';
        $_SESSION['flash']['message']['text'] = "Успешна регистрация!";
        header('Location: ../index.php?page=login');
        exit;
    } else {
        $error = 'Възникна грешка при регистрацията!';
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = $error;
        $_SESSION['flash']['data'] = $_POST;
        header('Location: ../index.php?page=register');
        exit;
    }


?>