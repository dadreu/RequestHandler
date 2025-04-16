<?php
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Возвращает доступные временные слоты для записи.
 */
try {
    $master_id = (int)($_GET['master_id'] ?? 0);
    $service_id = (int)($_GET['service_id'] ?? 0);
    $date = $_GET['date'] ?? '';

    if (!$master_id || !$service_id || !$date) {
        throw new Exception('Не указаны необходимые параметры');
    }

    // Проверка услуги
    $stmt = $pdo->prepare(
        "SELECT id_master_service, duration 
         FROM MasterServices 
         WHERE master_id = :master_id AND service_id = :service_id AND is_available = 1"
    );
    $stmt->execute(['master_id' => $master_id, 'service_id' => $service_id]);
    $service = $stmt->fetch();

    if (!$service) {
        throw new Exception('Услуга не найдена или недоступна');
    }

    $duration = $service['duration'];

    // Определение дня недели
    $day_of_week = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'][date('w', strtotime($date))];

    // Получение расписания
    $stmt = $pdo->prepare(
        "SELECT start_time, end_time, is_day_off 
         FROM MasterSchedule 
         WHERE master_id = :master_id AND day_of_week = :day_of_week"
    );
    $stmt->execute(['master_id' => $master_id, 'day_of_week' => $day_of_week]);
    $schedule = $stmt->fetch();

    if (!$schedule || $schedule['is_day_off']) {
        echo json_encode(['available_slots' => []]);
        exit;
    }

    // Генерация слотов
    $start = DateTime::createFromFormat('H:i:s', $schedule['start_time']);
    $end = DateTime::createFromFormat('H:i:s', $schedule['end_time']);
    $end->sub(new DateInterval("PT{$duration}M"));

    $slots = [];
    $interval = new DateInterval('PT15M');
    foreach (new DatePeriod($start, $interval, $end) as $dt) {
        $slots[] = $dt->format('H:i');
    }

    // Получение занятых слотов
    $stmt = $pdo->prepare(
        "SELECT a.date_time, ms.duration
         FROM Appointments a
         JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
         WHERE ms.master_id = :master_id AND DATE(a.date_time) = :date"
    );
    $stmt->execute(['master_id' => $master_id, 'date' => $date]);
    $appointments = $stmt->fetchAll();

    $occupied = array_map(function ($app) {
        $start = new DateTime($app['date_time']);
        $end = clone $start;
        $end->add(new DateInterval("PT{$app['duration']}M"));
        return [$start->format('H:i'), $end->format('H:i')];
    }, $appointments);

    // Фильтрация доступных слотов
    $available_slots = array_filter($slots, function ($slot) use ($occupied, $duration) {
        $start = DateTime::createFromFormat('H:i', $slot);
        $end = clone $start;
        $end->add(new DateInterval("PT{$duration}M"));

        foreach ($occupied as $occ) {
            $occ_start = DateTime::createFromFormat('H:i', $occ[0]);
            $occ_end = DateTime::createFromFormat('H:i', $occ[1]);
            if ($start < $occ_end && $end > $occ_start) {
                return false;
            }
        }
        return true;
    });

    echo json_encode(['available_slots' => array_values($available_slots)]);
} catch (Exception $e) {
    error_log("Ошибка в get_available_slots.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>