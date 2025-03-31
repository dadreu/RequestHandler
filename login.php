<?php
include 'config.php';
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['password'])) {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM Masters WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $master = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($master && password_verify($password, $master['password'])) {
        $response['success'] = true;
        $response['master_id'] = $master['id'];
    } else {
        $response['message'] = 'Неверный номер телефона или пароль';
    }
}

echo json_encode($response);
?>