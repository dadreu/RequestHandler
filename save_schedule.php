<?php
include 'config.php';
header('Content-Type: application/json');

// Получаем данные из тела запроса (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Проверяем master_id: сначала из GET, затем из JSON
$master_id = isset($_GET['master_id']) ? (int)$_GET['master_id'] : (isset($data['master_id']) ? (int)$data['master_id'] : 0);
$schedule = isset($data['schedule']) ? $data['schedule'] : null;

if ($master_id > 0 && !empty($schedule)) {
    try {
        // Удаляем старое расписание
        $stmt = $pdo->prepare("DELETE FROM MasterSchedule WHERE master_id = :master_id");
        $stmt->execute(['master_id' => $master_id]);

        // Сохраняем новое расписание
        $stmt = $pdo->prepare("INSERT INTO MasterSchedule (master_id, day_of_week, start_time, end_time) VALUES (:master_id, :day_of_week, :start_time, :end_time)");

        foreach ($schedule as $item) {
            // Валидация дня недели
            $valid_days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
            if (!in_array($item['day_of_week'], $valid_days)) {
                throw new Exception("Недопустимый день недели: " . $item['day_of_week']);
            }

            $stmt->execute([
                'master_id' => $master_id,
                'day_of_week' => $item['day_of_week'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time']
            ]);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(["error" => "Ошибка сохранения: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Не указаны необходимые данные (master_id или schedule)']);
}
?>