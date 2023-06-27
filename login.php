<?php
// Session start
session_start();

// Подключение к базе данных SQLite
try {
    $db = new PDO('sqlite:database.db', '', '', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ));
}catch (PDOException $e){
    echo 'Database connection error: ' . $e->getMessage();
    exit();
}

// Проверка, если форма была отправлена
if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Поиск пользователя в базе данных
    $query = $db->prepare('SELECT id, password FROM users WHERE username = :username');
    $query->bindValue(':username', $username);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if($result && password_verify($password, $result['password'])){
        $_SESSION['user_id'] = $result['id'];

        header('Location: general.php');
        exit();
    }else{
        echo 'Incorrect username or password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./static/styles/style.css">
    <title>Login page</title>
</head>
<body>
    <h1>Login</h1>
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
            <input type="submit" name="submit" value="Log In" class="button">
            <label>Don't have an account?<a href="register.php">Sign Up</a></label>
        </div>
    </form>
</body>
</html>
