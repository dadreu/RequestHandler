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

$appointment_id = $_POST['appointment_id'] ?? null;
$master_id = $_SESSION['user_id'];
$service_id = $_POST['service_id'] ?? null;
$fio = $_POST['fio'] ?? null;
$phone = isset($_POST['phone']) ? normalizePhone($_POST['phone']) : null;
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;

if (empty($appointment_id) || empty($service_id) || empty($fio) || empty($phone) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения']);
    exit;
}

if (strlen($phone) !== 11 || $phone[0] !== '7') {
    echo json_encode(['success' => false, 'message' => 'Неверный формат номера телефона']);
    exit;
}

$selectedDateTime = new DateTime("$date $time", new DateTimeZone('UTC'));
$now = new DateTime('now', new DateTimeZone('UTC'));
$now->setTimezone(new DateTimeZone('Asia/Yekaterinburg'));
$selectedDateTime->setTimezone(new DateTimeZone('Asia/Yekaterinburg'));

if ($selectedDateTime < $now) {
    echo json_encode(['success' => false, 'message' => 'Нельзя изменить запись на прошедшую дату']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt_check = $pdo->prepare("
        SELECT ms.master_id
        FROM Appointments a
        JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
        WHERE a.id_appointment = ?
    ");
    $stmt_check->execute([$appointment_id]);
    if ($stmt_check->fetchColumn() != $master_id) {
        echo json_encode(['success' => false, 'message' => 'Нет прав для изменения этой записи']);
        exit;
    }

    $stmt_check_client = $pdo->prepare("SELECT id_clients, full_name FROM Clients WHERE phone = :phone");
    $stmt_check_client->bindParam(':phone', $phone);
    $stmt_check_client->execute();
    $client = $stmt_check_client->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        $stmt_insert_client = $pdo->prepare("INSERT INTO Clients (full_name, phone, telegram_id) VALUES (:fio, :phone, NULL)");
        $stmt_insert_client->bindParam(':fio', $fio);
        $stmt_insert_client->bindParam(':phone', $phone);
        $stmt_insert_client->execute();
        $client_id = $pdo->lastInsertId();
    } else {
        $client_id = $client['id_clients'];
        if ($client['full_name'] !== $fio) {
            $stmt_update_client = $pdo->prepare("UPDATE Clients SET full_name = :fio WHERE id_clients = :client_id");
            $stmt_update_client->bindParam(':fio', $fio);
            $stmt_update_client->bindParam(':client_id', $client_id);
            $stmt_update_client->execute();
        }
    }

    $stmt_ms = $pdo->prepare("SELECT id_master_service FROM MasterServices WHERE master_id = ? AND service_id = ?");
    $stmt_ms->execute([$master_id, $service_id]);
    $id_master_service = $stmt_ms->fetchColumn();
    if (!$id_master_service) {
        echo json_encode(['success' => false, 'message' => 'Услуга не найдена для данного мастера']);
        exit;
    }

    $sql = "UPDATE Appointments SET id_master_service = :id_master_service, client_id = :client_id, date_time = :date_time WHERE id_appointment = :appointment_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_master_service', $id_master_service);
    $stmt->bindValue(':client_id', $client_id);
    $stmt->bindValue(':date_time', "$date $time");
    $stmt->bindValue(':appointment_id', $appointment_id);
    $stmt->execute();

    $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Обновил запись с ID $appointment_id"]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
}

function normalizePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) == 10) {
        $phone = '7' . $phone;
    } elseif (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1);
    }
    return $phone;
}
?>