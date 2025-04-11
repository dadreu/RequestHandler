<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'master') {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация мастера']);
    exit;
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Неверный CSRF-токен']);
    exit;
}

$master_id = $_SESSION['user_id'];
$service_name = $_POST['service_name'] ?? null;
$duration = $_POST['duration'] ?? null;
$price = $_POST['price'] ?? null;

if (empty($service_name) || empty($duration) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения.']);
    exit;
}

$duration = intval($duration);
$price = intval($price);

if ($duration < 0 || $price < 0) {
    echo json_encode(['success' => false, 'message' => 'Длительность и стоимость должны быть неотрицательными числами.']);
    exit;
}

$corrected_duration = ceil($duration / 15) * 15;
if ($corrected_duration < 15) {
    $corrected_duration = 15;
}

function adjustPrice($price) {
    $price = intval($price);
    if ($price < 5) {
        return 5;
    }
    $mod10 = $price % 10;
    $base = floor($price / 10) * 10;
    if ($mod10 < 5) {
        return $base + 5;
    } elseif ($mod10 < 9) {
        return $base + 9;
    } else {
        return $base + 10;
    }
}

$corrected_price = adjustPrice($price);

try {
    $stmt = $pdo->prepare("SELECT id_service FROM Services WHERE name = :name");
    $stmt->bindParam(':name', $service_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $service_id = $result['id_service'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO Services (name) VALUES (:name)");
        $stmt->bindParam(':name', $service_name);
        $stmt->execute();
        $service_id = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare("INSERT INTO MasterServices (master_id, service_id, price, duration, is_available) 
                           VALUES (:master_id, :service_id, :price, :duration, 1)");
    $stmt->bindParam(':master_id', $master_id);
    $stmt->bindParam(':service_id', $service_id);
    $stmt->bindParam(':price', $corrected_price);
    $stmt->bindParam(':duration', $corrected_duration);
    $stmt->execute();

    $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Добавил услугу с ID $service_id"]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка БД: ' . $e->getMessage()]);
}
?>