<?php
include 'config.php';

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? '';
$response = ['role' => null];

$stmt = $pdo->prepare("SELECT id FROM Masters WHERE phone = :phone");
$stmt->execute(['phone' => $phone]);
$master = $stmt->fetch(PDO::FETCH_ASSOC);

if ($master) {
    $response['role'] = 'master';
} else {
    $stmt = $pdo->prepare("SELECT id FROM Clients WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($client) {
        $response['role'] = 'client';
    }
}

echo json_encode($response);
?>