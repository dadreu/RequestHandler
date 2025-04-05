<?php
include 'config.php';
header('Content-Type: application/json');

$master_id = $_POST['master_id'] ?? null;
$service_name = $_POST['service_name'] ?? null;
$duration = $_POST['duration'] ?? null;
$price = $_POST['price'] ?? null;

if (empty($master_id) || empty($service_name) || empty($duration) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения.']);
    exit;
}

// Приведение типов
$master_id = intval($master_id);
$duration = intval($duration);
$price = intval($price);

// Проверка на положительные значения
if ($duration < 1 || $price < 1) {
    echo json_encode(['success' => false, 'message' => 'Длительность и стоимость должны быть положительными числами.']);
    exit;
}

// Корректировка длительности вверх до ближайшего значения, кратного 15
$corrected_duration = ceil($duration / 15) * 15;
if ($corrected_duration < 15) {
    $corrected_duration = 15; // Минимальное значение
}

// Корректировка стоимости вверх до ближайшего значения, кратного 100
$corrected_price = ceil($price / 100) * 100;
if ($corrected_price < 100) {
    $corrected_price = 100; // Минимальное значение
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

    // Привязываем услугу к мастеру с округлёнными значениями
    $stmt = $pdo->prepare("INSERT INTO MasterServices (master_id, service_id, price, duration, is_available) 
                           VALUES (:master_id, :service_id, :price, :duration, 1)");
    $stmt->bindParam(':master_id', $master_id);
    $stmt->bindParam(':service_id', $service_id);
    $stmt->bindParam(':price', $corrected_price);
    $stmt->bindParam(':duration', $corrected_duration);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка БД: ' . $e->getMessage()]);
}
?>