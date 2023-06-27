<?php
// Инициализация сессии
session_start();

// Удаление всех данных сессии
$_SESSION = array();

// Уничтожение сессии
session_destroy();

// Перенаправление на страницу входа
header('Location: general.php');
exit();
?>
