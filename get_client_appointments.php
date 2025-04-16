<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация клиента']);
    exit;
}

$client_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT a.date_time, ms.price, ms.duration, s.name AS service_name, m.phone AS master_phone
        FROM Appointments a
        JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
        JOIN Services s ON ms.service_id = s.id_service
        JOIN Masters m ON ms.master_id = m.id_masters
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

echo json_encode($response);
?>