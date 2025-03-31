<?php
include 'config.php';
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['code'])) {
    $phone = $_POST['phone'];
    $code = $_POST['code'];

    $stmt = $pdo->prepare("SELECT * FROM ConfirmationCodes WHERE phone = :phone AND code = :code AND created_at > NOW() - INTERVAL 5 MINUTE");
    $stmt->execute(['phone' => $phone, 'code' => $code]);
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($confirmation) {
        $stmt = $pdo->prepare("SELECT id FROM Clients WHERE phone = :phone");
        $stmt->execute(['phone' => $phone]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            $response['success'] = true;
            $response['client_id'] = $client['id'];
        }
    } else {
        $response['message'] = 'Неверный код или код устарел';
    }
}

echo json_encode($response);
?>