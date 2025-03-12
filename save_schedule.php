<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['master_id']) && !empty($data['schedule'])) {
    $master_id = $data['master_id'];
    $schedule = $data['schedule'];

    try {
        // Удаляем старое расписание для мастера
        $stmt = $pdo->prepare("DELETE FROM MasterSchedule WHERE master_id = :master_id");
        $stmt->execute(['master_id' => $master_id]);

        // Сохраняем новое расписание
        $stmt = $pdo->prepare("INSERT INTO MasterSchedule (master_id, day_of_week, start_time, end_time) VALUES (:master_id, :day_of_week, :start_time, :end_time)");

        foreach ($schedule as $item) {
            $stmt->execute([
                'master_id' => $master_id,
                'day_of_week' => $item['day_of_week'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time']
            ]);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Ошибка сохранения: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Не указаны необходимые данные']);
}
?>
