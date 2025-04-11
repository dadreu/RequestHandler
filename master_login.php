<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Неверный CSRF-токен']);
    exit;
}

$phone = normalizePhone($_POST['phone']);
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT id_masters, password FROM Masters WHERE phone = :phone");
$stmt->execute(['phone' => $phone]);
$master = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$master) {
    echo json_encode(['success' => false, 'message' => 'Номер телефона не зарегистрирован']);
} elseif (password_verify($password, $master['password'])) { // Исправлено: убрано password_hash
    $_SESSION['user_id'] = $master['id_masters'];
    $_SESSION['role'] = 'master';
    echo json_encode(['success' => true, 'master_id' => $master['id_masters']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
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