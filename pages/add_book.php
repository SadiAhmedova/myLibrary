<?php
$form_data = $_SESSION['form_data'] ?? [];

unset($_SESSION['form_data'])
?>
<h1 class="text-center">Добави книга</h1>
<form class="border rounded p-4 w-50 mx-auto" method="POST" action="./handlers/handle_add_book.php" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Заглавие</label>
        <input type="text" class="form-control" id="title" name="title"  value="<?php echo htmlspecialchars($form_data['title'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="author" class="form-label">Автор</label>
        <input type="text" class="form-control" id="author" name="author"  value="<?php echo htmlspecialchars($form_data['author'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="genre" class="form-label">Жанр</label>
        <input type="text" class="form-control" id="genre" name="genre"  value="<?php echo htmlspecialchars($form_data['genre'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Корица</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="pages" class="form-label">Брой страници</label>
        <input type="number" step="1" class="form-control" id="pages" name="pages"  value="<?php echo htmlspecialchars($form_data['pages'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="language" class="form-label">Език</label>
        <input type="text" class="form-control" id="language" name="language"  value="<?php echo htmlspecialchars($form_data['language'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Резюме</label>
        <textarea rows="10" class="form-control" id="content" name="content"> <?php echo htmlspecialchars($form_data['content'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Добави</button>
</form>