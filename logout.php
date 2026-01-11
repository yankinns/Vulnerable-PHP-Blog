<?php
require_once 'config.php';



// Очищаем все данные сессии
$_SESSION = array();

// Уничтожаем сессию
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Редирект с возможностью открытого редиректа
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
header("Location: $redirect");
exit();
?>
