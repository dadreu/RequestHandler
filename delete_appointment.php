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

$appointment_id = intval($data['id']);
$master_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        DELETE a FROM Appointments a
        JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
        WHERE a.id_appointment = ? AND ms.master_id = ?
    ");
    $stmt->execute([$appointment_id, $master_id]);
    if ($stmt->rowCount() > 0) {
        $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
        $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Удалил запись с ID $appointment_id"]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => "Запись не найдена или нет прав"]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Ошибка БД: " . $e->getMessage()]);
}
?>