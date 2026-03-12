<?php
// delete.php
require_once 'config.php';

// Проверяем, передан ли ID и является ли он числом
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Удаляем задачу (используем подготовленное выражение)
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// В любом случае возвращаемся на главную
header('Location: index.php');
exit;