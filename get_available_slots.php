<?php
include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

$master_id = isset($_GET['master_id']) ? intval($_GET['master_id']) : 0;
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$master_id || !$service_id || !$date) {
    echo json_encode(['error' => 'Не указаны необходимые параметры']);
    exit;
}

try {
    // Получение id_master_service и duration
    $stmt = $pdo->prepare("SELECT id_master_service, duration FROM MasterServices WHERE master_id = ? AND service_id = ? AND is_available = 1");
    $stmt->execute([$master_id, $service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$service) {
        error_log("Услуга не найдена для master_id: $master_id, service_id: $service_id");
        echo json_encode(['error' => 'Услуга не найдена или недоступна']);
        exit;
    }
    $id_master_service = $service['id_master_service'];
    $duration = $service['duration'];
    error_log("Длительность услуги: $duration минут");

    // Определение дня недели
    $dayIndex = date('w', strtotime($date));
    $daysOfWeek = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
    $dayOfWeek = $daysOfWeek[$dayIndex];
    error_log("Дата: $date, Индекс дня: $dayIndex, День недели: $dayOfWeek");

    // Получение расписания
    $stmt = $pdo->prepare("SELECT start_time, end_time, is_day_off FROM MasterSchedule WHERE master_id = ? AND day_of_week = ?");
    $stmt->execute([$master_id, $dayOfWeek]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        error_log("Расписание не найдено для master_id: $master_id, day_of_week: $dayOfWeek");
        echo json_encode(['available_slots' => [], 'error' => "Расписание не найдено для $dayOfWeek"]);
        exit;
    }

    if ($schedule['is_day_off'] == 1) {
        error_log("День $dayOfWeek является выходным для мастера $master_id");
        echo json_encode(['available_slots' => [], 'error' => "Этот день является выходным"]);
        exit;
    }

    $start_time = $schedule['start_time'];
    $end_time = $schedule['end_time'];
    error_log("Расписание для $dayOfWeek: start_time = $start_time, end_time = $end_time");

    // Генерация временных слотов
    $start_dt = DateTime::createFromFormat('H:i:s', $start_time);
    $end_dt = DateTime::createFromFormat('H:i:s', $end_time);
    $latest_start_dt = clone $end_dt;
    $latest_start_dt->sub(new DateInterval('PT' . $duration . 'M'));

    $interval = new DateInterval('PT15M');
    $period = new DatePeriod($start_dt, $interval, $latest_start_dt);
    $possible_starts = [];
    foreach ($period as $dt) {
        $possible_starts[] = $dt->format('H:i');
    }
    error_log("Возможные слоты: " . implode(', ', $possible_starts));

    // Получение занятых слотов
    $stmt = $pdo->prepare("
        SELECT a.date_time, ms.duration
        FROM Appointments a
        JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
        WHERE ms.master_id = ? AND DATE(a.date_time) = ?
    ");
    $stmt->execute([$master_id, $date]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $occupied = [];
    foreach ($appointments as $app) {
        $app_start = new DateTime($app['date_time']);
        $app_end = clone $app_start;
        $app_end->add(new DateInterval('PT' . $app['duration'] . 'M'));
        $occupied[] = [$app_start->format('H:i'), $app_end->format('H:i')];
    }
    error_log("Занятые слоты: " . json_encode($occupied));

    // Определение доступных слотов
    $available_slots = [];
    foreach ($possible_starts as $start_time) {
        $start_dt = DateTime::createFromFormat('H:i', $start_time);
        $end_dt = clone $start_dt;
        $end_dt->add(new DateInterval('PT' . $duration . 'M'));
        $is_available = true;

        foreach ($occupied as $occ) {
            $occ_start = DateTime::createFromFormat('H:i', $occ[0]);
            $occ_end = DateTime::createFromFormat('H:i', $occ[1]);
            if ($start_dt < $occ_end && $end_dt > $occ_start) {
                $is_available = false;
                break;
            }
        }

        if ($is_available) {
            $available_slots[] = $start_time;
        }
    }

    error_log("Доступные слоты: " . implode(', ', $available_slots));
    echo json_encode(['available_slots' => $available_slots]);
} catch (PDOException $e) {
    error_log("Ошибка PDO: " . $e->getMessage());
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>