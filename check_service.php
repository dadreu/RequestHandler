<?php
include 'config.php';
header('Content-Type: application/json');

$response = ['available' => false];

if (!empty($_GET['master_id']) && !empty($_GET['service_id'])) {
    $master_id = intval($_GET['master_id']);
    $service_id = intval($_GET['service_id']);
    try {
        $stmt = $pdo->prepare("SELECT id_master_service FROM MasterServices WHERE master_id = ? AND service_id = ? AND is_available = 1");
        $stmt->execute([$master_id, $service_id]);
        if ($stmt->fetch()) {
            $response['available'] = true;
        }
    } catch (Exception $e) {
        $response['message'] = "Ошибка БД: " . $e->getMessage();
    }
}

echo json_encode($response);
?>