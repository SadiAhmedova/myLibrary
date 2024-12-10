<?php
require_once('db.php');

$book_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$book_id) {
    echo "Невалидно ID на книга.";
    exit;
}

$query = "SELECT * FROM books WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    echo "Книгата не е намерена.";
    exit;
}

$current_page = 0;
if ($user_id) {
    $query = "SELECT current_page FROM user_progress WHERE user_id = :user_id AND book_id = :book_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);
    $progress = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($progress) {
        $current_page = $progress['current_page'];
    }
}

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

        header("Location: " . $_SERVER['REQUEST_URI']); 
        exit; 
    } else {
        echo "Моля, въведете валидна страница.";
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <?php
                $image_path = file_exists('uploads/' . $book['image']) ? 'uploads/' . $book['image'] : 'uploads/default-placeholder.png';
            ?>
            <img src="<?= htmlspecialchars($image_path) ?>" class="img-fluid" alt="Book Cover">

            <?php if ($user_id): ?>
                <hr>
                <h5>Вашият напредък:</h5>
                <form id="progressForm" method="POST" >
                    <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id) ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                    <label for="current-page">Въведи текущата страница:</label>
                    <input type="number" name="current_page" id="current-page" 
                           value="<?= htmlspecialchars($current_page) ?>" 
                           min="1" max="<?= htmlspecialchars($book['pages']) ?>" 
                           class="form-control w-25 mb-2">
                    <button type="submit" class="btn btn-primary">Запази прогреса</button>
                </form>
                <p>Прочетени страници: <?= htmlspecialchars($current_page) ?> / <?= htmlspecialchars($book['pages']) ?></p>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" 
                         style="width: <?= ($current_page / $book['pages']) * 100 ?>%;" 
                         aria-valuenow="<?= $current_page ?>" aria-valuemin="0" 
                         aria-valuemax="<?= $book['pages'] ?>">
                        <?= round(($current_page / $book['pages']) * 100, 2) ?>%
                    </div>
                </div>
            <?php else: ?>
                <p>Моля, влезте в профила си, за да следите напредъка си.</p>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <h1><?= htmlspecialchars($book['title']) ?></h1>
            <h3>Автор: <?= htmlspecialchars($book['author']) ?></h3>
            <p><strong>Жанр: </strong><?= htmlspecialchars($book['genre']) ?></p>
            <p><strong>Брой страници: </strong><?= htmlspecialchars($book['pages']) ?></p>
            <p><strong>Резюме:</strong></p>
            <p><?= htmlspecialchars($book['content']) ?></p>

            
        </div>
    </div>
</div>
