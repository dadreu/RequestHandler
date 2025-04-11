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
$schedule = $data['schedule'] ?? null;

if ($master_id > 0 && !empty($schedule)) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM MasterSchedule WHERE master_id = :master_id");
        $stmt->execute(['master_id' => $master_id]);

        $stmt = $pdo->prepare(
            "INSERT INTO MasterSchedule (master_id, day_of_week, start_time, end_time, is_day_off) 
            VALUES (:master_id, :day_of_week, :start_time, :end_time, :is_day_off)"
        );

        foreach ($schedule as $item) {
            $valid_days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
            if (!in_array($item['day_of_week'], $valid_days)) {
                throw new Exception("Недопустимый день недели: " . $item['day_of_week']);
            }
            $stmt->execute([
                'master_id' => $master_id,
                'day_of_week' => $item['day_of_week'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
                'is_day_off' => $item['is_day_off']
            ]);
        }

        $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
        $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Сохранил расписание"]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Ошибка сохранения: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Не указаны необходимые данные']);
}
?>