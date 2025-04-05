<?php
include 'config.php';
header('Content-Type: application/json');

// Функция нормализации номера телефона
function normalizePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone); // Удаляем все нечисловые символы
    if (strlen($phone) == 11) {
        if ($phone[0] == '8') {
            return '7' . substr($phone, 1); // Заменяем 8 на 7
        } elseif ($phone[0] == '7') {
            return $phone; // Уже в нужном формате
        }
    } elseif (strlen($phone) == 10) {
        return '7' . $phone; // Добавляем 7 к 10 цифрам
    } elseif (strlen($phone) > 11 && substr($phone, 0, 1) == '7') {
        return substr($phone, 0, 11); // Обрезаем лишние символы
    }
    return $phone; // Возвращаем исходный номер, если нормализация невозможна
}

// Получение данных из POST-запроса
$master_id = $_POST['master_id'] ?? null;
$service_id = $_POST['service_id'] ?? null;
$fio = $_POST['fio'] ?? null;
$phone = isset($_POST['phone']) ? normalizePhone($_POST['phone']) : null;
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;

// Проверка на наличие всех обязательных полей
if (empty($master_id) || empty($service_id) || empty($fio) || empty($phone) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения.']);
    exit;
}

// Дополнительная валидация номера телефона
if (strlen($phone) !== 11 || $phone[0] !== '7') {
    echo json_encode(['success' => false, 'message' => 'Неверный формат номера телефона. Ожидается 11 цифр, начинающихся с 7.']);
    exit;
}

// Проверка, что дата не в прошлом
$selectedDateTime = new DateTime("$date $time", new DateTimeZone('UTC'));
$now = new DateTime('now', new DateTimeZone('UTC'));
$now->setTimezone(new DateTimeZone('Asia/Yekaterinburg')); // Пермское время (UTC+5)
$selectedDateTime->setTimezone(new DateTimeZone('Asia/Yekaterinburg'));

if ($selectedDateTime < $now) {
    echo json_encode(['success' => false, 'message' => 'Нельзя записаться на прошедшую дату или время.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Проверка существования клиента по номеру телефона
    $stmt_check_client = $pdo->prepare("SELECT id_clients, full_name, telegram_id FROM Clients WHERE phone = :phone");
    $stmt_check_client->bindParam(':phone', $phone);
    $stmt_check_client->execute();
    $client = $stmt_check_client->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        // Добавление нового клиента, если не найден
        $stmt_insert_client = $pdo->prepare("INSERT INTO Clients (full_name, phone, telegram_id) VALUES (:fio, :phone, NULL)");
        $stmt_insert_client->bindParam(':fio', $fio);
        $stmt_insert_client->bindParam(':phone', $phone);
        $stmt_insert_client->execute();
        $client_id = $pdo->lastInsertId();
    } else {
        $client_id = $client['id_clients'];
        // Обновление ФИО, если оно отсутствует или отличается
        if ($client['full_name'] === null || $client['full_name'] !== $fio) {
            $stmt_update_client = $pdo->prepare("UPDATE Clients SET full_name = :fio WHERE id_clients = :client_id");
            $stmt_update_client->bindParam(':fio', $fio);
            $stmt_update_client->bindParam(':client_id', $client_id);
            $stmt_update_client->execute();
        }
    }

    // Вставка записи на приём
    $sql = "INSERT INTO Appointments (master_id, client_id, service_id, date_time)
            VALUES (:master_id, :client_id, :service_id, :date_time)";
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
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
}
?>