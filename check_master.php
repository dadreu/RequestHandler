<?php
include 'config.php';
header('Content-Type: application/json');

$response = ['available' => false];

if (!empty($_GET['id'])) {
    $master_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT id_master FROM Masters WHERE id_master = ?");
        $stmt->execute([$master_id]);
        if ($stmt->fetch()) {
            $response['available'] = true;
        }
    } catch (Exception $e) {
        $response['message'] = "Ошибка БД: " . $e->getMessage();
    }
}

echo json_encode($response);
?>