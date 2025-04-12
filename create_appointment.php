<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['master', 'client'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit;
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Неверный CSRF-токен']);
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$master_id = ($role === 'master') ? $user_id : ($_POST['master_id'] ?? null);
$client_id = ($role === 'client') ? $user_id : null;
$service_id = $_POST['service_id'] ?? null;
$fio = isset($_POST['fio']) ? urldecode($_POST['fio']) : null; // Декодируем ФИО
$phone = isset($_POST['phone']) ? normalizePhone($_POST['phone']) : null;
$date = $_POST['date'] ?? null;
$time = isset($_POST['time']) ? urldecode($_POST['time']) : null; // Декодируем время

// Проверка всех обязательных полей
if (empty($master_id) || empty($service_id) || empty($fio) || empty($phone) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения.']);
    exit;
}

// Проверка формата номера телефона
if (strlen($phone) !== 11 || $phone[0] !== '7') {
    echo json_encode(['success' => false, 'message' => 'Неверный формат номера телефона.']);
    exit;
}

// Проверка корректности формата времени
if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
    echo json_encode(['success' => false, 'message' => 'Неверный формат времени.']);
    exit;
}

// Проверка корректности формата даты
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Неверный формат даты.']);
    exit;
}

try {
    $selectedDateTime = new DateTime("$date $time", new DateTimeZone('UTC'));
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $now->setTimezone(new DateTimeZone('Asia/Yekaterinburg'));
    $selectedDateTime->setTimezone(new DateTimeZone('Asia/Yekaterinburg'));

    if ($selectedDateTime < $now) {
        echo json_encode(['success' => false, 'message' => 'Нельзя записаться на прошедшую дату или время.']);
        exit;
    }

    $pdo->beginTransaction();

    if ($role === 'master') {
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
            if ($client['full_name'] === null || $client['full_name'] !== $fio) {
                $stmt_update_client = $pdo->prepare("UPDATE Clients SET full_name = :fio WHERE id_clients = :client_id");
                $stmt_update_client->bindParam(':fio', $fio);
                $stmt_update_client->bindParam(':client_id', $client_id);
                $stmt_update_client->execute();
            }
        }
    } elseif ($role === 'client') {
        // Проверяем, что client_id соответствует сессии
        $stmt_check_client = $pdo->prepare("SELECT id_clients FROM Clients WHERE id_clients = :client_id AND phone = :phone");
        $stmt_check_client->bindParam(':client_id', $client_id);
        $stmt_check_client->bindParam(':phone', $phone);
        $stmt_check_client->execute();
        if (!$stmt_check_client->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Клиент не найден или номер телефона не соответствует.']);
            exit;
        }
    }

    // Проверка доступности услуги
    $stmt_check_service = $pdo->prepare("SELECT id_master_service FROM MasterServices WHERE master_id = ? AND service_id = ? AND is_available = 1");
    $stmt_check_service->execute([$master_id, $service_id]);
    if (!$stmt_check_service->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Услуга недоступна или не существует.']);
        exit;
    }

    // Проверка пересечения с другими записями
    $stmt_check_overlap = $pdo->prepare("
        SELECT a.id_appointment
        FROM Appointments a
        JOIN MasterServices ms ON a.master_id = ms.master_id AND a.service_id = ms.service_id
        WHERE a.master_id = :master_id
        AND DATE(a.date_time) = :date
        AND (
            (TIME(a.date_time) <= :time AND ADDTIME(TIME(a.date_time), SEC_TO_TIME(ms.duration * 60)) > :time)
            OR
            (TIME(a.date_time) >= :time AND TIME(a.date_time) < ADDTIME(:time, SEC_TO_TIME(:duration * 60)))
        )
    ");
    $stmt_duration = $pdo->prepare("SELECT duration FROM MasterServices WHERE master_id = ? AND service_id = ?");
    $stmt_duration->execute([$master_id, $service_id]);
    $duration = $stmt_duration->fetchColumn();

    $stmt_check_overlap->bindParam(':master_id', $master_id);
    $stmt_check_overlap->bindParam(':date', $date);
    $stmt_check_overlap->bindParam(':time', $time);
    $stmt_check_overlap->bindParam(':duration', $duration, PDO::PARAM_INT);
    $stmt_check_overlap->execute();

    if ($stmt_check_overlap->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Выбранное время уже занято.']);
        exit;
    }

    $sql = "INSERT INTO Appointments (master_id, client_id, service_id, date_time)
            VALUES (:master_id, :client_id, :service_id, :date_time)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':master_id', $master_id);
    $stmt->bindValue(':client_id', $client_id);
    $stmt->bindValue(':service_id', $service_id);
    $stmt->bindValue(':date_time', "$date $time");
    $stmt->execute();

    $stmt_log = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt_log->execute([$_SESSION['user_id'], $_SESSION['role'], "Создал запись для клиента с ID $client_id"]);

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