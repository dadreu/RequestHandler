<?php
// Подключение к базе данных
include 'config.php';

// Получаем данные из POST запроса
$data = json_decode(file_get_contents('php://input'), true);

// Обрабатываем каждую услугу
foreach ($data as $service) {
    $master_id = $service['master_id'];
    $service_id = $service['service_id'];
    $available = $service['available'];

    // Обновляем доступность услуги
    $query = "UPDATE MasterServices SET is_available = ? WHERE master_id = ? AND service_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$available, $master_id, $service_id]);
}

echo json_encode(['success' => true]);
?>