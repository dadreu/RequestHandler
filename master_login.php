<?php
// Запускаем сессию только если она ещё не активна
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';
header('Content-Type: application/json');

// Настройка логирования ошибок
ini_set('display_errors', 0); // Отключаем вывод ошибок в ответ
ini_set('log_errors', 1); // Включаем логирование
ini_set('error_log', '/var/www/html/error.log'); // Укажите путь к файлу логов

try {
    // Проверка CSRF-токена
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Неверный CSRF-токен']);
        exit;
    }

    // Проверка входных данных
    if (!isset($_POST['phone']) || !isset($_POST['password'])) {
        echo json_encode(['success' => false, 'message' => 'Не указан номер телефона или пароль']);
        exit;
    }

    $phone = normalizePhone($_POST['phone']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id_masters, password FROM Masters WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $master = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$master) {
        echo json_encode(['success' => false, 'message' => 'Номер телефона не зарегистрирован']);
        exit;
    }

    if (!password_verify($password, $master['password'])) {
        echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
        exit;
    }

    $_SESSION['user_id'] = $master['id_masters'];
    $_SESSION['role'] = 'master';
    echo json_encode(['success' => true, 'master_id' => $master['id_masters']]);
} catch (Exception $e) {
    error_log('Ошибка в master_login.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    exit;
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