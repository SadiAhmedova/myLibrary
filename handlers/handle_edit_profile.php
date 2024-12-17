<?php

require_once('../db.php');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Няма активна сесия! Моля, влезте отново.";
    header('Location: ../index.php?page=login');
    exit;
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$old_password = $_POST['old_password'] ?? '';
$password = $_POST['password'] ?? '';
$repeat_password = $_POST['repeat_password'] ?? '';

if (empty($username) || empty($email)) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Моля попълнете всички полета!";
    header('Location: ../index.php?page=edit_profile&id=' . $user_id);
    exit;
}

$query = "SELECT password FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

if ($user === false) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Грешка при намиране на потребителя!";
    header('Location: ../index.php?page=edit_profile&id=' . $user_id);
    exit;
}

if ($old_password) {
    if (!password_verify($old_password, $user['password'])) {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Невалидна стара парола!";
        header('Location: ../index.php?page=edit_profile&id=' . $user_id);
        exit;
    }

    if ($password && $password !== $repeat_password) {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Паролите не съвпадат!";
        header('Location: ../index.php?page=edit_profile&id=' . $user_id);
        exit;
    }

    if (mb_strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Паролата трябва да е поне 8 символа, да съдържа поне една главна буква и цифра!";
        header('Location: ../index.php?page=edit_profile&id=' . $user_id);
        exit;
    }

    if ($password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }
} else {
    $passwordHash = null;
}

if (($password || $repeat_password) && !$old_password) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "За да промените паролата си, въведете старата си!";
    header('Location: ../index.php?page=edit_profile&id=' . $user_id);
    exit;
}

$query = "UPDATE users SET username = :username, email = :email" . 
    ($passwordHash ? ", password = :password" : "") . " WHERE id = :id";

$stmt = $pdo->prepare($query);

$params = [
    ':username' => $username,
    ':email' => $email,
    ':id' => $user_id
];

if ($passwordHash) {
    $params[':password'] = $passwordHash;
}

try {
    if ($stmt->execute($params)) {
        $_SESSION['flash']['message']['type'] = 'success';
        $_SESSION['flash']['message']['text'] = "Профилът беше редактиран успешно!";
    } else {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Възникна грешка при редактиране на профила!";
    }
} catch (PDOException $e) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Грешка в заявката: " . $e->getMessage();
}

header('Location: ../index.php?page=profile&id=' . $user_id);
exit;

?>