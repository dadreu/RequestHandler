<?php
include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

$response = ['success' => false];

if (!empty($_GET['master_id'])) {
    $master_id = intval($_GET['master_id']);
    $sort_field = $_GET['sort_field'] ?? 'date_time';
    $sort_order = $_GET['sort_order'] ?? 'ASC';

    // Допустимые поля для сортировки
    $allowed_fields = ['date_time', 'client_name', 'phone', 'service_name', 'price', 'duration'];
    $allowed_orders = ['ASC', 'DESC'];
    $sort_field = in_array($sort_field, $allowed_fields) ? $sort_field : 'date_time';
    $sort_order = in_array($sort_order, $allowed_orders) ? $sort_order : 'ASC';

    try {
        // Проверка существования мастера
        $stmt = $pdo->prepare("SELECT id_masters FROM Masters WHERE id_masters = ?");
        $stmt->execute([$master_id]);
        if (!$stmt->fetch()) {
            $response['message'] = "Мастер с указанным ID не найден.";
            echo json_encode($response);
            exit;
        }

        // Текущее пермское время (UTC+5)
        $permTime = new DateTime('now', new DateTimeZone('UTC'));
        $permTime->modify('+5 hours'); // Пермское время
        $currentTime = $permTime->format('Y-m-d H:i:s');
        $todayStart = $permTime->format('Y-m-d 00:00:00');

        // Запрос записей мастера
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

        // Разделяем записи на предстоящие и выполненные
        $upcoming = [];
        $completed = [];

        foreach ($appointments as $appointment) {
            if ($appointment['date_time'] >= $currentTime) {
                $upcoming[] = $appointment; // Предстоящие (позже текущего времени)
            } else {
                $completed[] = $appointment; // Выполненные (до текущего времени, включая сегодня)
            }
        }
        if ($upcoming || $completed) {
            $response['success'] = true;
            $response['upcoming'] = $upcoming;
            $response['completed'] = $completed;
        } else {
            $response['message'] = "Записи отсутствуют.";
        }
    } catch (Exception $e) {
        $response['message'] = "Ошибка БД: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID мастера не указан.";
}

echo json_encode($response);
?>