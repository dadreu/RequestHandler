<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['available' => false];

try {
    $master_id = $_GET['master_id'] ?? null;
    $service_id = $_GET['service_id'] ?? null;

    if (!$master_id || !$service_id) {
        throw new Exception('Не указаны master_id или service_id');
    }

    $stmt = $pdo->prepare(
        "SELECT id_master_service 
         FROM MasterServices 
         WHERE master_id = :master_id AND service_id = :service_id AND is_available = 1"
    );
    $stmt->execute(['master_id' => (int)$master_id, 'service_id' => (int)$service_id]);
    $response['available'] = (bool)$stmt->fetch();

    echo json_encode($response);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}
?>