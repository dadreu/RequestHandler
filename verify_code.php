<?php
session_start();
include 'config.php';
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['code'])) {
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    if (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1);
    }
    $code = $_POST['code'];

    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $response['message'] = 'Неверный CSRF-токен';
        echo json_encode($response);
        exit;
    }

    // Получаем последний код
    $stmt = $pdo->prepare("SELECT * FROM ConfirmationCodes WHERE phone = :phone ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['phone' => $phone]);
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($confirmation && $confirmation['code'] == $code) {
        $created_at = new DateTime($confirmation['created_at']);
        $now = new DateTime();
        $interval = $now->diff($created_at);
        if ($interval->i < 5) { // Код действителен 5 минут
            $stmt = $pdo->prepare("SELECT id_clients FROM Clients WHERE phone = :phone");
            $stmt->execute(['phone' => $phone]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($client) {
                // Устанавливаем сессионные переменные
                $_SESSION['user_id'] = $client['id_clients'];
                $_SESSION['role'] = 'client';
                
                $response['success'] = true;
                $response['client_id'] = $client['id_clients'];
                
                // Логирование успешной авторизации
                $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
                $stmt_log->execute([$client['id_clients'], 'client', 'Авторизация клиента']);
            } else {
                $response['message'] = 'Клиент с таким номером телефона не найден';
            }
        } else {
            $response['message'] = 'Код устарел';
        }
    } else {
        $response['message'] = 'Неверный код';
    }
} else {
    $response['message'] = 'Недостаточно данных';
}

echo json_encode($response);
?>