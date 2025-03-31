<?php
include 'config.php'; // Подключение к базе данных
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['password'])) {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Подготовленный запрос к таблице Masters
    $stmt = $pdo->prepare("SELECT id, password FROM Masters WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $master = $stmt->fetch(PDO::FETCH_ASSOC);

    // Сравнение пароля напрямую
    if ($master && $master['password'] === $password) {
        $response['success'] = true;
        $response['master_id'] = $master['id'];
    } else {
        $response['message'] = 'Неверный номер телефона или пароль';
    }
}

echo json_encode($response);
?>