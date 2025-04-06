<?php
include 'config.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Отключаем вывод ошибок в ответ
ini_set('log_errors', 1); // Включаем логирование ошибок
ini_set('error_log', '/path/to/php_errors.log'); // Укажите путь к лог-файлу

$response = ['success' => false];

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data) || empty($data)) {
        $response['message'] = 'Данные не переданы или имеют неверный формат';
        echo json_encode($response);
        exit;
    }

    $updatedCount = 0;
    foreach ($data as $service) {
        $master_id = intval($service['master_id'] ?? 0);
        $service_id = intval($service['service_id'] ?? 0);
        $available = intval($service['available'] ?? 0);

        if ($master_id === 0 || $service_id === 0) {
            $response['message'] = 'Неверные master_id или service_id';
            $response['debug'] = $service;
            echo json_encode($response);
            exit;
        }

        $query = "UPDATE MasterServices SET is_available = ? WHERE master_id = ? AND service_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$available, $master_id, $service_id]);
        $updatedCount += $stmt->rowCount();
    }

    if ($updatedCount > 0) {
        $response['success'] = true;
        $response['updated'] = $updatedCount;
    } else {
        $response['message'] = 'Ни одна запись не обновлена';
        $response['debug'] = $data;
    }
} catch (Exception $e) {
    $response['message'] = 'Ошибка: ' . $e->getMessage();
    $response['debug'] = isset($data) ? $data : 'Данные не получены';
}

echo json_encode($response);
?>