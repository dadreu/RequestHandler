<?php
include 'config.php';
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['code'])) {
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    if (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1);
    }
    $code = $_POST['code'];

    // Получаем последний код
    $stmt = $pdo->prepare("SELECT * FROM ConfirmationCodes WHERE phone = :phone ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['phone' => $phone]);
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($confirmation && $confirmation['code'] == $code) {
        $created_at = new DateTime($confirmation['created_at']);
        $now = new DateTime();
        $interval = $now->diff($created_at);
        if ($interval->i < 5) { // Код действителен 5 минут
            $stmt = $pdo->prepare("SELECT id_client FROM Clients WHERE phone = :phone");
            $stmt->execute(['phone' => $phone]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($client) {
                $response['success'] = true;
                $response['client_id'] = $client['id_client'];
            }
        } else {
            $response['message'] = 'Код устарел';
        }
    } else {
        $response['message'] = 'Неверный код';
    }
} else {
    $response['message'] = 'Недостаточно данных';
}

echo json_encode($response);
?>