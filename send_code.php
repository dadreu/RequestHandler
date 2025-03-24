<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';
$code = rand(1000, 9999);
$_SESSION['reg_code'] = $code;
$_SESSION['reg_phone'] = $phone;

// Здесь должна быть логика отправки кода (например, через Telegram)
echo json_encode(['success' => true]);
?>