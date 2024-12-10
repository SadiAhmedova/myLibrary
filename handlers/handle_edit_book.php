<?php

require_once('../db.php');


$id = intval($_POST['id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? null;

$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$genre = $_POST['genre'] ?? '';
$image = $_FILES['image'] ?? '';
$pages = $_POST['pages'] ?? '';
$content = $_POST['content'] ?? '';
$language = $_POST['language'] ?? '';

if (empty($title)|| empty($author) || empty($genre)||empty($pages)||empty ($content)||empty($language)) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Моля попълнете всички полета!";

    header('Location: ../index.php?page=edit_book&id=' . $id);
    exit;
}

$new_file_name = null;
if ($image && $image['error'] == 0) {
    $new_file_name = time() . '_' . $image['name'];
    $upload_dir = '../uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (!move_uploaded_file($image['tmp_name'], $upload_dir . $new_file_name)) {
        $_SESSION['flash']['message']['type'] = 'danger';
        $_SESSION['flash']['message']['text'] = "Възникна грешка при качването на корицата!";
        header('Location: ../index.php?page=edit_book&id=' . $id);
        exit;
    }
} else {
    $query = "SELECT image FROM books WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $book = $stmt->fetch();

    $new_file_name = $book['image'];

}


$query = "
    UPDATE books
    SET title = :title, author = :author, genre = :genre, pages = :pages, user_id = :user_id, content = :content, image = :image, language = :language
    WHERE id = :id
";
$stmt = $pdo->prepare($query);
$params = [
    ':title' => $title,
    ':author' => $author,
    ':genre' => $genre,
    ':pages' => $pages,
    ':content' => $content,
    ':user_id' => $user_id,
    ':image' =>$new_file_name,
    ':id' => $id,
    ':language' => $language
];

if ($stmt->execute($params)) {
    $_SESSION['flash']['message']['type'] = 'success';
    $_SESSION['flash']['message']['text'] = "Книгата беше редактирана успешно!";
} else {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Възникна грешка при редакцията на книгата!";
}

header('Location: ../index.php?page=books');
exit;

?>