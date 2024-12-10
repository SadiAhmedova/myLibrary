<?php

    $id = intval($_GET['id'] ?? 0);
    $query = "SELECT * FROM books WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $book = $stmt->fetch();

?>
<form class="border rounded p-4 w-50 mx-auto" method="POST" action="./handlers/handle_edit_book.php" enctype="multipart/form-data">
    <h3 class="text-center">Редактирай книга</h3>
    <div class="mb-3">
        <label for="title" class="form-label">Заглавие</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo  htmlspecialchars($book['title']) ?? '' ?>">
    </div>
    <div class="mb-3">
        <label for="author" class="form-label">Автор</label>
        <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($book['author'])?? '' ?>">
    </div>
    <div class="mb-3">
        <label for="genre" class="form-label">Жанр</label>
        <input type="text" class="form-control" id="genre" name="genre" value="<?php echo htmlspecialchars($book['genre'])  ?? '' ?>">
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Корица</label>
        <?php if (!empty($book['image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($book['image']); ?>" alt="Book Image" style="width: 100px; height: auto;">
        <?php endif; ?>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="pages" class="form-label">Брой страници</label>
        <input type="number" step="1" class="form-control" id="pages" name="pages"  value="<?php echo htmlspecialchars($book['pages'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="language" class="form-label">Език</label>
        <input type="text" class="form-control" id="language" name="language"  value="<?php echo htmlspecialchars($book['language'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Резюме</label>
        <textarea rows="10" class="form-control" id="content" name="content"> <?php echo htmlspecialchars($book['content']) ?? '' ?></textarea>
    </div>


    <input type="hidden" name="id" value="<?php echo $book['id'] ?>">
    <button type="submit" class="btn btn-success mx-auto">Редактирай</button>
</form>