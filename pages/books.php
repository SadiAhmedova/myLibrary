<?php
require_once('./db.php');

$search = $_GET['search'] ?? ''; 

$query = "SELECT * FROM books WHERE title LIKE :search";
$params = ['search' => "%$search%"]; 

$user_id = $_SESSION['user_id'] ?? null;
$genre = $_GET['genre'] ?? null;
$author = $_GET['author'] ?? null;

if ($genre) {
    $query .= " AND genre = :genre";
    $params['genre'] = $genre;
}
if ($author) {
    $query .= " AND author = :author";
    $params['author'] = $author;
}

$query .= " ORDER BY author";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$favoriteBooks = [];
if ($user_id) {
    $favQuery = "SELECT book_id FROM favorite_books_users WHERE user_id = :user_id";
    $favStmt = $pdo->prepare($favQuery);
    $favStmt->execute(['user_id' => $user_id]);
    $favoriteBooks = $favStmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

$paramsAuthors = [];
$authorsQuery = "SELECT DISTINCT author FROM books WHERE 1=1";
if ($genre) {
    $authorsQuery .= " AND genre = :genre";
    $paramsAuthors['genre'] = $genre;
}
$authorsQuery .= " ORDER BY author";
$authorsStmt = $pdo->prepare($authorsQuery);
$authorsStmt->execute($paramsAuthors);
$authors = $authorsStmt->fetchAll(PDO::FETCH_COLUMN);

$genresQuery = "SELECT DISTINCT genre FROM books ORDER BY genre";
$genresStmt = $pdo->prepare($genresQuery);
$genresStmt->execute();
$genres = $genresStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php if (!empty($search)): ?>
    <div class="alert alert-info">
        Резултати за: <strong><?= htmlspecialchars($search) ?></strong>
    </div>
<?php endif; ?>


<div class="row">
<form action="" method="GET" class="form-inline justify-content-center">
        <input type="hidden" name="page" value="books">
        <div class="input-group">
            <input type="text" name="search" class="form-control w-50" placeholder="Търсете по заглавие" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary ml-2">Търсене</button>
        </div>
    </form>
</div>


<div class="row">
    <form class="mb-4" method="GET">
        <div class="input-group mb-3">
            <input type="hidden" name="page" value="books">
            <select name="genre" class="form-select" id="genre-filter">
                <option value="">Избери жанр</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?= htmlspecialchars($genre) ?>" <?= isset($_GET['genre']) && $_GET['genre'] === $genre ? 'selected' : '' ?>>
                    <?= htmlspecialchars($genre) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <select name="author" class="form-select" id="author-filter">
                <option value="">Избери автор</option>
                <?php foreach ($authors as $author): ?>
                    <option value="<?= htmlspecialchars($author) ?>" <?= isset($_GET['author']) && $_GET['author'] === $author ? 'selected' : '' ?>>
                        <?= htmlspecialchars($author) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="?page=books" class="btn btn-secondary">Изчисти филтрите</a>

        </div>
    </form>
</div>


<div id="book-list" class="d-flex flex-wrap justify-content-between">
    <?php
    if (empty($books)) {
        echo '<h1>Няма намерени книги</h1>';
    } else {
        foreach ($books as $book) {
            $edit_delete_btns = '';
            $fav_btn = '';

            if (isset($_SESSION['username'])) {
                if (in_array($book['id'], $favoriteBooks)) {
                    $fav_btn = '<div class="card-footer text-center">
                                   <button type="button" class="btn btn-sm btn-danger remove-book favorite-button" data-book="' . $book['id'] . '">Премахни от любими</button>
                               </div>';
                } else {
                    $fav_btn = '<div class="card-footer text-center">
                                   <button type="button" class="btn btn-sm btn-primary add-book favorite-button" data-book="' . $book['id'] . '">Добави в любими</button>
                               </div>';
                }
            }

            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $book['user_id']) {
                $edit_delete_btns = '
                   <div class="card-header d-flex flex-row justify-content-between">
                        <a class="btn btn-sm btn-warning" href="?page=edit_book&id=' . htmlspecialchars($book['id']) . '">Редактирай</a>
                        <form method="POST" action="./handlers/handle_delete_book.php" onsubmit="return confirm(\'Сигурни ли сте, че искате да изтриете книгата си?\');">
                            <input type="hidden" name="id" value="' . htmlspecialchars($book['id']) . '">
                            <button type="submit" class="btn btn-sm btn-danger">Изтрий</button>
                        </form>
                    </div>';
            }

            $image_path = file_exists('uploads/' . $book['image']) ? 'uploads/' . $book['image'] : 'uploads/default-placeholder.png';

            echo '
            <div class="card mb-4" style="width: 18rem;">
                ' . $edit_delete_btns . '
                <img src="' . htmlspecialchars($image_path) . '" class="card-img-top" alt="Book Image">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($book['title']) . '</h5>
                    <p class="card-text">' . htmlspecialchars($book['author']) . '</p>
                    <p class="card-text">' . htmlspecialchars($book['genre']) . '</p>
                </div>
                ' . $fav_btn .'
                <div class="card-footer text-center">
                    <a href="?page=read_book&id=' . $book['id'] . '" class="btn btn-sm btn-success">Чети</a>
                </div>
            </div>';
        }
    }
    ?>
</div>