<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Проверяет код подтверждения.
 */
try {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    if (!isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён. Пожалуйста, перезапустите приложение');
    }

    $phone = normalizePhone($_POST['phone'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $telegram_id = $_POST['telegram_id'] ?? '';

    if (!$phone || !$code || !$telegram_id) {
        throw new Exception('Введите номер телефона, код и Telegram ID');
    }

    $stmt = $pdo->prepare(
        "SELECT code 
         FROM ConfirmationCodes 
         WHERE phone = :phone AND telegram_id = :telegram_id 
         ORDER BY created_at DESC LIMIT 1"
    );
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id]);
    $stored_code = $stmt->fetchColumn();

    if ($stored_code !== $code) {
        throw new Exception('Неверный код');
    }

    $stmt = $pdo->prepare("SELECT id_clients FROM Clients WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $client_id = $stmt->fetchColumn();

    if (!$client_id) {
        $stmt = $pdo->prepare(
            "INSERT INTO Clients (full_name, phone, telegram_id) 
             VALUES (:full_name, :phone, :telegram_id)"
        );
        $stmt->execute([
            'full_name' => 'Клиент',
            'phone' => $phone,
            'telegram_id' => $telegram_id
        ]);
        $client_id = $pdo->lastInsertId();
    }

    $_SESSION['user_id'] = $client_id;
    $_SESSION['role'] = 'client';

    logAction($pdo, $client_id, 'client', "Клиент авторизовался");

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Ошибка в verify_code.php: " . $e->getMessage());
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