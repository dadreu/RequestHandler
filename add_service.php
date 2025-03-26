<?php
header('Content-Type: application/json');

// Настройки подключения к базе данных
$servername = "requesthandler-dadreu.amvera.io";
$username = "root";
$password = "";
$dbname = "SalonDB";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Получаем данные из запроса
$master_id = $_POST['master_id'];
$service_name = $_POST['service_name'];
$duration = $_POST['duration'];
$price = $_POST['price'];

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
} else {
    echo json_encode(["success" => false, "message" => "Ошибка при привязке услуги к мастеру: " . $stmt->error]);
}

$conn->close();
?>