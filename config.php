<?php
// config.php
$host = '46.191.235.28';
$port = '5432';
$dbname = 'todo_db'; // Имя базы данных
$user = 'postgres';  // Ваш пользователь PostgreSQL
$password = 'Asdf=1234Asdf=1234'; // Ваш пароль

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Режим ошибок
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Возвращать как массивы
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>