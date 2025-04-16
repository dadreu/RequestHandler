<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Возвращает доступные услуги для мастера в салоне из сессии.
 */
try {
    // Проверка сессии
    if (!isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён. Пожалуйста, перезапустите приложение');
    }

    $salon_id = (int)$_SESSION['salon_id'];
    $master_id = (int)($_GET['master_id'] ?? 0);

    if (!$master_id) {
        throw new Exception('Не указан master_id');
    }

    // Проверка принадлежности мастера к салону
    $stmt = $pdo->prepare(
        "SELECT id_masters 
         FROM Masters 
         WHERE id_masters = :master_id AND salon_id = :salon_id"
    );
    $stmt->execute(['master_id' => $master_id, 'salon_id' => $salon_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Мастер не принадлежит салону');
    }

    // Получение услуг
    $stmt = $pdo->prepare(
        "SELECT s.id_service, s.name, ms.price, ms.duration
         FROM Services s
         JOIN MasterServices ms ON s.id_service = ms.service_id
         WHERE ms.master_id = :master_id 
         AND ms.is_available = 1 
         AND s.salon_id = :salon_id
         ORDER BY s.name
         LIMIT 50"
    );
    $stmt->execute(['master_id' => $master_id, 'salon_id' => $salon_id]);
    $services = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'services' => $services
    ]);
} catch (Exception $e) {
    error_log("Ошибка в get_services.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>