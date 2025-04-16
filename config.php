<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

try {
    $host = 'amvera-dadreu-run-salondb';
    $port = '3306';
    $dbname = 'SalonDB';
    $username = getenv('MYSQL_USER') ?: 'your_username';
    $password = getenv('MYSQL_PASSWORD') ?: 'your_password';

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    error_log("Ошибка подключения к базе данных: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
    exit;
}
?>