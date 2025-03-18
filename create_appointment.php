<?php
include 'config.php';
header('Content-Type: application/json');

$master_id = $_POST['master_id'];
$service_id = $_POST['service_id'];
$fio = $_POST['fio'];
$phone = $_POST['phone'];
$date = $_POST['date'];
$time = $_POST['time'];

if (empty($master_id) || empty($service_id) || empty($fio) || empty($phone) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Проверка клиента
    $stmt_check_client = $pdo->prepare("SELECT id FROM Clients WHERE phone = :phone");
    $stmt_check_client->bindParam(':phone', $phone);
    $stmt_check_client->execute();
    $client = $stmt_check_client->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        $stmt_insert_client = $pdo->prepare("INSERT INTO Clients (full_name, phone) VALUES (:fio, :phone)");
        $stmt_insert_client->bindParam(':fio', $fio);
        $stmt_insert_client->bindParam(':phone', $phone);
        $stmt_insert_client->execute();
        $client_id = $pdo->lastInsertId();
    } else {
        $client_id = $client['id'];
    }

    // Вставка записи
    $sql = "INSERT INTO Appointments (master_id, client_id, service_id, date_time, price, duration)
            SELECT :master_id, :client_id, :service_id, :date_time, ms.price, ms.duration
            FROM MasterServices ms
            WHERE ms.master_id = :master_id AND ms.service_id = :service_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':master_id', $master_id);
    $stmt->bindValue(':client_id', $client_id);
    $stmt->bindValue(':service_id', $service_id);
    $stmt->bindValue(':date_time', "$date $time");
    $stmt->execute();

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>