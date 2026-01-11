<?php
require_once 'config.php';

if (!is_admin()) {
    header('Location: login.php');
    exit();
}



$post_id = $_GET['id'] ?? 0;
$confirm = $_GET['confirm'] ?? false;

if ($post_id > 0) {
    if ($confirm === 'yes') {
       
        $sql = "DELETE FROM posts WHERE id = $post_id";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['flash'] = "Пост #$post_id удален! <script>console.log('Post deleted')</script>";
            

            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = "Ошибка удаления: " . mysqli_error($conn);
        }
    }
} else {
    $error = "Не указан ID поста";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удаление поста</title>
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
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4>Удаление поста</h4>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <a href="dashboard.php" class="btn btn-primary">Вернуться</a>
                <?php elseif(!$confirm): ?>
                    <div class="alert alert-warning">
                        <h5>Вы уверены, что хотите удалить пост #<?php echo $post_id; ?>?</h5>
                        <p>Это действие нельзя отменить.</p>
                        
                        <div class="mt-3">
                            <a href="delete_post.php?id=<?php echo $post_id; ?>&confirm=yes" 
                               class="btn btn-danger">Да, удалить</a>
                            <a href="dashboard.php" class="btn btn-secondary">Отмена</a>
                        </div>
                        
                     
                        <div class="mt-4 p-3 bg-light rounded">
                            <small>Будет выполнен запрос:</small><br>
                            <code>DELETE FROM posts WHERE id = <?php echo $post_id; ?></code>
                        </div>
                    </div>
                    
                 
                    <script>
                    
                    setTimeout(function() {
                        if (confirm('Автоматическое удаление через 10 сек. Продолжить?')) {
                            window.location.href = 'delete_post.php?id=<?php echo $post_id; ?>&confirm=yes';
                        }
                    }, 10000);
                    </script>
                    
                <?php else: ?>
                    <div class="alert alert-success">
                        <h5>Пост #<?php echo $post_id; ?> успешно удален!</h5>
                        <p>Перенаправление на дашборд через 3 секунды...</p>
                    </div>
                    <script>
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 3000);
                    </script>
                <?php endif; ?>
            </div>
        </div>
        
      
        <div class="mt-4 card border-info">
            <div class="card-header bg-info text-white">
                <h6>Примеры эксплуатации (для обучения)</h6>
            </div>
            <div class="card-body">
                <p><strong>SQL Injection (удаление всех постов):</strong></p>
                <code>delete_post.php?id=1 OR 1=1</code>
                
                <p class="mt-3"><strong>CSRF-атака (вставьте на другой сайт):</strong></p>
                <code>&lt;img src="http://localhost/blog/delete_post.php?id=1&confirm=yes" width="0" height="0"&gt;</code>
                
                <p class="mt-3"><strong>IDOR (удаление чужого поста):</strong></p>
                <code>Просто измените ID в параметре</code>
            </div>
        </div>
    </div>
</body>
</html>
