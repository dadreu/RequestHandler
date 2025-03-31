<?php
include 'config.php'; // Подключение к базе данных
header('Content-Type: application/json');
$response = ['success' => false];

// Проверка подключения к базе данных
if (!$pdo) {
    $response['message'] = 'Ошибка подключения к базе данных';
    echo json_encode($response);
    exit;
}

if (isset($_POST['phone']) && isset($_POST['telegram_id']) && isset($_POST['code'])) {
    $phone = $_POST['phone'];
    $telegram_id = $_POST['telegram_id'];
    $code = $_POST['code'];

    // Проверка кода
    $stmt = $pdo->prepare("SELECT * FROM ConfirmationCodes WHERE phone = :phone AND telegram_id = :telegram_id AND code = :code AND created_at > NOW() - INTERVAL 5 MINUTE");
    if (!$stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code])) {
        $response['message'] = 'Ошибка при проверке кода';
        echo json_encode($response);
        exit;
    }
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($confirmation) {
        // Проверка мастера
        $stmt = $pdo->prepare("SELECT id FROM Masters WHERE phone = :phone");
        if (!$stmt->execute(['phone' => $phone])) {
            $response['message'] = 'Ошибка при поиске мастера';
            echo json_encode($response);
            exit;
        }
        $master = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($master) {
            $master_id = $master['id'];
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE role = 'master' AND master_id = :master_id");
            if (!$stmt->execute(['master_id' => $master_id])) {
                $response['message'] = 'Ошибка при поиске пользователя-мастера';
                echo json_encode($response);
                exit;
            }
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $response['success'] = true;
                $response['role'] = 'master';
                $response['master_id'] = $master_id;
            }
        } else {
            // Проверка клиента
            $stmt = $pdo->prepare("SELECT id FROM Clients WHERE phone = :phone");
            if (!$stmt->execute(['phone' => $phone])) {
                $response['message'] = 'Ошибка при поиске клиента';
                echo json_encode($response);
                exit;
            }
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($client) {
                $client_id = $client['id'];
                $stmt = $pdo->prepare("SELECT id FROM Users WHERE role = 'client' AND client_id = :client_id");
                if (!$stmt->execute(['client_id' => $client_id])) {
                    $response['message'] = 'Ошибка при поиске пользователя-клиента';
                    echo json_encode($response);
                    exit;
                }
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $response['success'] = true;
                    $response['role'] = 'client';
                    $response['client_id'] = $client_id;
                }
            }
        }
        // Удаление кода
        $stmt = $pdo->prepare("DELETE FROM ConfirmationCodes WHERE id = :id");
        $stmt->execute(['id' => $confirmation['id']]);
    } else {
        $response['message'] = 'Неверный код или код устарел';
    }
} else {
    $response['message'] = 'Недостаточно данных';
}

echo json_encode($response);