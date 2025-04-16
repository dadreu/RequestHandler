<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Инициализирует сессию, валидирует Telegram initData и сохраняет bot_token.
 */
try {
    // Проверка входных данных
    $init_data = $_POST['init_data'] ?? null;
    if (!$init_data) {
        throw new Exception('Не переданы данные инициализации Telegram');
    }

    // Список допустимых токенов ботов
    $valid_bot_tokens = [
        '8168606272:AAFuikWYy8UKjzK3iuyMjRtWHCdS1KKECbE',
        '7922175259:AAFthA1LcUs8Oh5wh01z3eQyr3uBh2F9w8I'
    ];

    // Поиск bot_token
    $bot_token = null;
    foreach ($valid_bot_tokens as $token) {
        if (verifyTelegramInitData($init_data, $token)) {
            $bot_token = $token;
            break;
        }
    }

    if (!$bot_token) {
        throw new Exception('Недействительные данные инициализации');
    }

    // Поиск salon_id
    $stmt = $pdo->prepare("SELECT salon_id FROM Bots WHERE bot_token = :bot_token");
    $stmt->execute(['bot_token' => $bot_token]);
    $salon_id = $stmt->fetchColumn();

    if ($salon_id === false) {
        throw new Exception('Бот не привязан к салону');
    }

    // Сохранение в сессии
    $_SESSION['bot_token'] = $bot_token;
    $_SESSION['salon_id'] = (int)$salon_id;

    logAction($pdo, 0, 'system', "Инициализирована сессия для бота $bot_token, salon_id: $salon_id");

    echo json_encode([
        'success' => true,
        'salon_id' => $salon_id
    ]);
} catch (Exception $e) {
    error_log("Ошибка в init_session.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Проверяет подлинность Telegram initData.
 * @param string $initData Данные инициализации
 * @param string $botToken Токен бота
 * @return bool Подлинность данных
 */
function verifyTelegramInitData(string $initData, string $botToken): bool {
    parse_str($initData, $data);
    if (!isset($data['hash'])) {
        return false;
    }

    $received_hash = $data['hash'];
    unset($data['hash']);
    ksort($data);
    $data_check_string = implode("\n", array_map(fn($k, $v) => "$k=$v", array_keys($data), $data));
    $secret_key = hash_hmac('sha256', $botToken, 'WebAppData', true);
    $computed_hash = hash_hmac('sha256', $data_check_string, $secret_key);

    return hash_equals($computed_hash, $received_hash);
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