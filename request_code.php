<?php
include 'config.php';
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['telegram_id'])) {
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']); // Удаляем все нечисловые символы
    if (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1); // Заменяем 8 на 7 для российских номеров
    }
    $telegram_id = $_POST['telegram_id'];

    // Проверка в таблице Clients
    $stmt = $pdo->prepare("SELECT id FROM Clients WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        $client_id = $client['id'];
    } else {
        // Регистрация нового клиента
        $stmt = $pdo->prepare("INSERT INTO Clients (phone) VALUES (:phone)");
        $stmt->execute(['phone' => $phone]);
        $client_id = $pdo->lastInsertId();
    }

    // Генерация и сохранение кода
    $code = rand(100000, 999999);
    $stmt = $pdo->prepare("INSERT INTO ConfirmationCodes (phone, telegram_id, code) VALUES (:phone, :telegram_id, :code)");
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code]);

    // Отправка кода через Telegram
    $botToken = '8168606272:AAFuikWYy8UKjzK3iuyMjRtWHCdS1KKECbE'; // Замените на ваш токен
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