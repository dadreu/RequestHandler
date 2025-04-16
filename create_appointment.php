<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=UTF-8');

/**
 * Создаёт новую запись на услугу в салоне.
 */
try {
    // Проверка авторизации
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['master', 'client'])) {
        throw new Exception('Требуется авторизация');
    }

    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    // Проверка salon_id
    if (!isset($_SESSION['salon_id'])) {
        throw new Exception('Салон не определён. Пожалуйста, перезапустите приложение');
    }

    $role = $_SESSION['role'];
    $user_id = (int)$_SESSION['user_id'];
    $salon_id = (int)$_SESSION['salon_id'];
    $master_id = $role === 'master' ? $user_id : (int)($_POST['master_id'] ?? 0);
    $client_id = $role === 'client' ? $user_id : null;
    $service_id = (int)($_POST['service_id'] ?? 0);
    $fio = trim(urldecode($_POST['fio'] ?? ''));
    $phone = normalizePhone(urldecode($_POST['phone'] ?? ''));
    $date = $_POST['date'] ?? '';
    $time = urldecode($_POST['time'] ?? '');

    // Валидация данных
    if (!$master_id || !$service_id || !$fio || !$phone || !$date || !$time) {
        throw new Exception('Все поля обязательны для заполнения');
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
        throw new Exception('Неверный формат даты или времени');
    }

    if (strlen($phone) !== 11 || $phone[0] !== '7') {
        throw new Exception('Неверный формат номера телефона');
    }

    $date_time = new DateTime("$date $time", new DateTimeZone('Asia/Yekaterinburg'));
    $now = new DateTime('now', new DateTimeZone('Asia/Yekaterinburg'));
    if ($date_time < $now) {
        throw new Exception('Нельзя записаться на прошедшее время');
    }

    $pdo->beginTransaction();

    // Проверка принадлежности мастера и услуги к салону
    $stmt = $pdo->prepare(
        "SELECT m.salon_id AS master_salon, s.salon_id AS service_salon
         FROM Masters m
         JOIN MasterServices ms ON m.id_masters = ms.master_id
         JOIN Services s ON ms.service_id = s.id_service
         WHERE m.id_masters = :master_id AND s.id_service = :service_id"
    );
    $stmt->execute(['master_id' => $master_id, 'service_id' => $service_id]);
    $salons = $stmt->fetch();

    if (!$salons || $salons['master_salon'] != $salon_id || $salons['service_salon'] != $salon_id) {
        throw new Exception('Мастер или услуга не принадлежат салону');
    }

    // Обработка клиента
    if ($role === 'master') {
        $client_id = upsertClient($pdo, $fio, $phone);
    } else {
        $stmt = $pdo->prepare(
            "SELECT id_clients 
             FROM Clients 
             WHERE id_clients = :client_id AND phone = :phone"
        );
        $stmt->execute(['client_id' => $client_id, 'phone' => $phone]);
        if (!$stmt->fetch()) {
            throw new Exception('Клиент не найден или номер телефона не соответствует');
        }
    }

    // Проверка услуги
    $stmt = $pdo->prepare(
        "SELECT id_master_service, duration 
         FROM MasterServices 
         WHERE master_id = :master_id AND service_id = :service_id AND is_available = 1"
    );
    $stmt->execute(['master_id' => $master_id, 'service_id' => $service_id]);
    $service = $stmt->fetch();

    if (!$service) {
        throw new Exception('Услуга недоступна или не существует');
    }

    $id_master_service = $service['id_master_service'];
    $duration = $service['duration'];

    // Проверка пересечений
    if (hasOverlap($pdo, $master_id, $date, $time, $duration)) {
        throw new Exception('Выбранное время уже занято');
    }

    // Создание записи
    $stmt = $pdo->prepare(
        "INSERT INTO Appointments (id_master_service, client_id, date_time) 
         VALUES (:id_master_service, :client_id, :date_time)"
    );
    $stmt->execute([
        'id_master_service' => $id_master_service,
        'client_id' => $client_id,
        'date_time' => $date_time->format('Y-m-d H:i:s')
    ]);

    logAction($pdo, $user_id, $role, "Создал запись для клиента с ID $client_id в салоне $salon_id");
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Ошибка в create_appointment.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Нормализует номер телефона.
 * @param string $phone Номер телефона
 * @return string Нормализованный номер
 */
function normalizePhone(string $phone): string {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) === 10 ? '7' . $phone : (strlen($phone) === 11 && $phone[0] === '8' ? '7' . substr($phone, 1) : $phone);
}

/**
 * Создаёт или обновляет клиента.
 * @param PDO $pdo Подключение к базе данных
 * @param string $fio ФИО клиента
 * @param string $phone Номер телефона
 * @return int ID клиента
 */
function upsertClient(PDO $pdo, string $fio, string $phone): int {
    $stmt = $pdo->prepare(
        "SELECT id_clients, full_name 
         FROM Clients 
         WHERE phone = :phone"
    );
    $stmt->execute(['phone' => $phone]);
    $client = $stmt->fetch();

    if ($client) {
        if ($client['full_name'] !== $fio) {
            $stmt = $pdo->prepare(
                "UPDATE Clients 
                 SET full_name = :fio 
                 WHERE id_clients = :client_id"
            );
            $stmt->execute(['fio' => $fio, 'client_id' => $client['id_clients']]);
        }
        return $client['id_clients'];
    }

    $stmt = $pdo->prepare(
        "INSERT INTO Clients (full_name, phone) 
         VALUES (:fio, :phone)"
    );
    $stmt->execute(['fio' => $fio, 'phone' => $phone]);
    return $pdo->lastInsertId();
}

/**
 * Проверяет пересечение времени с другими записями.
 * @param PDO $pdo Подключение к базе данных
 * @param int $master_id ID мастера
 * @param string $date Дата
 * @param string $time Время
 * @param int $duration Длительность
 * @return bool Есть ли пересечение
 */
function hasOverlap(PDO $pdo, int $master_id, string $date, string $time, int $duration): bool {
    $stmt = $pdo->prepare(
        "SELECT a.id_appointment
         FROM Appointments a
         JOIN MasterServices ms ON a.id_master_service = ms.id_master_service
         WHERE ms.master_id = :master_id
         AND DATE(a.date_time) = :date
         AND (
             (TIME(a.date_time) <= :time AND ADDTIME(TIME(a.date_time), SEC_TO_TIME(ms.duration * 60)) > :time)
             OR
             (TIME(a.date_time) >= :time AND TIME(a.date_time) < ADDTIME(:time, SEC_TO_TIME(:duration * 60)))
         )"
    );
    $stmt->execute([
        'master_id' => $master_id,
        'date' => $date,
        'time' => $time,
        'duration' => $duration
    ]);
    return (bool)$stmt->fetch();
}

/**
 * Логирует действие.
 * @param PDO $pdo Подключение к базе данных
 * @param int $user_id ID пользователя
 * @param string $role Роль
 * @param string $action Действие
 */
function logAction(PDO $pdo, int $user_id, string $role, string $action): void {
    $stmt = $pdo->prepare(
        "INSERT INTO Logs (user_id, role, action, timestamp) 
         VALUES (:user_id, :role, :action, NOW())"
    );
    $stmt->execute(['user_id' => $user_id, 'role' => $role, 'action' => $action]);
}
?>