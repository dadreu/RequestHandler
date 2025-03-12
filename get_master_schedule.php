<?php
include 'config.php';
header('Content-Type: application/json');

if (!empty($_GET['master_id'])) {
    $master_id = $_GET['master_id'];

    try {
        $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM MasterSchedule WHERE master_id = :master_id");
        $stmt->execute(['master_id' => $master_id]);
        $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'schedule' => $schedule]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Ошибка запроса: " . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => "Не указан master_id"]);
}
?>
