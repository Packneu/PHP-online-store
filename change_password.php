<?php
//Session start
session_start();

// Проверка, если пользователь не авторизован, перенаправление на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключение к базе данных SQLite
try {
    $db = new PDO('sqlite:database.db', '', '', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ));
} catch (PDOException $e) {
    echo 'Database connection error: ' . $e->getMessage();
    exit();
}

// Получение информации о текущем пользователе
$user_id = $_SESSION['user_id'];
$query = $db->prepare('SELECT * FROM users WHERE id = :user_id');
$query->bindValue(':user_id', $user_id);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

// Обработка отправки формы
if (isset($_POST['submit'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];

    // Проверка, если текущий пароль соответствует сохраненному паролю пользователя
    if (password_verify($currentPassword, $user['password'])) {
        // Проверка, если новый пароль совпадает с подтверждением нового пароля
        if ($newPassword === $confirmNewPassword) {
            // Хеширование нового пароля
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Обновление пароля в базе данных
            $query = $db->prepare('UPDATE users SET password = :password WHERE id = :user_id');
            $query->bindValue(':password', $hashedPassword);
            $query->bindValue(':user_id', $user_id);
            $query->execute();

            echo 'Password updated successfully.';
            header('Location: account.php');
        } else {
            echo 'New password and confirmation password do not match.';
        }
    } else {
        echo 'Invalid current password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./static/styles/style.css">
    <title>Change Password</title>
</head>
<body>
<h1>Change Password</h1>
<form method="POST" action="">
    <div class="input-container">
    <label for="current_password" class="label">Current Password:</label>
    <input type="password" name="current_password" class="input-field" required><br>
    </div>

    <div class="input-container">
    <label for="new_password" class="label">New Password:</label>
    <input type="password" name="new_password" class="input-field" required><br>
    </div>

    <div class="input-container">
    <label for="confirm_new_password" class="label">Confirm New Password:</label>
    <input type="password" name="confirm_new_password" class="input-field" required><br>
    </div>

    <div class="button-container">
    <input type="submit" name="submit" value="Change Password" class="button">
    </div>
</form>
</body>
</html>
