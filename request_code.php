<?php
include 'config.php';
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['telegram_id'])) {
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    if (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1);
    }
    $telegram_id = $_POST['telegram_id'];

    // Проверка времени последней отправки
    $stmt = $pdo->prepare("SELECT MAX(created_at) as last_sent FROM ConfirmationCodes WHERE phone = :phone AND telegram_id = :telegram_id");
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id]);
    $last_sent = $stmt->fetch(PDO::FETCH_ASSOC)['last_sent'];

    if ($last_sent) {
        $last_sent_time = new DateTime($last_sent);
        $now = new DateTime();
        $interval = $now->diff($last_sent_time);
        if ($interval->i < 1) { // Меньше 1 минуты
            $response['message'] = 'Новый код можно запросить через ' . (60 - $interval->s) . ' секунд';
            echo json_encode($response);
            exit;
        }
    }

    // Проверка в таблице Clients
    $stmt = $pdo->prepare("SELECT id, telegram_id FROM Clients WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        if ($client['telegram_id'] === null) {
            $stmt_update = $pdo->prepare("UPDATE Clients SET telegram_id = :telegram_id WHERE id = :client_id");
            $stmt_update->execute(['telegram_id' => $telegram_id, 'client_id' => $client['id']]);
            $client_id = $client['id'];
        } elseif ($client['telegram_id'] === $telegram_id) {
            $client_id = $client['id'];
        } else {
            $response['message'] = 'Номер телефона не связан с этим Telegram аккаунтом';
            echo json_encode($response);
            exit;
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO Clients (phone, telegram_id) VALUES (:phone, :telegram_id)");
        $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id]);
        $client_id = $pdo->lastInsertId();
    }

    // Генерация и сохранение кода
    $code = rand(100000, 999999);
    $stmt = $pdo->prepare("INSERT INTO ConfirmationCodes (phone, telegram_id, code) VALUES (:phone, :telegram_id, :code)");
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code]);

    // Отправка кода через Telegram
    $botToken = '8168606272:AAFuikWYy8UKjzK3iuyMjRtWHCdS1KKECbE';
    $text = "Ваш код подтверждения: $code";
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$telegram_id&text=" . urlencode($text);
    file_get_contents($url);

    $response['success'] = true;
    $response['client_id'] = $client_id;
} else {
    $response['message'] = 'Недостаточно данных';
}

echo json_encode($response);
?>