<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

$response = ['success' => false];

if (isset($_SESSION['reg_phone']) && $_SESSION['reg_phone'] === $phone) {
    $stmt = $pdo->prepare("INSERT INTO Clients (full_name, phone) VALUES (:full_name, :phone)");
    $stmt->execute(['full_name' => 'Новый пользователь', 'phone' => $phone]);
    
    $clientId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("INSERT INTO Users (username, password, role, client_id) VALUES (:username, :password, 'client', :client_id)");
    $stmt->execute([
        'username' => $phone,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'client_id' => $clientId
    ]);

    unset($_SESSION['reg_code']);
    unset($_SESSION['reg_phone']);
    $response['success'] = true;
}

echo json_encode($response);
?>