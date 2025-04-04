<?php
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false];

if (!empty($_GET['client_id'])) {
    $client_id = intval($_GET['client_id']);

    try {
        $stmt = $pdo->prepare("
            SELECT a.date_time, ms.price, ms.duration, s.name AS service_name, m.phone AS master_phone
            FROM Appointments a
            JOIN MasterServices ms ON a.master_id = ms.master_id AND a.service_id = ms.service_id
            JOIN Services s ON a.service_id = s.id
            JOIN Masters m ON a.master_id = m.id
            WHERE a.client_id = ?
            ORDER BY a.date_time
            LIMIT 0, 50
        ");
        $stmt->execute([$client_id]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($appointments) {
            $response['success'] = true;
            $response['appointments'] = $appointments;
        } else {
            $response['message'] = "Записи отсутствуют.";
        }
    } catch (Exception $e) {
        $response['error'] = "Ошибка БД: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID клиента не найден.";
}

echo json_encode($response);
?>