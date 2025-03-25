<?php
include 'config.php';

header('Content-Type: application/json');

$client_id = $_POST['client_id'] ?? '';

if (empty($client_id)) {
    echo json_encode(['success' => false, 'message' => 'Client ID не указан.']);
    exit;
}

// Предполагается, что в таблице Clients есть поле telegram_id
$stmt = $pdo->prepare("SELECT telegram_id FROM Clients WHERE id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch();

$telegram_id = $client['telegram_id'] ?? null;
$code = sprintf("%06d", rand(0, 999999)); // 6-значный код

if (!$telegram_id) {
    // Для примера: если telegram_id отсутствует, выводим код в консоль
    error_log("Код для клиента $client_id: $code");
    echo json_encode(['success' => true, 'code' => $code]);
    exit;
}

$bot_token = 'YOUR_BOT_TOKEN'; // Замените на токен вашего Telegram-бота
$message = "Ваш код подтверждения: $code";
$url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$telegram_id&text=" . urlencode($message);

$response = file_get_contents($url);
if ($response) {
    echo json_encode(['success' => true, 'code' => $code]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка отправки кода.']);
}
?>