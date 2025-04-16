<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Возвращает salon_id на основе токена Telegram-бота.
 */
try {
    // Получение токена бота из GET-параметра или заголовка
    $bot_token = $_GET['bot_token'] ?? $_SERVER['HTTP_X_BOT_TOKEN'] ?? null;

    if (!$bot_token) {
        throw new Exception('Токен бота не указан');
    }

    // Поиск salon_id в таблице Bots
    $stmt = $pdo->prepare("SELECT salon_id FROM Bots WHERE bot_token = :bot_token");
    $stmt->execute(['bot_token' => $bot_token]);
    $salon_id = $stmt->fetchColumn();

    if ($salon_id === false) {
        throw new Exception('Бот не привязан к салону');
    }

    // Сохранение salon_id в сессии для последующих запросов
    $_SESSION['salon_id'] = (int)$salon_id;

    echo json_encode([
        'success' => true,
        'salon_id' => $salon_id
    ]);
} catch (Exception $e) {
    error_log("Ошибка в get_salon_by_bot.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>