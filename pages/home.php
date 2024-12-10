<?php
    require_once('./db.php');

    $stmt = $pdo->query("SELECT DISTINCT genre FROM books");
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $search = $_GET['search'] ?? ''; 

    $welcomeText = '';
    $login_register_btns ='';

?>



<div class="container mt-4">

    <div class="row p-0 m-0 align-items-center rounded-3 border shadow-lg">

        <?php if (!isset($_SESSION['user_id'])){
            $welcomeText = "Добре дошли в MyLibrary!";
            $login_register_btns = '
            <a href="?page=register" class="btn btn-primary btn-lg mb-2 mb-md-0">Регистрирай се</a>
            <a href="?page=login" class="btn btn-outline-primary btn-lg mb-2 mb-md-0">Влез в профила си</a>';
        }else{
            $welcomeText = htmlspecialchars($_SESSION['username']) . ', Добре дошли обратно в MyLibrary!';
        }
        echo '
        <div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
            <h1 class="display-4 fw-bold lh-1 text-body-emphasis">' . $welcomeText.'</h1>
            <p class="lead text-muted">
                <strong>Откривайте</strong> нови книги и <strong>следете напредъка си</strong> лесно и удобно.
            </p>' .$login_register_btns . '
        </div>
        <div class="h-100 col-lg-4 offset-lg-1 p-0 overflow-hidden shadow-lg" style="height: auto">
            <img style="object-fit: cover;" class="w-100 h-100 rounded-lg-3 w-100"
            src="https://cdn.pixabay.com/photo/2023/10/27/10/43/book-stack-8345013_1280.jpg" alt="Библиотека">
        </div>';

        ?>
        
    </div>

    <div class="text-center my-4">
        <h2 class="font-weight-bold">Може да разгледате наличните книги в сайта ни!</h2>
    </div>

    <div class="text-center my-4">
        <h3>Търсене на книга</h3>
        <form method="GET" class="mb-4">
            <div class="input-group w-75 mx-auto">
                <input type="hidden" name="page" value="books">
                <input type="text" class="form-control" placeholder="Търсете заглавие на книга" name="search" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-primary" type="submit">Търсене</button>
            </div>
        </form>
    </div>

    <div class="text-center my-4">
        <h3 class="font-weight-bold">Жанрове</h3>
        <div class="row justify-content-center">
            <?php foreach ($genres as $genre): ?>
                <a href="?page=books&genre=<?= urlencode($genre['genre']) ?>" class="btn btn-outline-primary btn-lg m-2"><?= htmlspecialchars($genre['genre']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
