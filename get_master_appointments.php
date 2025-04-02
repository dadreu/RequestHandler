<?php
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false];

if (!empty($_GET['master_id'])) {
    $master_id = intval($_GET['master_id']);
    $sort_field = $_GET['sort_field'] ?? 'date_time'; // По умолчанию сортировка по дате
    $sort_order = $_GET['sort_order'] ?? 'ASC'; // По умолчанию по возрастанию

    // Допустимые поля для сортировки
    $allowed_fields = ['date_time', 'client_name', 'phone', 'service_name', 'price'];
    if (!in_array($sort_field, $allowed_fields)) {
        $sort_field = 'date_time'; // Защита от некорректных значений
    }
    if (!in_array($sort_order, ['ASC', 'DESC'])) {
        $sort_order = 'ASC';
    }

    try {
        $stmt = $pdo->prepare("
            SELECT a.date_time, a.price, c.full_name AS client_name, c.phone, s.name AS service_name
            FROM Appointments a
            JOIN Services s ON a.service_id = s.id
            JOIN Clients c ON a.client_id = c.id
            WHERE a.master_id = ? 
            ORDER BY " . $sort_field . " " . $sort_order . "
            LIMIT 0, 50
        ");
        $stmt->execute([$master_id]);
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
    $response['message'] = "ID мастера не найден.";
}

echo json_encode($response);
?>