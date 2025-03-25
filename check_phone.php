<?php
include 'config.php';

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';

if (empty($phone)) {
    echo json_encode(['role' => null, 'message' => 'Номер телефона не указан.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM Masters WHERE phone = ?");
$stmt->execute([$phone]);
$master = $stmt->fetch();

if ($master) {
    echo json_encode(['role' => 'master']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM Clients WHERE phone = ?");
$stmt->execute([$phone]);
$client = $stmt->fetch();

if ($client) {
    echo json_encode(['role' => 'client', 'client_id' => $client['id']]);
} else {
    echo json_encode(['role' => null, 'message' => 'Номер телефона не найден.']);
}
?>