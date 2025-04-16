<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Возвращает записи мастера.
 */
try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'master') {
        throw new Exception('Требуется авторизация мастера');
    }

    if (!isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён');
    }

    $master_id = (int)$_SESSION['user_id'];
    $salon_id = (int)$_SESSION['salon_id'];
    $sort_field = $_GET['sort_field'] ?? 'date_time';
    $sort_order = $_GET['sort_order'] ?? 'ASC';

    $valid_fields = ['date_time', 'client_name', 'phone', 'service_name', 'price'];
    $sort_field = in_array($sort_field, $valid_fields) ? $sort_field : 'date_time';
    $sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

    $stmt = $pdo->prepare(
        "SELECT a.id_appointment, a.date_time, c.full_name AS client_name, c.phone, 
                s.name AS service_name, ms.price
         FROM Appointments a
         JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
         JOIN Services s ON ms.service_id = s.id_service
         JOIN Clients c ON a.client_id = c.id_clients
         WHERE ms.master_id = :master_id AND s.salon_id = :salon_id
         ORDER BY $sort_field $sort_order"
    );
    $stmt->execute(['master_id' => $master_id, 'salon_id' => $salon_id]);
    $appointments = $stmt->fetchAll();

    $upcoming = [];
    $completed = [];
    $now = new DateTime('now', new DateTimeZone('Asia/Yekaterinburg'));

    foreach ($appointments as $app) {
        $app_time = new DateTime($app['date_time'], new DateTimeZone('Asia/Yekaterinburg'));
        $app['date_time'] = $app_time->format('Y-m-d H:i');
        if ($app_time > $now) {
            $upcoming[] = $app;
        } else {
            $completed[] = $app;
        }
    }

    echo json_encode([
        'success' => true,
        'upcoming' => $upcoming,
        'completed' => $completed
    ]);
} catch (Exception $e) {
    error_log("Ошибка в get_master_appointments.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>