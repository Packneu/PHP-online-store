<?php
// Начало сессии
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

// Проверка, если форма была отправлена
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // Получение идентификатора пользователя из сессии
    $user_id = $_SESSION['user_id'];

    // Путь для сохранения загруженного файла
    $targetDirectory = "./static/image/"; // Путь к директории для загрузки файлов
    $targetFile = $targetDirectory . basename($image);

// Перемещение загруженного файла на сервер
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $uploadStatus = "Файл успешно загружен.";
    } else {
        $uploadStatus = "Ошибка при загрузке файла.";
    }

    // Вставка объявления в базу данных
    $query = $db->prepare('INSERT INTO ads (user_id, title, price, description, image) VALUES (:user_id, :title, :price, :description, :image)');
    $query->bindValue(':user_id', $user_id);
    $query->bindValue(':title', $title);
    $query->bindValue(':price', $price);
    $query->bindValue(':description', $description);
    $query->bindValue(':image', $targetFile);
    $query->execute();

    // Перенаправление на главную страницу с объявлениями
    header('Location: general.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./static/styles/style.css">
    <title>Create an ad</title>
</head>
<body>
<h1>Create an ad</h1>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="input-container">
    <label for="title" class="label">Title:</label>
    <input type="text" name="title" class="input-field" required>
    </div>

    <div class="input-container">
    <label for="description" class="label">Description:</label>
    <textarea name="description" class="input-field" required></textarea>
    </div>

    <div class="input-container">
    <label for="price" class="label">Price:</label>
    <input type="number" name="price" class="input-field" required>
    </div>

    <div class="input-container">
    <label for="image" class="label">Image:</label>
    <input type="file" name="image" accept="image/*" class="button" required>
    </div>

    <div class="button-container">
    <input type="submit" name="submit" value="Create" class="button">
    </div>

</form>
</body>
</html>
