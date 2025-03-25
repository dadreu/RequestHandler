<?php
$host = 'amvera-dadreu-run-salondb';  // Внутреннее доменное имя базы данных
$port = '3306';  // Стандартный порт MySQL
$dbname = 'SalonDB';  // Имя базы данных из переменной окружения
$username = getenv('MYSQL_USER');  // Имя пользователя из переменной окружения
$password = getenv('MYSQL_PASSWORD');  // Пароль из переменной окружения

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Ошибка подключения к БД: " . $e->getMessage()]));
}
?>