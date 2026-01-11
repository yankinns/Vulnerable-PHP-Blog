<?php
require_once 'config.php';

if (!is_admin()) {
   
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'login.php';
    header("Location: $redirect");
    exit();
}

// Получаем статистику
$stats = [];
$stats['posts'] = mysqli_query($conn, "SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$stats['comments'] = mysqli_query($conn, "SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];
$stats['users'] = mysqli_query($conn, "SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];


$logs = [];
if (file_exists('access.log')) {
   
    $log_file = isset($_GET['log']) ? $_GET['log'] : 'access.log';
    $logs = file($log_file, FILE_IGNORE_NEW_LINES);
    $logs = array_slice($logs, -10); // последние 10 записей
}


$user_filter = isset($_GET['user_id']) ? $_GET['user_id'] : '';
if ($user_filter) {
    $posts_sql = "SELECT * FROM posts WHERE author_id = $user_filter ORDER BY id DESC";
} else {
    $posts_sql = "SELECT * FROM posts ORDER BY id DESC LIMIT 5";
}
$recent_posts = mysqli_query($conn, $posts_sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель управления</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        .stat-card { transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Панель управления</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Главная</a>
                <a class="nav-link" href="add_post.php">Новый пост</a>
                <a class="nav-link" href="upload.php">Загрузка файлов</a>
                <a class="nav-link" href="logout.php">Выйти (<?php echo $_SESSION['username']; ?>)</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Панель управления</h1>
        
        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Посты</h5>
                        <h2><?php echo $stats['posts']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Комментарии</h5>
                        <h2><?php echo $stats['comments']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Пользователи</h5>
                        <h2><?php echo $stats['users']; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Последние посты -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Последние посты</h5>
                        <form method="GET" class="d-inline">
                            <select name="user_id" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                <option value="">Все авторы</option>
                                <option value="1" <?php echo $user_filter == '1' ? 'selected' : ''; ?>>admin</option>
                                <option value="2" <?php echo $user_filter == '2' ? 'selected' : ''; ?>>editor</option>
                            </select>
                        </form>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Заголовок</th>
                                    <th>Автор</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($post = mysqli_fetch_assoc($recent_posts)): ?>
                                <tr>
                                    <td><?php echo $post['id']; ?></td>
                                    <td>
                                        <a href="view_post.php?id=<?php echo $post['id']; ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $post['author_id']; ?></td>
                                    <td>
                                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">Изменить</a>
                                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Удалить пост?')">Удалить</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Логи и инструменты -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Последние логи</h5>
                        <form method="GET" class="d-inline">
                            <select name="log" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                <option value="access.log" <?php echo ($log_file ?? '') == 'access.log' ? 'selected' : ''; ?>>access.log</option>
                                <option value="error.log" <?php echo ($log_file ?? '') == 'error.log' ? 'selected' : ''; ?>>error.log</option>
                                <option value="config.php" <?php echo ($log_file ?? '') == 'config.php' ? 'selected' : ''; ?>>config.php</option>
                            </select>
                        </form>
                    </div>
                    <div class="card-body">
                        <div style="max-height: 200px; overflow-y: auto;">
                            <?php if (!empty($logs)): ?>
                                <?php foreach($logs as $log): ?>
                                    <div class="border-bottom pb-1 mb-1">
                                        <small><?php echo htmlspecialchars($log); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">Логи отсутствуют</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Быстрые действия</h5>
                    </div>
                    <div class="card-body">
                        <a href="add_post.php" class="btn btn-primary mb-2">Добавить пост</a>
                        <a href="upload.php" class="btn btn-secondary mb-2">Загрузить файл</a>
                        
                        
                        <form method="POST" class="mt-3">
                            <h6>Системная команда (только ping):</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" name="cmd" placeholder="Введите host для ping" value="localhost">
                                <button class="btn btn-outline-dark" type="submit">Выполнить</button>
                            </div>
                        </form>
                        
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
                          
                            $cmd = $_POST['cmd'];
                            echo "<div class='mt-3 p-2 bg-dark text-white rounded'>";
                            echo "<small>Результат:</small><br>";
                       
                            if (strpos($cmd, ';') === false && strpos($cmd, '&') === false) {
                                system("ping -n 2 " . $cmd);
                            } else {
                                echo "Недопустимые символы!";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
