<?php
// Запускаем сессию, если она ещё не активна
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключение к базе данных
include 'config.php';

header('Content-Type: application/json; charset=UTF-8');

// Настройка логирования ошибок
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/error.log');

$response = ['success' => false];

try {
    // Проверяем наличие master_id
    if (!isset($_GET['master_id']) || empty($_GET['master_id'])) {
        $response['message'] = 'ID мастера не указан.';
        echo json_encode($response);
        exit;
    }

    $master_id = intval($_GET['master_id']);

    // Проверяем существование мастера
    $stmt = $pdo->prepare("SELECT id_masters FROM Masters WHERE id_masters = ?");
    $stmt->execute([$master_id]);
    if (!$stmt->fetch()) {
        $response['message'] = 'Мастер с указанным ID не найден.';
        echo json_encode($response);
        exit;
    }

    // Получаем список услуг с доступностью для мастера
    $query = "SELECT s.id_service, s.name, ms.price, ms.duration, ms.is_available 
              FROM Services s
              JOIN MasterServices ms ON s.id_service = ms.service_id
              WHERE ms.master_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$master_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Возвращаем данные в формате JSON
    $response['success'] = true;
    $response['services'] = $services;
} catch (Exception $e) {
    error_log('Ошибка в get_services_by_master.php: ' . $e->getMessage());
    $response['message'] = 'Ошибка на сервере.';
}

echo json_encode($response);
?>