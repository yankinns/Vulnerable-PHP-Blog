<?php
require_once 'config.php';

if (!is_admin()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['username'] == 'admin' ? 1 : 2;
    
   
 
    $sql = "INSERT INTO posts (title, content, author_id) 
            VALUES ('$title', '$content', $author_id)";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Пост успешно добавлен!";
        
       
        $_SESSION['flash'] = "Новый пост создан: " . $title;
        
     
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'dashboard.php';
        header("Location: $redirect");
        exit();
    } else {
        $error = "Ошибка: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить пост</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Панель управления</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Главная</a>
                <a class="nav-link active" href="add_post.php">Новый пост</a>
                <a class="nav-link" href="dashboard.php">Дашборд</a>
                <a class="nav-link" href="logout.php">Выйти</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Добавить новый пост</h1>
        
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
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                       required>
                <small class="text-muted">Можно использовать HTML-теги</small>
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">Содержание</label>
   
                <textarea class="form-control" id="content" name="content" rows="10" required><?php 
                    echo isset($_POST['content']) ? $_POST['content'] : ''; 
                ?></textarea>
                <small class="text-muted">Можно вставлять JavaScript-код</small>
            </div>
            
      
            <input type="hidden" name="redirect" value="<?php echo $_GET['redirect'] ?? 'dashboard.php'; ?>">
            
            <button type="submit" class="btn btn-primary">Опубликовать</button>
            <a href="dashboard.php" class="btn btn-secondary">Отмена</a>
        </form>
        

        <div class="mt-4 card">
            <div class="card-header">
                <h6>Примеры тестовых данных:</h6>
            </div>
            <div class="card-body">
                <p><strong>XSS:</strong> <code>&lt;script&gt;alert('Hacked!')&lt;/script&gt;</code></p>
                <p><strong>SQL Injection в заголовке:</strong> <code>Test', 'Content'); DROP TABLE posts; --</code></p>
                <p><strong>Перенаправление:</strong> Установите параметр <code>?redirect=http://evil.com</code></p>
            </div>
        </div>
    </div>
    

    <script>
    var userData = {
        username: '<?php echo $_SESSION["username"] ?? "guest"; ?>',
        lastAction: 'add_post'
    };
    console.log('User:', userData);
    </script>
</body>
</html>
