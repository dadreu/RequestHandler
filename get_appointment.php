<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false];

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['master', 'client'])) {
    $response['message'] = "Требуется авторизация";
    echo json_encode($response);
    exit;
}

if (!empty($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    $role = $_SESSION['role'];
    $user_id = $_SESSION['user_id'];

    try {
        $sql = "
            SELECT a.id_appointment, ms.master_id, a.client_id, ms.service_id, a.date_time, ms.price, ms.duration, c.full_name AS client_name, c.phone
            FROM Appointments a
            JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
            JOIN Clients c ON a.client_id = c.id_clients
            WHERE a.id_appointment = ?
        ";
        if ($role === 'master') {
            $sql .= " AND ms.master_id = ?";
        } else {
            $sql .= " AND a.client_id = ?";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$appointment_id, $user_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($appointment) {
            $response['success'] = true;
            $response['appointment'] = $appointment;
        } else {
            $response['message'] = "Запись не найдена или нет прав";
        }
    } catch (Exception $e) {
        $response['message'] = "Ошибка БД: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID записи не указан";
}

echo json_encode($response);
?>