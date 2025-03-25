<?php
include 'config.php'; // Подключение к базе данных
header('Content-Type: application/json');

function verifyTelegramInitData($initData, $botToken) {
    parse_str($initData, $params);
    $checkHash = $params['hash'];
    unset($params['hash']);
    ksort($params);
    $dataCheckString = implode("\n", array_map(fn($k, $v) => "$k=$v", array_keys($params), $params));
    $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
    $hash = hash_hmac('sha256', $dataCheckString, $secretKey);
    return $hash === $checkHash;
}

$telegram_id = $_POST['telegram_id'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$initData = $_POST['initData'] ?? '';
$botToken = getenv('BOT_TOKEN'); // Токен бота из переменной окружения

$response = ['success' => false];

if (!empty($telegram_id) && !empty($full_name) && !empty($initData)) {
    if (!verifyTelegramInitData($initData, $botToken)) {
        $response['message'] = 'Неверный initData';
        echo json_encode($response);
        exit;
    }

    try {
        // Проверяем, существует ли клиент с таким telegram_id
        $stmt = $pdo->prepare("SELECT id FROM Clients WHERE telegram_id = :telegram_id");
        $stmt->execute(['telegram_id' => $telegram_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            $response['message'] = 'Пользователь уже зарегистрирован';
            $response['client_id'] = $client['id'];
        } else {
            // Регистрируем нового клиента
            $stmt = $pdo->prepare("INSERT INTO Clients (full_name, telegram_id) VALUES (:full_name, :telegram_id)");
            $stmt->execute(['full_name' => $full_name, 'telegram_id' => $telegram_id]);
            $response['success'] = true;
            $response['client_id'] = $pdo->lastInsertId();
        }
    } catch (PDOException $e) {
        $response['message'] = 'Ошибка базы данных: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Не все данные переданы';
}

echo json_encode($response);
?>