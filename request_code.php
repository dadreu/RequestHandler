<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['phone']) || !isset($_POST['telegram_id']) || !isset($_POST['csrf_token'])) {
        throw new Exception('Недостаточно данных');
    }

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    $phone = normalizePhone($_POST['phone']);
    $telegram_id = $_POST['telegram_id'];

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
        if ($interval->i < 1) {
            throw new Exception('Новый код можно запросить через ' . (60 - $interval->s) . ' секунд');
        }
    }

    // Проверка клиента
    $stmt = $pdo->prepare("SELECT id_clients, telegram_id FROM Clients WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
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
        $stmt = $pdo->prepare("INSERT INTO Clients (phone, telegram_id) VALUES (:phone, :telegram_id)");
        $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id]);
        $client_id = $pdo->lastInsertId();
    }

    // Генерация и отправка кода
    $code = rand(100000, 999999);
    $stmt = $pdo->prepare(
        "INSERT INTO ConfirmationCodes (phone, telegram_id, code) 
         VALUES (:phone, :telegram_id, :code)"
    );
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code]);

    $bot_token = '8168606272:AAFuikWYy8UKjzK3iuyMjRtWHCdS1KKECbE';
    $text = "Ваш код подтверждения: $code";
    file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$telegram_id&text=" . urlencode($text));

    echo json_encode(['success' => true, 'client_id' => $client_id]);
} catch (Exception $e) {
    error_log("Ошибка в request_code.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function normalizePhone(string $phone): string {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) === 10 ? '7' . $phone : (strlen($phone) === 11 && $phone[0] === '8' ? '7' . substr($phone, 1) : $phone);
}
?>