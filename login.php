<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Логин и пароль обязательны.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($password === $user['password']) {
            echo json_encode([
                'success' => true,
                'role' => $user['role'],
                'id' => $user['role'] === 'master' ? $user['master_id'] : $user['client_id']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Неверный пароль.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Пользователь не найден.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса.']);
}
?>