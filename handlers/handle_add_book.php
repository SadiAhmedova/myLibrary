<?php

require_once('../db.php');


$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$genre = $_POST['genre'] ?? '';
$image = $_FILES['image'] ?? '';
$pages = $_POST['pages'] ?? '';
$content = $_POST['content'] ?? '';
$language = $_POST['language'] ?? '';

$user_id = $_SESSION['user_id'] ?? null;



if (empty($title)|| empty($author) || empty($genre)||empty($pages)||empty($content) ||empty($language)) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Моля попълнете всички полета!";


    $_SESSION['form_data'] = [
        'title' => $title,
        'author' => $author,
        'genre' => $genre,
        'pages' => $pages,
        'content' => $content,
        'language' =>  $language
    ];


    header('Location: ../index.php?page=add_book');
    exit;
}

if (empty($image['name']) || $image['error'] != 0) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Моля качете снимка!";

    $_SESSION['form_data'] = [
        'title' => $title,
        'author' => $author,
        'genre' => $genre,
        'pages' => $pages,
        'content' => $content,
        'language' => $language
    ];

    header('Location: ../index.php?page=add_book');
    exit;
}

$new_file_name = time() . '_' . $image['name'];
$upload_dir = '../uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (!move_uploaded_file($image['tmp_name'], $upload_dir . $new_file_name)) {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Възникна грешка при качването на корицата!";

    $_SESSION['form_data'] = [
        'title' => $title,
        'author' => $author,
        'genre' => $genre,
        'pages' => $pages,
        'content' => $content,
        'language' => $language
    ];

    header('Location: ../index.php?page=add_book');
    exit;
}

$query = "INSERT INTO books (title, author, genre, pages, content, user_id, image, language) VALUES (:title, :author, :genre, :pages, :content, :user_id, :image, :language)";
$stmt = $pdo->prepare($query);
$params = [
    ':title' => $title,
    ':author' => $author,
    ':genre' => $genre,
    ':pages' => $pages,
    ':content' => $content,
    ':user_id' => $user_id,
    ':image' =>$new_file_name,
    'language' => $language
];

if ($stmt->execute($params)) {
    $_SESSION['flash']['message']['type'] = 'success';
    $_SESSION['flash']['message']['text'] = "Книгата беше добавенa успешно!";

    header('Location: ../index.php?page=books');
    exit;
} else {
    $_SESSION['flash']['message']['type'] = 'danger';
    $_SESSION['flash']['message']['text'] = "Възникна грешка при добавянето на книгата!";

    header('Location: ../index.php?page=add_book');
    exit;
}

?>