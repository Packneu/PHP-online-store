<?php
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

// Обработка формы редактирования аккаунта
if (isset($_POST['submit'])) {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];

    // Обновление информации о пользователе в базе данных
    $query = $db->prepare('UPDATE users SET username = :username, email = :email WHERE id = :user_id');
    $query->bindValue(':username', $newUsername);
    $query->bindValue(':email', $newEmail);
    $query->bindValue(':user_id', $user_id);
    $query->execute();

    // Перенаправление на страницу аккаунта пользователя
    header('Location: account.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./static/styles/style.css">
    <title>User Account</title>
</head>
<body>
<div>
<h1>User Account</h1>
<p>Username: <?php echo $user['username']; ?></p>
<p>Email: <?php echo $user['email']; ?></p>

<h2>Edit Account</h2>
<form method="POST" action="">
    <div class="input-container">
    <label for="username" class="label">Username:</label>
    <input type="text" name="username" value="<?php echo $user['username']; ?>" class="input-field" required><br>
    </div>

    <div class="input-container">
    <label for="email" class="label">Email:</label>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" class="input-field"><br>
    </div>

    <div class="button-container">
    <p><input type="submit" name="submit" value="Save" class="button"></p><hr>
    </div>
</form>

<a href="change_password.php" class="button">Change Password</a>
<a href="general.php"class="button">Back to Store</a>
</div>
</body>
</html>
