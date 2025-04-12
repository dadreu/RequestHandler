<?php
// Запускаем сессию только если она ещё не активна
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Настройка логирования ошибок
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/error.log');

try {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
} catch (Exception $e) {
    error_log('Ошибка в get_csrf_token.php: ' . $e->getMessage());
    echo json_encode(['error' => 'Ошибка сервера']);
    exit;
}
?>