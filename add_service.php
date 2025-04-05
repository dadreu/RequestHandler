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

// Проверка на неотрицательные значения
if ($duration < 0 || $price < 0) {
    echo json_encode(['success' => false, 'message' => 'Длительность и стоимость должны быть неотрицательными числами.']);
    exit;
}

// Корректировка длительности вверх до ближайшего значения, кратного 15
$corrected_duration = ceil($duration / 15) * 15;
if ($corrected_duration < 15) {
    $corrected_duration = 15; // Минимальное значение
}

// Корректировка стоимости вверх до ближайшего значения, кратного 5 или заканчивающегося на 9
function adjustPrice($price) {
    $price = intval($price);
    if ($price < 5) {
        return 5; // Минимальное значение
    }

    $mod10 = $price % 10; // Последняя цифра
    $base = floor($price / 10) * 10; // Округляем вниз до десятков

    if ($mod10 < 5) {
        return $base + 5; // Округляем до 5
    } elseif ($mod10 < 9) {
        return $base + 9; // Округляем до 9
    } else {
        return $base + 10; // Округляем до следующего кратного 10
    }
}

$corrected_price = adjustPrice($price);

try {
    // Проверяем, существует ли услуга
    $stmt = $pdo->prepare("SELECT id_service FROM Services WHERE name = :name");
    $stmt->bindParam(':name', $service_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $service_id = $result['id_service'];
    } else {
        // Создаём новую услугу без описания
        $stmt = $pdo->prepare("INSERT INTO Services (name) VALUES (:name)");
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