<?php
$id = intval($_SESSION['user_id'] ?? 0);
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $id]);
$profile = $stmt->fetch();


?>

<form class="border rounded p-4 w-50 mx-auto" method="POST" action="./handlers/handle_edit_profile.php" enctype="multipart/form-data">
    <h3 class="text-center">Редактиране на профил</h3>
    <div class="mb-3">
        <label for="username" class="form-label">Потребителско име</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($profile['username'] ?? ''); ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Имейл</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>">
    </div>
    <div class="mb-3">
        <label for="old_password" class="form-label">Стара пар��ла</label>
        <input type="password" class="form-control" id="old_password" name="old_password">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Нова парола</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <div class="mb-3">
        <label for="repeat_password" class="form-label">Повтори новата парола</label>
        <input type="password" class="form-control" id="repeat_password" name="repeat_password">
    </div>
    <input type="hidden" name="id" value="<?php echo $profile['id'] ?>">

    <button type="submit" class="btn btn-primary mx-auto">Редактирай</button>
</form>