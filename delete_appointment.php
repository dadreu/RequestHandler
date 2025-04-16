<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'master') {
        throw new Exception('Требуется авторизация мастера');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    $appointment_id = (int)($data['id'] ?? 0);
    $master_id = $_SESSION['user_id'];

    if (!$appointment_id) {
        throw new Exception('ID записи не указан');
    }

    $stmt = $pdo->prepare(
        "DELETE a FROM Appointments a
         JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
         WHERE a.id_appointment = :appointment_id AND ms.master_id = :master_id"
    );
    $stmt->execute(['appointment_id' => $appointment_id, 'master_id' => $master_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Запись не найдена или нет прав');
    }

    logAction($pdo, $master_id, 'master', "Удалил запись с ID $appointment_id");

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>