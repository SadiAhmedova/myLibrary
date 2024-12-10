<?php

require_once('db.php');


$page = $_GET['page'] ?? 'home';
$search = $_GET['search'] ?? '';
if (mb_strlen($search) > 0) {
    setcookie('last_search', $search, time() + 3600, '/', 'localhost', false, false);
}

$flash = [];
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="./images/open-book.png">

</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-dark py-3">
    <div class="container-fluid">
        <ul class="navbar-nav mx-auto">
            <li class="nav-item">
                <a class="nav-link <?php echo($page == 'home' ? 'active' : '') ?>" aria-current="page" href="?page=home">Начало</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo($page == 'books' ? 'active' : '') ?>" href="?page=books">Книги</a>
            </li>
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo($page == 'add_book' ? 'active' : '') ?>" href="?page=add_book">Добави книга</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo($page == 'profile' ? 'active' : '') ?>" href="?page=profile">Профил</a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="d-flex flex-row gap-4">

            <?php

                if (isset($_SESSION['username'])) {
                    echo '<form method="POST" action="./handlers/handle_logout.php">
                        <button type="submit" class="btn btn-outline-light">Изход</button>
                    </form>';
                } else {
                    echo '<a href="?page=login" class="btn btn-outline-light">Вход</a>';
                    echo '<a href="?page=register" class="btn btn-outline-light">Регистрация</a>';
                }
            ?>
        </div>
    </div>
  </nav>
    <main class="container py-4" style="min-height:85vh;">
    <?php
            if (isset($flash['message'])) {
                echo '
                    <div class="alert alert-' . $flash['message']['type'] . '" role="alert">
                        ' . $flash['message']['text'] . '
                    </div>
                ';
            }

            if (file_exists('./pages/' . $page . '.php')) {
                require_once('./pages/' . $page . '.php');
            } else {
                require_once('./pages/not_found.php');
            }
        ?>
    </main>
    <footer class="bg-dark text-center py-5 mt-auto">
        <div class="container">
            <span class="text-light">© 2024 My Library | All rights reserved</span>
        </div>
    </footer>

    <script>
    $(document).ready(function() {
        $('.favorite-button').on('click', function() {
            var bookId = $(this).data('book');
            var button = $(this);
            var action = button.hasClass('btn-primary') ? 'add' : 'remove';
            
            $.ajax({
                url: action === 'add' ? './ajax/add_favourite.php' : './ajax/remove_favourite.php',
                method: 'POST',
                data: {
                    book_id: bookId
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    if (data.success) {
                        if (action === 'add') {
                        button
                            .removeClass('btn-primary')
                            .addClass('btn-danger')
                            .text('Премахни от любими');
                    } else {
                        button
                            .addClass('btn-primary')
                            .removeClass('btn-danger')
                            .text('Добави в любими');
                    }
                    } else {
                        alert(data.error);
                    }
                },
                error: function() {
                    alert('Грешка при добавянето на книга в любими.');
                }
            });
        });

        $('#author-filter').on('change', function () {
            var author = $(this).val();
        
            $.ajax({
                url: 'index.php?page=books',
                method: 'GET',
                data: { author: author, page: 'books' },
                success: function (response) {
                    $('#book-list').html($(response).find('#book-list').html());
                },
                error: function (error) {
                    alert('Грешка при филтрирането на книги.');
                }
            });
        });



        $('.genre-filter').on('click', function () {
            var genre = $(this).data('genre');
            var author = $('select[name="author"]').val();
        
            $('.genre-filter').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');

            $.ajax({
                url: 'index.php?page=books',
                method: 'GET',
                data: {
                    genre: genre,
                    author: author,
                    page: 'books'
                },
                success: function (response) {
                    $('#book-list').html($(response).find('#book-list').html());
                },
                error: function () {
                    console.error('Грешка при AJAX: ', error);
                    alert('Грешка при филтрирането на книги.');
                }
            });
        });



        $('#genre-filter').on('change', function () {
            var genre = $(this).val();
        
            $('select[name="author"]').html('<option value="">Избери автор</option>');

            $.ajax({
                url: 'index.php?page=books',
                method: 'GET',
                data: { genre: genre, page: 'books' },
                success: function (response) {
                    $('#book-list').html($(response).find('#book-list').html());
                
                    var authorsOptions = $(response).find('select[name="author"]').html();
                    $('select[name="author"]').html(authorsOptions);
                },
                error: function (error) {
                    console.error('Грешка при AJAX: ', error);
                    alert('Грешка при филтрирането на книги.');
                }
            });
        });


        $('#save-progress').on('click', function() {
            const formData = new FormData(document.getElementById('progressForm'));

            fetch('./ajax/update_progress.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Прогресът е запазен успешно!');
                } else {
                    console.error('Грешка: ', data.message);
                    alert(data.message);
                }
            })
            .catch(error => console.error('Грешка при заявката: ', error));
        });

    });
</script>

</body>
</html>