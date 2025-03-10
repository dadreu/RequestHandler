<?php
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false];

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Поиск пользователя
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username");
    $stmt->execute(['username' => $username]);
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
