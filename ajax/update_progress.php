<?php
require_once('db.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_page = intval($_POST['current_page'] ?? 0);
    if ($current_page > 0 && $current_page <= $book['pages']) {
        $query = "INSERT INTO user_progress (user_id, book_id, current_page) 
                  VALUES (:user_id, :book_id, :current_page) 
                  ON DUPLICATE KEY UPDATE current_page = VALUES(current_page)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':book_id' => $book_id,
            ':current_page' => $current_page
        ]);

        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            echo json_encode(['success' => true]);
            exit;
        }

        header("Location: " . $_SERVER['REQUEST_URI']); 
        exit; 
    } else {
        echo "Моля, въведете валидна страница.";
    }
}