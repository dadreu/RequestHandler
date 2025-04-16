<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Проверка авторизации мастера
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'master') {
        throw new Exception('Требуется авторизация мастера');
    }

    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    // Получение данных из запроса
    $master_id = $_SESSION['user_id'];
    $service_name = $_POST['service_name'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $price = $_POST['price'] ?? '';

    // Валидация входных данных
    if (empty($service_name) || empty($duration) || empty($price)) {
        throw new Exception('Все поля обязательны для заполнения');
    }

    $duration = (int)$duration;
    $price = (int)$price;

    if ($duration < 0 || $price < 0) {
        throw new Exception('Длительность и стоимость должны быть неотрицательными');
    }

    // Корректировка длительности (кратно 15 минутам)
    $corrected_duration = max(15, ceil($duration / 15) * 15);

    // Корректировка цены (округление до 5 или 10)
    $corrected_price = adjustPrice($price);

    // Получение salon_id мастера
    $stmt = $pdo->prepare("SELECT salon_id FROM Masters WHERE id_masters = :master_id");
    $stmt->execute(['master_id' => $master_id]);
    $salon_id = $stmt->fetchColumn();

    if (!$salon_id) {
        throw new Exception('Салон мастера не найден');
    }

    // Проверка существующей услуги в салоне
    $stmt = $pdo->prepare("SELECT id_service FROM Services WHERE name = :name AND salon_id = :salon_id");
    $stmt->execute(['name' => $service_name, 'salon_id' => $salon_id]);
    $service_id = $stmt->fetchColumn();

    // Транзакция для добавления услуги
    $pdo->beginTransaction();

    if (!$service_id) {
        $stmt = $pdo->prepare("INSERT INTO Services (name, salon_id) VALUES (:name, :salon_id)");
        $stmt->execute(['name' => $service_name, 'salon_id' => $salon_id]);
        $service_id = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare(
        "INSERT INTO MasterServices (master_id, service_id, price, duration, is_available) 
         VALUES (:master_id, :service_id, :price, :duration, 1)"
    );
    $stmt->execute([
        'master_id' => $master_id,
        'service_id' => $service_id,
        'price' => $corrected_price,
        'duration' => $corrected_duration
    ]);

    // Логирование действия
    logAction($pdo, $_SESSION['user_id'], $_SESSION['role'], "Добавил услугу с ID $service_id");

    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Корректирует цену до ближайшего значения (5, 9 или 10).
 * @param int $price Исходная цена
 * @return int Скорректированная цена
 */
function adjustPrice(int $price): int {
    if ($price < 5) return 5;
    $base = floor($price / 10) * 10;
    $mod10 = $price % 10;
    return $base + ($mod10 < 5 ? 5 : ($mod10 < 9 ? 9 : 10));
}

/**
 * Записывает действие в лог.
 * @param PDO $pdo Подключение к базе данных
 * @param int $user_id ID пользователя
 * @param string $role Роль пользователя
 * @param string $action Действие
 */
function logAction(PDO $pdo, int $user_id, string $role, string $action): void {
    $stmt = $pdo->prepare("INSERT INTO Logs (user_id, role, action, timestamp) VALUES (:user_id, :role, :action, NOW())");
    $stmt->execute(['user_id' => $user_id, 'role' => $role, 'action' => $action]);
}
?>