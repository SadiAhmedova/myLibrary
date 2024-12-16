<?php

require_once('../db.php');

$id = intval($_SESSION['user_id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Невалиден идентификатор на потребител!";

    header('Location: ../index.php?page=profile&id=' . $id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute([':id' => $id])) {
        $_SESSION['flash']['message']['type'] = 'success';
        $_SESSION['flash']['message']['text'] = "Профилът беше изтрит успешно!";
        session_unset();
        session_destroy();
    } else {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Възникна грешка при изтриването на профилът Ви!";
    }
    
    header('Location: ../index.php');
    exit;
}

?>