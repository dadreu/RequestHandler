<?php
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false];

if (!empty($_GET['client_id'])) {
    $client_id = intval($_GET['client_id']); // Преобразуем ID клиента в целое число для безопасности

    try {
        error_log("Received client_id: " . $client_id);

        $stmt = $pdo->prepare("
            SELECT a.date_time, a.price, s.name AS service_name
            FROM Appointments a
            JOIN Services s ON a.service_id = s.id
            WHERE a.client_id = ?
            ORDER BY a.date_time
        ");
        $stmt->execute([$client_id]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($appointments) {
            $response['success'] = true;
            $response['appointments'] = $appointments;
        } else {
            $response['message'] = "У вас нет записей.";
        }
    } catch (Exception $e) {
        $response['error'] = "Ошибка БД: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID клиента не найден.";
}

echo json_encode($response);
?>
