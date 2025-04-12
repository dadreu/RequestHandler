<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'master') {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация мастера']);
    exit;
}

$master_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time, is_day_off FROM MasterSchedule WHERE master_id = ?");
    $stmt->execute([$master_id]);
    $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($schedule)) {
        $days_of_week = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        $start_time = '09:00:00';
        $end_time = '18:00:00';
        $is_day_off = 0;

        $stmt = $pdo->prepare(
            "INSERT INTO MasterSchedule (master_id, day_of_week, start_time, end_time, is_day_off)
            VALUES (:master_id, :day_of_week, :start_time, :end_time, :is_day_off)"
        );

        foreach ($days_of_week as $day) {
            $stmt->execute([
                'master_id' => $master_id,
                'day_of_week' => $day,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'is_day_off' => $is_day_off
            ]);
        }

        $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time, is_day_off FROM MasterSchedule WHERE master_id = ?");
        $stmt->execute([$master_id]);
        $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['success' => true, 'schedule' => $schedule]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Ошибка запроса: " . $e->getMessage()]);
}
?>