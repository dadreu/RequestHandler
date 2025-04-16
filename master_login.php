<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Неверный CSRF-токен');
    }

    $phone = normalizePhone($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$phone || !$password) {
        throw new Exception('Не указан номер телефона или пароль');
    }

    $stmt = $pdo->prepare(
        "SELECT id_masters, password, salon_id 
         FROM Masters 
         WHERE phone = :phone"
    );
    $stmt->execute(['phone' => $phone]);
    $master = $stmt->fetch();

    if (!$master || !password_verify($password, $master['password'])) {
        throw new Exception('Неверный номер телефона или пароль');
    }

    $_SESSION['user_id'] = $master['id_masters'];
    $_SESSION['role'] = 'master';
    $_SESSION['salon_id'] = $master['salon_id'];

    echo json_encode(['success' => true, 'master_id' => $master['id_masters']]);
} catch (Exception $e) {
    error_log("Ошибка в master_login.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function normalizePhone(string $phone): string {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) === 10 ? '7' . $phone : (strlen($phone) === 11 && $phone[0] === '8' ? '7' . substr($phone, 1) : $phone);
}
?>