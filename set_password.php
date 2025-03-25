<?php
include 'config.php';

header('Content-Type: application/json');

$client_id = $_POST['client_id'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($client_id) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Необходимые данные не указаны.']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT id FROM Users WHERE client_id = ?");
$stmt->execute([$client_id]);
$user = $stmt->fetch();

if ($user) {
    $stmt = $pdo->prepare("UPDATE Users SET password = ? WHERE client_id = ?");
    $result = $stmt->execute([$hashed_password, $client_id]);
} else {
    $username = 'client_' . $client_id . '_' . time(); // Уникальный логин
    $stmt = $pdo->prepare("INSERT INTO Users (username, password, role, client_id) VALUES (?, ?, 'client', ?)");
    $result = $stmt->execute([$username, $hashed_password, $client_id]);
}

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка сохранения пароля.']);
}
?>