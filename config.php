<?php
// Подключение библиотеки phpdotenv (если используется файл .env)
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Получение значений переменных окружения
$host = 'localhost';
$port = '3306';
$dbname = getenv('MYSQL_DATABASE'); // или $_ENV['MYSQL_DATABASE']
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

// Подключение к базе данных через PDO
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Подключение к базе данных успешно!";
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>