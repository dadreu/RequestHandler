<?php
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    if (!isset($_SESSION['csrf_token'])) {
        throw new Exception('CSRF-токен не найден');
    }
    echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
} catch (Exception $e) {
    error_log("Ошибка в get_csrf_token.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>