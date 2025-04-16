<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Возвращает записи клиента.
 */
try {
    // Проверка авторизации
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
        throw new Exception('Требуется авторизация клиента');
    }

    // Проверка salon_id
    if (!isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён. Пожалуйста, перезапустите приложение');
    }

    $client_id = (int)$_SESSION['user_id'];
    $salon_id = (int)$_SESSION['salon_id'];

    // Получение записей
    $stmt = $pdo->prepare(
        "SELECT a.id_appointment, a.date_time, ms.price, ms.duration, 
                s.name AS service_name, m.phone AS master_phone, m.full_name AS master_name
         FROM Appointments a
         JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
         JOIN Services s ON ms.service_id = s.id_service
         JOIN Masters m ON ms.master_id = m.id_masters
         WHERE a.client_id = :client_id AND m.salon_id = :salon_id
         ORDER BY a.date_time DESC
         LIMIT 50"
    );
    $stmt->execute(['client_id' => $client_id, 'salon_id' => $salon_id]);
    $appointments = $stmt->fetchAll();

    // Форматирование дат
    foreach ($appointments as &$app) {
        $app['date_time'] = (new DateTime($app['date_time'], new DateTimeZone('Asia/Yekaterinburg')))
            ->format('Y-m-d H:i');
    }

    echo json_encode([
        'success' => true,
        'appointments' => $appointments ?: []
    ]);
} catch (Exception $e) {
    error_log("Ошибка в get_client_appointments.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>