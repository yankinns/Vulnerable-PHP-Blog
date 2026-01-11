<?php
require_once 'config.php';

if (!is_admin()) {
    header('Location: login.php');
    exit();
}


$post_id = $_GET['id'] ?? 0;

// Получаем пост для редактирования
$sql = "SELECT * FROM posts WHERE id = $post_id";
$result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    die("Пост не найден!");
}



$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    

    $update_sql = "UPDATE posts SET title = '$title', content = '$content' WHERE id = $post_id";
    
    if (mysqli_query($conn, $update_sql)) {
        $success = "Пост обновлен!";
        // Обновляем данные поста
        $post['title'] = $title;
        $post['content'] = $content;
    } else {
        $error = "Ошибка: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать пост #<?php echo $post_id; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Панель управления</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Главная</a>
                <a class="nav-link" href="dashboard.php">Дашборд</a>
                <a class="nav-link" href="logout.php">Выйти</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Редактировать пост #<?php echo $post_id; ?></h1>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
    
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Заголовок</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">Содержание</label>
           
                <textarea class="form-control" id="content" name="content" rows="10" required><?php 
                    echo $post['content']; 
                ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="dashboard.php" class="btn btn-secondary">Отмена</a>
            <a href="delete_post.php?id=<?php echo $post_id; ?>" 
               class="btn btn-danger"
               onclick="return confirm('Удалить пост?')">Удалить</a>
        </form>
        
       
        <div class="mt-4 card border-warning">
            <div class="card-header bg-warning">
                <h6>Отладочная информация (УЯЗВИМОСТЬ!)</h6>
            </div>
            <div class="card-body">
                <p><strong>SQL-запрос:</strong> <code>SELECT * FROM posts WHERE id = <?php echo $post_id; ?></code></p>
                <p><strong>Автор ID:</strong> <?php echo $post['author_id']; ?></p>
                <p><strong>Текущий пользователь:</strong> <?php echo $_SESSION['username']; ?></p>
                <p><strong>Все параметры GET:</strong> <?php print_r($_GET); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
