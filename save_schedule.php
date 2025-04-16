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

if (!is_array($schedule) || empty($schedule)) {
    echo json_encode(['success' => false, 'message' => 'Расписание не передано']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Удаление старого расписания
    $stmt = $pdo->prepare("DELETE FROM MasterSchedule WHERE master_id = :master_id");
    $stmt->execute(['master_id' => $master_id]);

    // Подготовка запроса для вставки
    $stmt = $pdo->prepare(
        "INSERT INTO MasterSchedule (master_id, day_of_week, start_time, end_time, is_day_off) 
        VALUES (:master_id, :day_of_week, :start_time, :end_time, :is_day_off)"
    );

    $valid_days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
    foreach ($schedule as $item) {
        // Валидация дня недели
        if (!isset($item['day_of_week']) || !in_array($item['day_of_week'], $valid_days)) {
            throw new Exception("Недопустимый день недели: " . ($item['day_of_week'] ?? 'не указан'));
        }

        // Валидация времени
        if (!isset($item['start_time']) || !isset($item['end_time'])) {
            throw new Exception("Время начала или окончания не указано для дня: " . $item['day_of_week']);
        }

        if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $item['start_time']) ||
            !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $item['end_time'])) {
            throw new Exception("Неверный формат времени для дня: " . $item['day_of_week']);
        }

        // Валидация is_day_off
        $is_day_off = isset($item['is_day_off']) ? (int)$item['is_day_off'] : 0;
        if ($is_day_off !== 0 && $is_day_off !== 1) {
            throw new Exception("Недопустимое значение is_day_off для дня: " . $item['day_of_week']);
        }

        // Если не выходной, проверяем, что end_time > start_time
        if (!$is_day_off) {
            $start_dt = DateTime::createFromFormat('H:i:s', $item['start_time']);
            $end_dt = DateTime::createFromFormat('H:i:s', $item['end_time']);
            if ($end_dt <= $start_dt) {
                throw new Exception("Время окончания должно быть позже времени начала для дня: " . $item['day_of_week']);
            }
        }

        $stmt->execute([
            'master_id' => $master_id,
            'day_of_week' => $item['day_of_week'],
            'start_time' => $item['start_time'],
            'end_time' => $item['end_time'],
            'is_day_off' => $is_day_off
        ]);
    }

    // Логирование
    $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Сохранил расписание"]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Ошибка сохранения расписания: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сохранения: ' . $e->getMessage()]);
}
?>