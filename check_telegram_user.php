<?php
include 'config.php';
header('Content-Type: application/json');

$telegram_id = $_POST['telegram_id'] ?? '';
$response = ['success' => false];

if (!empty($telegram_id)) {
    // Проверяем мастера
    $stmt = $pdo->prepare("SELECT id FROM Masters WHERE telegram_id = :telegram_id");
    $stmt->execute(['telegram_id' => $telegram_id]);
    $master = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($master) {
        $response['success'] = true;
        $response['role'] = 'master';
        $response['master_id'] = $master['id'];
    } else {
        // Проверяем клиента
        $stmt = $pdo->prepare("SELECT id FROM Clients WHERE telegram_id = :telegram_id");
        $stmt->execute(['telegram_id' => $telegram_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            $response['success'] = true;
            $response['role'] = 'client';
            $response['client_id'] = $client['id'];
        }
    }
}

echo json_encode($response);
?>