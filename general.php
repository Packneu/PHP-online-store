<?php
//Session start
session_start();

// Подключение к базе данных SQLite
try {
    $db = new PDO('sqlite:database.db', '', '', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ));
} catch (PDOException $e) {
    echo 'Database connection error: ' . $e->getMessage();
    exit();
}

// Проверка логина пользователя
$isLoggedIn = false;
$userId = null;
$userNickname = '';
if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    $userId = $_SESSION['user_id'];

    // Получение никнейма пользователя
    $query = $db->prepare('SELECT username FROM users WHERE id = :user_id');
    $query->bindValue(':user_id', $userId);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $userNickname = $result['username'];
    }
}

// Получение списка объявлений
$query = $db->query('SELECT * FROM ads');
$ads = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./static/styles/style.css">
    <title>Online store</title>
</head>
<body>
<div>
    <?php if ($isLoggedIn): ?>
        <p>Hi, <?php echo $userNickname; ?>!</p>
        <a href="logout.php" class="button">Logout</a>
        <a href="account.php" class="button">User Account</a>
    <?php else: ?>
        <a href="register.php" class="button">Sign Up</a>
        <a href="login.php" class="button">Log In</a>
    <?php endif; ?>

<h1>Products:</h1>

<?php if ($isLoggedIn): ?>
    <a href="create_ads.php" class="button">Create an Announcement</a>
<?php endif; ?>
</div>
<?php foreach ($ads as $ad): ?>
    <div>
        <h2><?php echo $ad['title']; ?></h2>
        <p>Description: <?php echo $ad['description']; ?></p>
        <p>Price: <?php echo $ad['price']; ?> $</p>
        <p><img src="<?php echo $ad['image']; ?>" alt="Ad Image" style="max-width: 300px; height: auto;"></p>

        <?php if ($isLoggedIn && $ad['user_id'] == $userId): ?>
            <a href="edit_ads.php?ad_id=<?php echo $ad['id']; ?>" class="button">Edit Announcement</a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</body>
</html>
