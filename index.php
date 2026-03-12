<?php
// index.php
require_once 'config.php';

$errors = [];

// --- Обработка добавления задачи ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Валидация: название не должно быть пустым
    if (empty($title)) {
        $errors[] = 'Название задачи не может быть пустым.';
    }

    if (empty($errors)) {
        // Вставка в БД (используем подготовленные выражения для безопасности)
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description) VALUES (:title, :description)");
        $stmt->execute(['title' => $title, 'description' => $description]);

        // Перенаправление, чтобы избежать повторной отправки формы при обновлении страницы
        header('Location: index.php');
        exit;
    }
}

// --- Получение всех задач из БД (новые сверху) ---
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Менеджер задач (PHP)</title>
    <style> body { font-family: Arial; margin: 40px; } .task-item { border: 1px solid #ccc; padding: 10px; margin: 10px 0; } .error { color: red; } </style>
</head>
<body>
<h1>Список задач (PHP + PostgreSQL)</h1>

<!-- Форма добавления -->
<form method="post">
    <div>
        <label>Название:</label>
        <input type="text" name="title"
               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
    </div>
    <div>
        <label>Описание:</label>
        <textarea name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
    </div>
    <button type="submit" name="add_task">Добавить задачу</button>
</form>

<!-- Вывод ошибок -->
<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Список задач -->
<div>
    <h2>Задачи:</h2>
    <?php if (empty($tasks)): ?>
        <p>Нет задач.</p>
    <?php else: ?>
        <?php foreach ($tasks as $task): ?>
            <div class="task-item">
                <strong><?php echo htmlspecialchars($task['title']); ?></strong> —
                <span><?php echo htmlspecialchars($task['description']); ?></span>
                <small><?php echo date('d.m.Y H:i', strtotime($task['created_at'])); ?></small>
                <!-- Ссылка на удаление -->
                <a href="delete.php?id=<?php echo $task['id']; ?>" onclick="return confirm('Удалить?')">Удалить</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>