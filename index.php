<?php
require_once 'config.php';
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];

// --- Обработка добавления задачи ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title)) {
        $errors[] = 'Название задачи не может быть пустым.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, user_id) VALUES (:title, :description, :user_id)");
        $stmt->execute(['title' => $title, 'description' => $description, 'user_id' => $user_id]);
        header('Location: index.php');
        exit;
    }
}

// --- Получение задач только текущего пользователя ---
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Менеджер задач</title>
    <style> ... </style> <!-- можно скопировать старые стили -->
</head>
<body>
<div style="float: right;">
    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
    <a href="logout.php">Выйти</a>
</div>
<h1>Мои задачи</h1>

<!-- Форма добавления -->
<form method="post">
    <div>
        <label>Название:</label>
        <input type="text" name="title" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
    </div>
    <div>
        <label>Описание:</label>
        <textarea name="description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
    </div>
    <button type="submit" name="add_task">Добавить задачу</button>
</form>

<!-- Ошибки -->
<?php if (!empty($errors)): ?>
    <div style="color:red">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Список задач -->
<div>
    <?php if (empty($tasks)): ?>
        <p>Нет задач.</p>
    <?php else: ?>
        <?php foreach ($tasks as $task): ?>
            <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
                <strong><?= htmlspecialchars($task['title']) ?></strong> — <?= htmlspecialchars($task['description']) ?>
                <small><?= date('d.m.Y H:i', strtotime($task['created_at'])) ?></small>
                <a href="delete.php?id=<?= $task['id'] ?>" onclick="return confirm('Удалить?')">Удалить</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>