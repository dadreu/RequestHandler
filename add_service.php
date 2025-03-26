<?php
include 'config.php';
header('Content-Type: application/json');

$master_id = $_POST['master_id'];
$service_name = $_POST['service_name'];
$duration = $_POST['duration'];
$price = $_POST['price'];

if (empty($master_id) || empty($service_name) || empty($duration) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения.']);
    exit;
}

try {
    // Проверяем, существует ли услуга
    $stmt = $pdo->prepare("SELECT id FROM Services WHERE name = :name");
    $stmt->bindParam(':name', $service_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $service_id = $result['id'];
    } else {
        // Создаём новую услугу
        $stmt = $pdo->prepare("INSERT INTO Services (name, description) VALUES (:name, '')");
        $stmt->bindParam(':name', $service_name);
        $stmt->execute();
        $service_id = $pdo->lastInsertId();
    }

    // Привязываем услугу к мастеру
    $stmt = $pdo->prepare("INSERT INTO MasterServices (master_id, service_id, price, duration, is_available) 
                           VALUES (:master_id, :service_id, :price, :duration, 1)");
    $stmt->bindParam(':master_id', $master_id);
    $stmt->bindParam(':service_id', $service_id);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':duration', $duration);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>