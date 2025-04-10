<?php
// Подключение к базе данных
include 'config.php';

try {
    // Получаем masterId
    $master_id = $_GET['master_id'];

    // Получаем список услуг с доступностью для мастера
    $query = "SELECT s.id_service, s.name, ms.price, ms.duration, ms.is_available 
              FROM Services s
              JOIN MasterServices ms ON s.id_service = ms.service_id
              WHERE ms.master_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$master_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Возвращаем данные в формате JSON
    echo json_encode(['success' => true, 'services' => $services]);
} catch (Exception $e) {
    // Отображаем ошибку, если она произошла
    echo json_encode(['success' => false, 'message' => 'Ошибка на сервере: ' . $e->getMessage()]);
}
?>