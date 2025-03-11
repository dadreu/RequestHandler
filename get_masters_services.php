<?php
include 'config.php';

header('Content-Type: application/json');

try {
    $masters = $pdo->query("SELECT id, full_name, phone FROM Masters")->fetchAll(PDO::FETCH_ASSOC);
    $services = $pdo->query("SELECT id, name, price, duration, description FROM Services WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['masters' => $masters, 'services' => $services]);
} catch (PDOException $e) {
    die(json_encode(["error" => "Ошибка запроса: " . $e->getMessage()]));
}
?>
