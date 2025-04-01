<?php
include 'config.php';
header('Content-Type: application/json');

// Function to normalize phone numbers
function normalizePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric characters
    if (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1); // Replace leading 8 with 7
    }
    return $phone;
}

// Retrieve POST data
$master_id = $_POST['master_id'];
$service_id = $_POST['service_id'];
$fio = $_POST['fio'];
$phone = normalizePhone($_POST['phone']);
$date = $_POST['date'];
$time = $_POST['time'];

// Check for empty fields
if (empty($master_id) || empty($service_id) || empty($fio) || empty($phone) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Check if client exists by phone
    $stmt_check_client = $pdo->prepare("SELECT id, full_name, telegram_id FROM Clients WHERE phone = :phone");
    $stmt_check_client->bindParam(':phone', $phone);
    $stmt_check_client->execute();
    $client = $stmt_check_client->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        // Insert new client if not found
        $stmt_insert_client = $pdo->prepare("INSERT INTO Clients (full_name, phone, telegram_id) VALUES (:fio, :phone, NULL)");
        $stmt_insert_client->bindParam(':fio', $fio);
        $stmt_insert_client->bindParam(':phone', $phone);
        $stmt_insert_client->execute();
        $client_id = $pdo->lastInsertId();
    } else {
        $client_id = $client['id'];
        // Update full_name if it’s null or different
        if ($client['full_name'] === null || $client['full_name'] !== $fio) {
            $stmt_update_client = $pdo->prepare("UPDATE Clients SET full_name = :fio WHERE id = :client_id");
            $stmt_update_client->bindParam(':fio', $fio);
            $stmt_update_client->bindParam(':client_id', $client_id);
            $stmt_update_client->execute();
        }
    }

    // Insert appointment with corrected query
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