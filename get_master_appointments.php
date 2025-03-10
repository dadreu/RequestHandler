<?php
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false];

if (!empty($_GET['master_id'])) {
    $master_id = intval($_GET['master_id']); // Преобразуем ID в целое число для безопасности

    try {
        // Логируем master_id (для отладки)
        error_log("Received master_id: " . $master_id);

        // Получаем записи по ID мастера с именем клиента из таблицы Clients
        $stmt = $pdo->prepare("
            SELECT a.date_time, a.price, c.full_name AS client_name, s.name AS service_name
            FROM Appointments a
            JOIN Services s ON a.service_id = s.id
            JOIN Clients c ON a.client_id = c.id
            WHERE a.master_id = ? 
            ORDER BY a.date_time
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
