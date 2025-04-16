<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Авторизация мастера.
 */
try {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    $phone = normalizePhone($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$phone || !$password) {
        throw new Exception('Введите номер телефона и пароль');
    }

    if (strlen($phone) !== 11 || $phone[0] !== '7') {
        throw new Exception('Неверный формат номера телефона');
    }

    // Поиск мастера
    $stmt = $pdo->prepare(
        "SELECT id_masters, password, salon_id 
         FROM Masters 
         WHERE phone = :phone"
    );
    $stmt->execute(['phone' => $phone]);
    $master = $stmt->fetch();

    if (!$master || !password_verify($password, $master['password'])) {
        throw new Exception('Неверный номер телефона или пароль');
    }

    // Установка сессии
    $_SESSION['user_id'] = $master['id_masters'];
    $_SESSION['role'] = 'master';
    $_SESSION['salon_id'] = (int)$master['salon_id'];

    logAction($pdo, $master['id_masters'], 'master', "Мастер авторизовался в салоне {$master['salon_id']}");

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Ошибка в master_login.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Нормализует номер телефона.
 */
function normalizePhone(string $phone): string {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) === 10 ? '7' . $phone : (strlen($phone) === 11 && $phone[0] === '8' ? '7' . substr($phone, 1) : $phone);
}

/**
 * Логирует действие.
 */
function logAction(PDO $pdo, int $user_id, string $role, string $action): void {
    $stmt = $pdo->prepare(
        "INSERT INTO Logs (user_id, role, action, timestamp) 
         VALUES (:user_id, :role, :action, NOW())"
    );
    $stmt->execute(['user_id' => $user_id, 'role' => $role, 'action' => $action]);
}
?>