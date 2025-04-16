<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Проверяет код подтверждения и авторизует клиента.
 */
try {
    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    // Проверка salon_id
    if (!isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён. Пожалуйста, перезапустите приложение');
    }

    $salon_id = (int)$_SESSION['salon_id'];
    $phone = normalizePhone($_POST['phone'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if (!$phone || !$code) {
        throw new Exception('Введите номер телефона и код');
    }

    // Проверка кода
    $stmt = $pdo->prepare(
        "SELECT code 
         FROM ConfirmationCodes 
         WHERE phone = :phone AND telegram_id = :telegram_id 
         ORDER BY created_at DESC LIMIT 1"
    );
    $stmt->execute(['phone' => $phone, 'telegram_id' => $_SESSION['telegram_id'] ?? '']);
    $stored_code = $stmt->fetchColumn();

    if ($stored_code !== $code) {
        throw new Exception('Неверный код');
    }

    // Поиск или создание клиента
    $stmt = $pdo->prepare("SELECT id_clients FROM Clients WHERE phone = :phone AND salon_id = :salon_id");
    $stmt->execute(['phone' => $phone, 'salon_id' => $salon_id]);
    $client_id = $stmt->fetchColumn();

    if (!$client_id) {
        $stmt = $pdo->prepare(
            "INSERT INTO Clients (full_name, phone, telegram_id, salon_id) 
             VALUES (:full_name, :phone, :telegram_id, :salon_id)"
        );
        $stmt->execute([
            'full_name' => 'Клиент',
            'phone' => $phone,
            'telegram_id' => $_SESSION['telegram_id'] ?? '',
            'salon_id' => $salon_id
        ]);
        $client_id = $pdo->lastInsertId();
    }

    // Установка сессии
    $_SESSION['user_id'] = $client_id;
    $_SESSION['role'] = 'client';

    // Очистка временных данных
    unset($_SESSION['telegram_id']);

    logAction($pdo, $client_id, 'client', "Клиент авторизовался в салоне $salon_id");

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
 * @param string $phone Номер телефона
 * @return string Нормализованный номер
 */
function normalizePhone(string $phone): string {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) === 10 ? '7' . $phone : (strlen($phone) === 11 && $phone[0] === '8' ? '7' . substr($phone, 1) : $phone);
}

/**
 * Логирует действие.
 * @param PDO $pdo Подключение к базе данных
 * @param int $user_id ID пользователя
 * @param string $role Роль
 * @param string $action Действие
 */
function logAction(PDO $pdo, int $user_id, string $role, string $action): void {
    $stmt = $pdo->prepare(
        "INSERT INTO Logs (user_id, role, action, timestamp) 
         VALUES (:user_id, :role, :action, NOW())"
    );
    $stmt->execute(['user_id' => $user_id, 'role' => $role, 'action' => $action]);
}
?>