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

// Проверка, если параметр ad_id передан в URL и форма была отправлена
if (isset($_GET['ad_id']) && isset($_POST['submit'])) {
    $ad_id = $_GET['ad_id'];
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

    // Обновление объявления в базе данных
    $query = $db->prepare('UPDATE ads SET title = :title, price = :price, description = :description, image = :image WHERE id = :ad_id AND user_id = :user_id');
    $query->bindValue(':ad_id', $ad_id);
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

// Проверка, если параметр ad_id передан в URL и текущий пользователь имеет право редактирования данного объявления
if (isset($_GET['ad_id'])) {
    $ad_id = $_GET['ad_id'];
    $user_id = $_SESSION['user_id'];

    // Получение информации об объявлении из базы данных
    $query = $db->prepare('SELECT * FROM ads WHERE id = :ad_id AND user_id = :user_id');
    $query->bindValue(':ad_id', $ad_id);
    $query->bindValue(':user_id', $user_id);
    $query->execute();
    $ad = $query->fetch(PDO::FETCH_ASSOC);

    // Проверка, если объявление не найдено или текущий пользователь не имеет права редактирования
    if (!$ad) {
        echo 'You have no rights to edit this ad.';
        exit();
    }
} else {
    echo 'No ad was found.';
    exit();
}

// Обработка удаления объявления
if (isset($_POST['delete'])) {
    // Удаление объявления из базы данных
    $query = $db->prepare('DELETE FROM ads WHERE id = :ad_id AND user_id = :user_id');
    $query->bindValue(':ad_id', $ad_id);
    $query->bindValue(':user_id', $user_id);
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
    <title>Editing an ad</title>
</head>
<body>
<h1>Editing an ad</h1>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="input-container">
    <label for="title" class="label">Title:</label>
    <input type="text" name="title" value="<?php echo $ad['title']; ?>" class="input-field" required>
    </div>

    <div class="input-container">
    <label for="description" class="label">Description:</label>
    <textarea name="description" required><?php echo $ad['description']; ?></textarea>
    </div>

    <div class="input-container">
    <label for="price" class="label">Price:</label>
    <input type="number" name="price" value="<?php echo $ad['price']; ?>" class="input-field" required>
    </div>

    <div class="input-container">
    <label for="image" class="label">Image:</label>
    <input type="file" name="image" accept="image/*" class="button" required>
    </div>

    <div class="button-container">
    <input type="submit" name="submit" value="Save" class="button">
    </div>
</form>

<form method="POST" action="">
    <div class="button-container">
    <input type="submit" name="delete" value="Delete" class="danger">
    </div>
</form>
</body>
</html>

