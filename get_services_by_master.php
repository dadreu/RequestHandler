<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/error.log');

$response = ['success' => false];

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'master') {
    $response['message'] = 'Не авторизован или недостаточно прав.';
    echo json_encode($response);
    exit;
}

$master_id = intval($_SESSION['user_id']);

try {
    $stmt = $pdo->prepare("SELECT id_masters FROM Masters WHERE id_masters = ?");
    $stmt->execute([$master_id]);
    if (!$stmt->fetch()) {
        $response['message'] = 'Мастер не найден.';
        echo json_encode($response);
        exit;
    }

    $query = "SELECT s.id_service, s.name, ms.price, ms.duration, ms.is_available 
              FROM Services s
              JOIN MasterServices ms ON s.id_service = ms.service_id
              WHERE ms.master_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$master_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['services'] = $services;
} catch (Exception $e) {
    error_log('Ошибка в get_services_by_master.php: ' . $e->getMessage());
    $response['message'] = 'Ошибка на сервере.';
}

echo json_encode($response);
?>