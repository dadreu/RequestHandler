<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/error.log');

$response = ['success' => false];

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'master') {
    $response['message'] = 'Не авторизован или недостаточно прав.';
    echo json_encode($response);
    exit;
}

$master_id = intval($_SESSION['user_id']);

try {
    $stmt = $pdo->prepare("SELECT id_masters FROM Masters WHERE id_masters = ?");
    $stmt->execute([$master_id]);
    if (!$stmt->fetch()) {
        $response['message'] = 'Мастер не найден.';
        echo json_encode($response);
        exit;
    }

    $sort_field = $_GET['sort_field'] ?? 'date_time';
    $sort_order = $_GET['sort_order'] ?? 'ASC';
    $allowed_fields = ['date_time', 'client_name', 'phone', 'service_name', 'price', 'duration'];
    $allowed_orders = ['ASC', 'DESC'];
    $sort_field = in_array($sort_field, $allowed_fields) ? $sort_field : 'date_time';
    $sort_order = in_array($sort_order, $allowed_orders) ? $sort_order : 'ASC';

    $stmt = $pdo->prepare("
        SELECT a.id_appointment, a.date_time, ms.price, ms.duration, c.full_name AS client_name, c.phone, s.name AS service_name
        FROM Appointments a
        JOIN MasterServices ms ON a.master_id = ms.master_id AND a.service_id = ms.service_id
        JOIN Services s ON a.service_id = s.id_service
        JOIN Clients c ON a.client_id = c.id_clients
        WHERE a.master_id = ?
        ORDER BY $sort_field $sort_order
        LIMIT 0, 50
    ");
    $stmt->execute([$master_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $upcoming = [];
    $completed = [];
    $permTime = new DateTime('now', new DateTimeZone('UTC'));
    $permTime->modify('+5 hours');
    $currentTime = $permTime->format('Y-m-d H:i:s');

    foreach ($appointments as $appointment) {
        if ($appointment['date_time'] >= $currentTime) {
            $upcoming[] = $appointment;
        } else {
            $completed[] = $appointment;
        }
    }

    if ($upcoming || $completed) {
        $response['success'] = true;
        $response['upcoming'] = $upcoming;
        $response['completed'] = $completed;
    } else {
        $response['message'] = 'Записи отсутствуют.';
    }
} catch (Exception $e) {
    error_log('Ошибка в get_master_appointments.php: ' . $e->getMessage());
    $response['message'] = 'Ошибка сервера.';
}

echo json_encode($response);
?>