<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация клиента']);
    exit;
}

$client_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT phone FROM Clients WHERE id_clients = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        echo json_encode(['success' => true, 'phone' => $client['phone']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Клиент не найден']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>