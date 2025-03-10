<?php
$host = 'localhost';
$port = '3307'; // Указание порта MySQL
$dbname = 'SalonDB';
$username = 'root'; // Укажите ваш логин MySQL
$password = ''; // Укажите ваш пароль MySQL

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Ошибка подключения к БД: " . $e->getMessage()]));
}
?>
