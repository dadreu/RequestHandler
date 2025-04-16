<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['available' => false];

try {
    $master_id = $_GET['id'] ?? null;
    if (!$master_id) {
        throw new Exception('ID мастера не указан');
    }

    $stmt = $pdo->prepare("SELECT id_masters FROM Masters WHERE id_masters = :master_id");
    $stmt->execute(['master_id' => (int)$master_id]);
    $response['available'] = (bool)$stmt->fetch();

    echo json_encode($response);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}
?>