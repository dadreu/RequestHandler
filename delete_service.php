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

    $service_id = (int)($data['id'] ?? 0);
    $master_id = $_SESSION['user_id'];

    if (!$service_id) {
        throw new Exception('ID услуги не указан');
    }

    // Проверка прав на услугу
    $stmt = $pdo->prepare(
        "SELECT id_master_service 
         FROM MasterServices 
         WHERE service_id = :service_id AND master_id = :master_id"
    );
    $stmt->execute(['service_id' => $service_id, 'master_id' => $master_id]);
    $master_service = $stmt->fetch();

    if (!$master_service) {
        throw new Exception('У вас нет прав для удаления этой услуги');
    }

    $id_master_service = $master_service['id_master_service'];

    // Проверка использования услуги в записях
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Appointments WHERE id_master_service = :id_master_service");
    $stmt->execute(['id_master_service' => $id_master_service]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Нельзя удалить услугу, используемую в записях');
    }

    // Удаление услуги
    $stmt = $pdo->prepare("DELETE FROM MasterServices WHERE id_master_service = :id_master_service");
    $stmt->execute(['id_master_service' => $id_master_service]);

    logAction($pdo, $master_id, 'master', "Удалил услугу с ID $service_id");

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>