<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Запрашивает код подтверждения и отправляет его в Telegram.
 */
try {
    if (!isset($_POST['phone']) || !isset($_POST['telegram_id']) || !isset($_POST['csrf_token'])) {
        throw new Exception('Недостаточно данных');
    }

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    if (!isset($_SESSION['bot_token']) || !isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён. Пожалуйста, перезапустите приложение');
    }

    $phone = normalizePhone($_POST['phone']);
    $telegram_id = $_POST['telegram_id'];

    if (strlen($phone) !== 11 || $phone[0] !== '7') {
        throw new Exception('Неверный формат номера телефона');
    }

    // Проверка времени последнего запроса
    $stmt = $pdo->prepare(
        "SELECT MAX(created_at) AS last_sent 
         FROM ConfirmationCodes 
         WHERE phone = :phone AND telegram_id = :telegram_id"
    );
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id]);
    $last_sent = $stmt->fetchColumn();

    if ($last_sent) {
        $interval = (new DateTime())->diff(new DateTime($last_sent));
        $seconds_left = 60 - ($interval->i * 60 + $interval->s);
        if ($interval->i < 1 && $seconds_left > 0) {
            throw new Exception('Новый код можно запросить через ' . $seconds_left . ' секунд');
        }
    }

    // Проверка клиента
    $stmt = $pdo->prepare("SELECT id_clients, telegram_id FROM Clients WHERE phone = :phone AND salon_id = :salon_id");
    $stmt->execute(['phone' => $phone, 'salon_id' => $_SESSION['salon_id']]);
    $client = $stmt->fetch();

    if ($client) {
        if (!$client['telegram_id']) {
            $stmt = $pdo->prepare("UPDATE Clients SET telegram_id = :telegram_id WHERE id_clients = :client_id");
            $stmt->execute(['telegram_id' => $telegram_id, 'client_id' => $client['id_clients']]);
            $client_id = $client['id_clients'];
        } elseif ((string)$client['telegram_id'] !== $telegram_id) {
            throw new Exception('Номер телефона не связан с этим Telegram аккаунтом');
        } else {
            $client_id = $client['id_clients'];
        }
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO Clients (phone, telegram_id, salon_id) 
             VALUES (:phone, :telegram_id, :salon_id)"
        );
        $stmt->execute([
            'phone' => $phone,
            'telegram_id' => $telegram_id,
            'salon_id' => $_SESSION['salon_id']
        ]);
        $client_id = $pdo->lastInsertId();
    }

    // Генерация кода
    $code = sprintf("%06d", rand(100000, 999999));

    // Сохранение кода
    $stmt = $pdo->prepare(
        "INSERT INTO ConfirmationCodes (phone, telegram_id, code) 
         VALUES (:phone, :telegram_id, :code)"
    );
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code]);

    // Отправка кода
    $bot_token = $_SESSION['bot_token'];
    $text = "Ваш код подтверждения: $code";
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$telegram_id&text=" . urlencode($text);
    $response = file_get_contents($url);
    $result = json_decode($response, true);

    if (!$result['ok']) {
        throw new Exception('Не удалось отправить код: ' . ($result['description'] ?? 'Неизвестная ошибка'));
    }

    logAction($pdo, $client_id, 'client', "Код отправлен для телефона $phone в салоне {$_SESSION['salon_id']}");

    echo json_encode(['success' => true, 'client_id' => $client_id]);
} catch (Exception $e) {
    error_log("Ошибка в request_code.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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