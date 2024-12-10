<?php

require_once('../db.php');

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Невалиден идентификатор на книга!";

    header('Location: ../index.php?page=books');
    exit;
}

$query = "DELETE FROM books WHERE id = :id";
$stmt = $pdo->prepare($query);
if ($stmt->execute([':id' => $id])) {
    $_SESSION['flash']['message']['type'] = 'success';
    $_SESSION['flash']['message']['text'] = "Книгата беше изтрита успешно!";
} else {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Възникна грешка при изтриването на книгата!";
}

header('Location: ../index.php?page=books');
exit;

?>