<?php
header('Content-Type: application/json');
include 'config.php';

$master_id = isset($_GET['master_id']) ? (int)$_GET['master_id'] : 0;
$day_of_week = isset($_GET['day_of_week']) ? $_GET['day_of_week'] : '';

if ($master_id <= 0 || empty($day_of_week)) {
    echo json_encode(["error" => "Неверные параметры"]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT start_time, end_time FROM MasterSchedule WHERE master_id = :master_id AND day_of_week = :day_of_week");
    $stmt->execute(['master_id' => $master_id, 'day_of_week' => $day_of_week]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($schedule) {
        echo json_encode($schedule);
    } else {
        echo json_encode(null); // Мастер не работает в этот день
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Ошибка запроса: " . $e->getMessage()]);
}
?>