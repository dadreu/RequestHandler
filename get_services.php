<?php
include 'config.php';

header('Content-Type: application/json; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["services" => []];

try {
    // Проверяем, передан ли параметр master_id
    if (isset($_GET['master_id'])) {
        $masterId = intval($_GET['master_id']);

        // Загружаем услуги для выбранного мастера
        $stmt_services = $pdo->prepare("
            SELECT s.id, s.name 
            FROM Services s
            JOIN MasterServices ms ON s.id = ms.service_id
            WHERE ms.master_id = ? 
            AND ms.is_available = 1
            LIMIT 50
        ");
        $stmt_services->execute([$masterId]); // передаем параметр как массив
        $response["services"] = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Если master_id не передан, возвращаем ошибку
        $response["error"] = "master_id is required";
    }

    // Возвращаем ответ в формате JSON
    echo json_encode($response);

} catch (PDOException $e) {
    // Обработка ошибок
    echo json_encode(["error" => $e->getMessage()]);
}
?>
