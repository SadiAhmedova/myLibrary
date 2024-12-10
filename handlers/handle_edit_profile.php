<?php

require_once('../db.php');

// Проверяваме дали потребителят е логнат
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Няма активна сесия! Моля, влезте отново.";
    header('Location: ../index.php?page=login');  // Пренасочване към страницата за вход
    exit;
}

// Получаваме данни от формата
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$old_password = $_POST['old_password'] ?? '';
$password = $_POST['password'] ?? '';
$repeat_password = $_POST['repeat_password'] ?? '';

// Проверка дали полетата са попълнени
if (empty($username) || empty($email)) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Моля попълнете всички полета!";
    header('Location: ../index.php?page=edit_profile&id=' . $user_id);
    exit;
}



// Проверка за съществуващия потребител
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

// Ако има стара парола, проверяваме дали тя е валидна
if ($old_password) {
    if (!password_verify($old_password, $user['password'])) {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Невалидна стара парола!";
        header('Location: ../index.php?page=edit_profile&id=' . $user_id);
        exit;
    }

    // Проверка дали новата парола и потвърдена парола съвпадат
    if ($password && $password !== $repeat_password) {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Паролите не съвпадат!";
        header('Location: ../index.php?page=edit_profile&id=' . $user_id);
        exit;
    }

    // Хешираме новата парола
    if ($password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }
} else {
    // Ако няма нова парола, използваме старата
    $passwordHash = null;
}
if(($password || $repeat_password) && !$old_password){
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "За да промените паролата си, въведете старата си!";
    header('Location: ../index.php?page=edit_profile&id=' . $user_id);
    exit;
}

// Актуализираме данните на потребителя в базата
$query = "UPDATE users SET username = :username, email = :email" . 
    ($passwordHash ? ", password = :password" : "") . " WHERE id = :id";

$stmt = $pdo->prepare($query);

// Подготовка на параметрите
$params = [
    ':username' => $username,
    ':email' => $email,
    ':id' => $user_id
];

// Ако има нова парола, добавяме я в параметрите
if ($passwordHash) {
    $params[':password'] = $passwordHash;
} else {
    // Ако няма нова парола, не я добавяме
    // Вече задаваме стойността на ':password' в SQL заявката само ако има нова парола.
}

// Изпълняваме заявката
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
