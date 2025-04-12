<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

// Временно для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверка сессии
if (!session_id()) {
    echo json_encode(['success' => false, 'message' => 'Сессия не запущена']);
    exit;
}

// Проверка CSRF-токена
if (!isset($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'CSRF-токен не передан']);
    exit;
}
if (!isset($_SESSION['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'CSRF-токен не установлен в сессии']);
    exit;
}
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Неверный CSRF-токен']);
    exit;
}

// Нормализация телефона
function normalizePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) == 10) {
        $phone = '7' . $phone;
    } elseif (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1);
    }
    return $phone;
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
?>