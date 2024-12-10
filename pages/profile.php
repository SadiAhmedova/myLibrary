<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

require_once('db.php');
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("SELECT * FROM books WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$uploadedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$favQuery = "
    SELECT books.*
    FROM favorite_books_users
    JOIN books ON favorite_books_users.book_id = books.id
    WHERE favorite_books_users.user_id = :user_id
";
$favStmt = $pdo->prepare($favQuery);
$favStmt->execute(['user_id' => $user_id]);
$favoriteBooks = $favStmt->fetchAll(PDO::FETCH_ASSOC);

$inProgressQuery = "
    SELECT books.*, user_progress.current_page
    FROM user_progress
    JOIN books ON user_progress.book_id = books.id
    WHERE user_progress.user_id = :user_id
";
$inProgressStmt = $pdo->prepare($inProgressQuery);
$inProgressStmt->execute(['user_id' => $user_id]);
$inProgressBooks = $inProgressStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">

    <div class="border rounded p-4 w-50 mx-auto mt-4">
        <h3 class="text-center">Профил</h3>
        <p><strong>Потребителско име:<br/></strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Имейл:<br/></strong> <?php echo htmlspecialchars($user['email']); ?></p>
        
        <div class="d-flex justify-content-between">
            <a href="?page=edit_profile&id='<?php $_SESSION['user_id']; ?>" class="btn btn-warning">Редактирай профила</a>
            
            <form action="./handlers/handle_delete_profile.php" method="POST" onsubmit="return confirm('Сигурни ли сте, че искате да изтриете акаунта си?');">
                <button type="submit" class="btn btn-danger">Изтрий акаунта</button>
            </form>

        </div>
    </div>


    <h4>Любими книги</h4>
    <div class="row">
        <?php if ($favoriteBooks){
                foreach ($favoriteBooks as $book){
                    echo '
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="index.php?page=read_book&id=' . htmlspecialchars($book['id']) .'" class="text-decoration-none text-dark">'.
                                     htmlspecialchars($book['title']).
                                '</a>
                                </h5>
                                <p class="card-text text-muted">от ' .  htmlspecialchars($book['author']) . '</p>
                             </div>
                        </div>
                    </div>';
                }}
                else{
                    echo '<p>Нямате любими книги.</p>';
            }
        ?>
    </div>

    <h4>Книги в прогрес</h4>
    <div class="row">
        <?php if ($inProgressBooks){
         foreach ($inProgressBooks as $book){
            echo '<div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="index.php?page=read_book&id=' . htmlspecialchars($book['id']) . '" class="text-decoration-none text-dark">' . 
                                htmlspecialchars($book['title']) . 
                                '</a>
                                </h5>
                                <p class="card-text text-muted">от ' . htmlspecialchars($book['author']) . '</p>
                        <p>Текуща страница: ' . htmlspecialchars($book['current_page']) . '</p>
                    </div>
                </div>
                </div>';
         } 
        }else{
            echo '<p>Нямате книги в прогрес.</p>';
         }
         ?>
    </div>


    <h3>Моите книги</h3>
    <div class="row">
        <?php
        if($uploadedBooks){

            foreach ($uploadedBooks as $book){
                echo '<div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                            <a href="index.php?page=read_book&id=' . htmlspecialchars($book['id']) . '" class="text-decoration-none text-dark">'. 
                                htmlspecialchars($book['title']) . '</a>
                            </h5>
                            <p class="card-text text-muted">от ' . htmlspecialchars($book['author']) . 
                            '<div class="mt-auto">
                                <div class="card-footer d-flex justify-content-between">
                                    <a class="btn btn-sm btn-warning" href="?page=edit_book&id=' . $book['id'] . '">Редактирай</a>
                                    <form method="POST" action="./handlers/handle_delete_book.php" onsubmit="return confirm(\'Сигурни ли сте, че искате да изтриете профила си, заедно с това ще се изтрият и вашите книги?\');">
                                        <input type="hidden" name="id" value="' . $book['id'] . '">
                                        <button type="submit" class="btn btn-sm btn-danger">Изтрий</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';}}
                else{
                    echo '<p>Нямате качени книги.</p>';
                }
            ?>
    </div>
</div>
