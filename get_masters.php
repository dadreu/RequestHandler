<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Возвращает список мастеров для салона из сессии.
 */
try {
    // Проверка наличия salon_id в сессии
    if (!isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён. Пожалуйста, перезапустите приложение');
    }

    $salon_id = (int)$_SESSION['salon_id'];

    // Проверка существования салона
    $stmt = $pdo->prepare("SELECT id_salon FROM Salons WHERE id_salon = :salon_id");
    $stmt->execute(['salon_id' => $salon_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Салон не найден');
    }

    // Получение мастеров
    $stmt = $pdo->prepare(
        "SELECT id_masters, full_name 
         FROM Masters 
         WHERE salon_id = :salon_id 
         ORDER BY full_name"
    );
    $stmt->execute(['salon_id' => $salon_id]);
    $masters = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'masters' => $masters
    ]);
} catch (Exception $e) {
    error_log("Ошибка в get_masters.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>