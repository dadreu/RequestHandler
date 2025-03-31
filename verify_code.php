<?php
include 'config.php'; // Подключение к базе данных
header('Content-Type: application/json');
$response = ['success' => false];

if (isset($_POST['phone']) && isset($_POST['telegram_id']) && isset($_POST['code'])) {
    $phone = $_POST['phone'];
    $telegram_id = $_POST['telegram_id'];
    $code = $_POST['code'];

    // Проверка кода (действителен 5 минут)
    $stmt = $pdo->prepare("SELECT * FROM ConfirmationCodes WHERE phone = :phone AND telegram_id = :telegram_id AND code = :code AND created_at > NOW() - INTERVAL 5 MINUTE");
    $stmt->execute(['phone' => $phone, 'telegram_id' => $telegram_id, 'code' => $code]);
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($confirmation) {
        // Код верный, определяем роль
        $stmt = $pdo->prepare("SELECT id FROM Masters WHERE phone = :phone");
        $stmt->execute(['phone' => $phone]);
        $master = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($master) {
            $master_id = $master['id'];
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE role = 'master' AND master_id = :master_id");
            $stmt->execute(['master_id' => $master_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $response['success'] = true;
                $response['role'] = 'master';
                $response['master_id'] = $master_id;
            }
        } else {
            $stmt = $pdo->prepare("SELECT id, password FROM Clients WHERE phone = :phone");
            $stmt->execute(['phone' => $phone]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($client) {
                $client_id = $client['id'];
                $response['success'] = true;
                $response['client_id'] = $client_id;
                if (empty($client['password'])) {
                    $response['complete_registration'] = true;
                } else {
                    $response['role'] = 'client';
                }
            }
        }
        // Удаление использованного кода
        $stmt = $pdo->prepare("DELETE FROM ConfirmationCodes WHERE id = :id");
        $stmt->execute(['id' => $confirmation['id']]);
    } else {
        $response['message'] = 'Неверный код или код устарел';
    }
}

echo json_encode($response);
?>