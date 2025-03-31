<?php
include 'config.php';
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['telegram_id'])) {
    $phone = $_POST['phone'];
    $telegram_id = $_POST['telegram_id'];

    // Проверка в таблице Clients
    $stmt = $pdo->prepare("SELECT id, telegram_id FROM Clients WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        // Проверяем, что Telegram ID совпадает
        if ($client['telegram_id'] === $telegram_id) {
            $client_id = $client['id'];
        } else {
            $response['message'] = 'Номер телефона не связан с этим Telegram аккаунтом';
            echo json_encode($response);
            exit;
        }
    } else {
        // Регистрация нового клиента с указанным Telegram ID
        $stmt = $pdo->prepare("INSERT INTO Clients (phone, telegram_id) VALUES (:phone, :telegram_id)");
        $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id]);
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