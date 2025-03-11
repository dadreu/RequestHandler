<?php
// Подключаем конфигурацию для подключения к базе данных
include 'config.php';

// Массивы для мастеров и услуг
$masters = [];
$services = [];

try {
    // Получаем мастеров
    $sql_masters = "SELECT id, full_name FROM Masters";
    $stmt_masters = $pdo->query($sql_masters);
    
    if ($stmt_masters) {
        // Извлекаем данные о мастерах
        while ($row = $stmt_masters->fetch(PDO::FETCH_ASSOC)) {
            $masters[] = $row;
        }
    }

    // Получаем услуги
    $sql_services = "SELECT id, name FROM Services WHERE status = 'active'";
    $stmt_services = $pdo->query($sql_services);

    if ($stmt_services) {
        // Извлекаем данные об услугах
        while ($row = $stmt_services->fetch(PDO::FETCH_ASSOC)) {
            $services[] = $row;
        }
    }

    // Возвращаем данные в формате JSON
    echo json_encode(['masters' => $masters, 'services' => $services]);

} catch (PDOException $e) {
    // Если произошла ошибка, выводим её
    die(json_encode(["error" => "Ошибка при выполнении запроса: " . $e->getMessage()]));
}
?>
