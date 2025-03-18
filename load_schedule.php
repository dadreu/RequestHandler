<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'config.php';
header('Content-Type: application/json');

if (!empty($_GET['master_id'])) {
    $master_id = $_GET['master_id'];

    try {
        // Проверяем, есть ли расписание для мастера
        $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM MasterSchedule WHERE master_id = :master_id");
        $stmt->execute(['master_id' => $master_id]);
        $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Если расписание отсутствует, создаем его по умолчанию
        if (empty($schedule)) {
            $days_of_week = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']; // Новый порядок
            $start_time = '09:00:00';
            $end_time = '18:00:00';

            // Вставляем расписание по умолчанию
            $stmt = $pdo->prepare(
                "INSERT INTO MasterSchedule (master_id, day_of_week, start_time, end_time)
                VALUES (:master_id, :day_of_week, :start_time, :end_time)"
            );

            foreach ($days_of_week as $day) {
                $stmt->execute([
                    'master_id' => $master_id,
                    'day_of_week' => $day,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ]);
            }

            // Повторно загружаем расписание после вставки
            $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM MasterSchedule WHERE master_id = :master_id");
            $stmt->execute(['master_id' => $master_id]);
            $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode(['success' => true, 'schedule' => $schedule]);

    } catch (PDOException $e) {
        echo json_encode(["error" => "Ошибка запроса: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => "Не указан master_id"]);
}
?>