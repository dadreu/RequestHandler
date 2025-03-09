<?php
// Подключение к базе данных
include 'config.php';

$response = array('success' => false);

// Проверка логина и пароля
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Отладочный вывод
    var_dump($_POST); // Для отладки

    // Подготовленный запрос для поиска пользователя по логину
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если пользователь найден и пароли совпадают
    if ($user && $user['password'] === $password) {
        // Пароль верен
        $response['success'] = true;
    }
}

// Отправка ответа в формате JSON
echo json_encode($response);
?>
