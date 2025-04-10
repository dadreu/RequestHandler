<?php
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false];

// Получаем данные из тела запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['id'])) {
    $service_id = intval($data['id']);
    try {
        // Проверяем, используется ли услуга в записях
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Appointments WHERE service_id = ?");
        $stmt->execute([$service_id]);
        if ($stmt->fetchColumn() > 0) {
            $response['message'] = "Нельзя удалить услугу, так как она используется в записях";
            echo json_encode($response);
            exit;
        }

        // Удаляем связи из MasterServices
        $stmt = $pdo->prepare("DELETE FROM MasterServices WHERE service_id = ?");
        $stmt->execute([$service_id]);

        // Удаляем услугу из Services
        $stmt = $pdo->prepare("DELETE FROM Services WHERE id = ?");
        $stmt->execute([$service_id]);

        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
        } else {
            $response['message'] = "Услуга не найдена";
        }
    } catch (Exception $e) {
        $response['message'] = "Ошибка БД: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID услуги не указан";
}

echo json_encode($response);
?>