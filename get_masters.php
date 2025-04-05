<?php
include 'config.php';

header('Content-Type: application/json; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["masters" => []];

try {
    // Загружаем список мастеров
    $stmt_masters = $pdo->query("SELECT id_master, full_name FROM Masters");
    $response["masters"] = $stmt_masters->fetchAll(PDO::FETCH_ASSOC);

    // Возвращаем ответ в формате JSON
    echo json_encode($response);

} catch (PDOException $e) {
    // Обработка ошибок
    echo json_encode(["error" => $e->getMessage()]);
}
?>