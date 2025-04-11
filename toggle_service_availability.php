<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'master') {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация мастера']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Неверный CSRF-токен']);
    exit;
}

$master_id = $_SESSION['user_id'];
$services = $data['services'] ?? null;

if (!is_array($services) || empty($services)) {
    echo json_encode(['success' => false, 'message' => 'Данные услуг не переданы']);
    exit;
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE MasterServices SET is_available = :available WHERE master_id = :master_id AND service_id = :service_id");

    foreach ($services as $service) {
        $service_id = intval($service['service_id'] ?? 0);
        $available = intval($service['available'] ?? 0);
        if ($service_id === 0) continue;

        $stmt->execute(['available' => $available, 'master_id' => $master_id, 'service_id' => $service_id]);
    }

    $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Обновил доступность услуг"]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
}
?>