<?php
require_once('../db.php');

$response = [
    'success' => true,
    'data' => [],
    'error' => ''
];

$book_id = intval($_POST['book_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($book_id <= 0) {
    $response['success'] = false;
    $response['error'] = 'Невалидна книга.';
} else {

    $query = "SELECT COUNT(*) FROM favorite_books_users WHERE user_id = :user_id AND book_id = :book_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $user_id,
        ':book_id' => $book_id
    ]);
    $isAlreadyFavorite = $stmt->fetchColumn();

    if ($isAlreadyFavorite) {
        $response['success'] = false;
        $response['error'] = 'Книгата е вече в любими.';
    }else{
        $query = "INSERT INTO favorite_books_users (user_id, book_id) VALUES (:user_id, :book_id)";
        $stmt = $pdo->prepare($query);
        $params = [
            ':user_id' => $user_id,
            ':book_id' => $book_id
        ];

        if ($stmt->execute($params)) {
            $response['data'] = ['book_id' => $book_id];
        } else {
            $response['success'] = false;
            $response['error'] = 'Грешка при добавянето на любимата книга.';
        }
    }
    
}

echo json_encode($response);
exit;
?>
