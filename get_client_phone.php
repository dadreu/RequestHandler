<?php
include 'config.php';
header('Content-Type: application/json');

$client_id = $_GET['client_id'];

if (empty($client_id)) {
    echo json_encode(['error' => 'Client ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT phone FROM Clients WHERE id_clients = :client_id");
    $stmt->bindParam(':client_id', $client_id);
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        echo json_encode(['phone' => $client['phone']]);
    } else {
        echo json_encode(['error' => 'Client not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>