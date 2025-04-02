<?php
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false];

if (!empty($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("
            SELECT a.id, a.master_id, a.client_id, a.service_id, a.date_time, a.price, c.full_name AS client_name, c.phone
            FROM Appointments a
            JOIN Clients c ON a.client_id = c.id
            WHERE a.id = ?
        ");
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($appointment) {
            $response['success'] = true;
            $response['appointment'] = $appointment;
        } else {
            $response['message'] = "Запись не найдена";
        }
    } catch (Exception $e) {
        $response['message'] = "Ошибка БД: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID записи не указан";
}

echo json_encode($response);
?>