<?php
include 'config.php';
header('Content-Type: application/json');

$response = ['success' => false];

// Получаем данные из тела запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['id'])) {
    $appointment_id = intval($data['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM Appointments WHERE id_appointment = ?");
        $stmt->execute([$appointment_id]);
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
        } else {
            $response['message'] = "Запись не найдена";
        }
    } catch (Exception $e) {
        $response['message'] = "Ошибка БД: " . $e->getMessage();
    }
} else {
    $response['message'] = "ID записи не указан";
}

echo json_encode($response);
?>