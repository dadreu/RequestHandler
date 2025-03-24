<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';
$inputCode = $_POST['code'] ?? '';
$savedCode = $_SESSION['reg_code'] ?? '';
$savedPhone = $_SESSION['reg_phone'] ?? '';

$response = ['success' => false];

if ($phone === $savedPhone && $inputCode == $savedCode) {
    $response['success'] = true;
}

echo json_encode($response);
?>