<?php
header('Content-Type: application/json');

include 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Получаем данные из запроса
$master_id = $_POST['master_id'] ?? null;
$service_name = $_POST['service_name'] ?? null;
$duration = $_POST['duration'] ?? null;
$price = $_POST['price'] ?? null;

// Проверяем, что все поля заполнены
if (!$master_id || !$service_name || !$duration || !$price) {
    echo json_encode(["success" => false, "message" => "Все поля обязательны для заполнения."]);
    exit;
}

// Проверяем, существует ли услуга в таблице Services
$sql = "SELECT id FROM Services WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $service_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Услуга уже существует, берём её ID
    $service_id = $result->fetch_assoc()['id'];
} else {
    // Создаём новую услугу
    $sql = "INSERT INTO Services (name, description) VALUES (?, '')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $service_name);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Ошибка при добавлении услуги: " . $stmt->error]);
        exit;
    }
    $service_id = $stmt->insert_id;
}

// Привязываем услугу к мастеру в таблице MasterServices
$sql = "INSERT INTO MasterServices (master_id, service_id, price, duration, is_available) VALUES (?, ?, ?, ?, 1)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iidi", $master_id, $service_id, $price, $duration);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
    exit;
} else {
    echo json_encode(["success" => false, "message" => "Ошибка при привязке услуги к мастеру: " . $stmt->error]);
    exit;
}

$conn->close();
?>