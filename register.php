<?php
// Подключение к базе данных SQLite
try {
    $db = new PDO('sqlite:database.db', '', '', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ));
} catch (PDOException $e) {
    echo 'Database connection error: ' . $e->getMessage();
    exit();
}

// Проверка, если форма была отправлена
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Хеширование пароля
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Проверка наличия пользователя в базе данных
    $query = $db->prepare('SELECT COUNT(*) as count FROM users WHERE username = :username');
    $query->bindValue(':username', $username);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo 'A user with this name already exists.';
    } else {
        // Добавление нового пользователя в базу данных
        $query = $db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $query->bindValue(':username', $username);
        $query->bindValue(':password', $hashedPassword);
        $query->execute();

        // Переадресация на страницу логина
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./static/styles/style.css">
    <title>Registration page</title>
</head>
<body>
<h1>Registration</h1>
<form method="POST" action="">
    <div class="input-container">
    <label for="username" class="label">Username:</label>
    <input type="text" name="username" class="input-field" required><br>
    </div>

    <div class="input-container">
    <label for="password" class="label">Password:</label>
    <input type="password" name="password" class="input-field" required><br>
    </div>

    <div class="button-container">
        <input type="submit" name="submit" value="Sign up" class="button">
        <label>Do you already have an account?<a href="login.php">Log In</a></label>
    </div>
</form>
</body>
</html>

