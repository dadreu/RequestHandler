<?php
include 'config.php';
header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';
$fullName = $_POST['full_name'] ?? '';

if (empty($phone) || empty($fullName)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}

// Проверка, что номер не занят
$stmt = $pdo->prepare("SELECT * FROM Clients WHERE phone = ?");
$stmt->execute([$phone]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Номер телефона уже зарегистрирован']);
    exit;
}

// Добавляем нового клиента
$stmt = $pdo->prepare("INSERT INTO Clients (full_name, phone, telegram_id) VALUES (?, ?, 0)");
$stmt->execute([$fullName, $phone]);
$clientId = $pdo->lastInsertId();

// Генерация кода (заглушка)
$code = sprintf("%06d", rand(0, 999999));

// В реальном приложении отправьте код через SMS или Telegram
error_log("Код для клиента $clientId: $code");

echo json_encode(['success' => true, 'client_id' => $clientId, 'code' => $code]);
?>