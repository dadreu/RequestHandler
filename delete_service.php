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

$service_id = intval($data['id']);
$master_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM MasterServices WHERE service_id = ? AND master_id = ?");
    $stmt->execute([$service_id, $master_id]);
    if ($stmt->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'У вас нет прав для удаления этой услуги']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Appointments WHERE service_id = ?");
    $stmt->execute([$service_id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Нельзя удалить услугу, так как она используется в записях']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM MasterServices WHERE service_id = ? AND master_id = ?");
    $stmt->execute([$service_id, $master_id]);

    $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Удалил услугу с ID $service_id"]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка БД: ' . $e->getMessage()]);
}
?>