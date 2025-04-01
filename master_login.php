<?php
include 'config.php'; // Подключение к базе данных
header('Content-Type: application/json');
$response = ['success' => false];

// Функция нормализации номера телефона
function normalizePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone); // Удаляем все нечисловые символы
    if (strlen($phone) == 11 && $phone[0] == '8') {
        $phone = '7' . substr($phone, 1); // Заменяем 8 на 7
    }
    return $phone;
}

if (!isset($_POST['phone']) || !isset($_POST['password'])) {
    $response['message'] = 'Не переданы номер телефона или пароль';
} else {
    $phone = normalizePhone($_POST['phone']); // Нормализуем номер телефона
    $password = $_POST['password'];

    // Подготовленный запрос к таблице Masters
    $stmt = $pdo->prepare("SELECT id, password FROM Masters WHERE phone = :phone");
    $stmt->execute(['phone' => $phone]);
    $master = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверяем, найден ли пользователь
    if (!$master) {
        $response['message'] = 'Номер телефона не зарегистрирован';
    }
    // Сравнение пароля напрямую
    elseif ($master['password'] === $password) {
        $response['success'] = true;
        $response['master_id'] = $master['id'];
    } else {
        $response['message'] = 'Неверный пароль';
    }
}

echo json_encode($response);
?>