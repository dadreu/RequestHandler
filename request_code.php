<?php
include 'config.php'; // Подключение к базе данных
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['telegram_id'])) {
    $phone = $_POST['phone'];
    $telegram_id = $_POST['telegram_id'];

    // Проверка в таблице Masters
    $stmt = $pdo->prepare("SELECT id FROM Masters WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $master = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($master) {
        $master_id = $master['id'];
        $stmt = $pdo->prepare("SELECT id FROM Users WHERE role = 'master' AND master_id = :master_id");
        $stmt->execute(['master_id' => $master_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // Генерация и сохранение кода
            $code = rand(100000, 999999);
            $stmt = $pdo->prepare("INSERT INTO ConfirmationCodes (phone, telegram_id, code) VALUES (:phone, :telegram_id, :code)");
            $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code]);

            // Отправка кода через Telegram
            $botToken = '8168606272:AAFuikWYy8UKjzK3iuyMjRtWHCdS1KKECbE'; // Замените на ваш токен бота
            $text = "Ваш код подтверждения: $code";
            $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$telegram_id&text=" . urlencode($text);
            file_get_contents($url);

            $response['success'] = true;
        } else {
            $response['message'] = 'Пользователь не найден';
        }
    } else {
        // Проверка в таблице Clients
        $stmt = $pdo->prepare("SELECT id FROM Clients WHERE phone = :phone");
        $stmt->execute(['phone' => $phone]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($client) {
            $client_id = $client['id'];
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE role = 'client' AND client_id = :client_id");
            $stmt->execute(['client_id' => $client_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                // Генерация и сохранение кода
                $code = rand(100000, 999999);
                $stmt = $pdo->prepare("INSERT INTO ConfirmationCodes (phone, telegram_id, code) VALUES (:phone, :telegram_id, :code)");
                $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code]);

                // Отправка кода через Telegram
                $botToken = '8168606272:AAFuikWYy8UKjzK3iuyMjRtWHCdS1KKECbE'; // Замените на ваш токен бота
                $text = "Ваш код подтверждения: $code";
                $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$telegram_id&text=" . urlencode($text);
                file_get_contents($url);

                $response['success'] = true;
            } else {
                $response['message'] = 'Пользователь не найден';
            }
        } else {
            $response['message'] = 'Номер телефона не зарегистрирован';
        }
    }
}

echo json_encode($response);
?>