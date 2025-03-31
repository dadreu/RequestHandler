<?php
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['password'])) {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Поиск пользователя
    $stmt = $pdo->prepare("SELECT * FROM Masters WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверка пароля
    if ($user && $user['password'] === $password) {
        $response['success'] = true;
        $response['role'] = $user['role']; // Передаем роль

        if ($user['role'] === 'master') {
            $response['master_id'] = $user['master_id']; // Передаем master_id
        } elseif ($user['role'] === 'client') {
            $response['client_id'] = $user['client_id']; // Передаем client_id
        }
    }
}

// Отправка ответа
echo json_encode($response);
?>