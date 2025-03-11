<?php
include 'config.php';

// Получаем данные из формы
$master_id = $_POST['master_id'];
$service_id = $_POST['service_id'];
$fio = $_POST['fio'];
$phone = $_POST['phone'];
$date = $_POST['date'];
$time = $_POST['time'];

// Проверка на обязательные поля
if (empty($master_id) || empty($service_id) || empty($fio) || empty($phone) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения.']);
    exit;
}

try {
    // Начинаем транзакцию
    $pdo->beginTransaction();

    // Проверяем, существует ли клиент с таким номером телефона
    $sql_check_client = "SELECT id FROM Clients WHERE phone = :phone";
    $stmt_check_client = $pdo->prepare($sql_check_client);
    $stmt_check_client->bindParam(':phone', $phone);
    $stmt_check_client->execute();
    $client = $stmt_check_client->fetch(PDO::FETCH_ASSOC);

    // Если клиента нет, добавляем нового
    if (!$client) {
        $sql_insert_client = "INSERT INTO Clients (full_name, phone) VALUES (:fio, :phone)";
        $stmt_insert_client = $pdo->prepare($sql_insert_client);
        $stmt_insert_client->bindParam(':fio', $fio);
        $stmt_insert_client->bindParam(':phone', $phone);
        $stmt_insert_client->execute();

        // Получаем ID нового клиента
        $client_id = $pdo->lastInsertId();
    } else {
        // Если клиент существует, используем его ID
        $client_id = $client['id'];
    }

    // Добавляем запись в таблицу Appointments
    $sql_insert_appointment = "INSERT INTO Appointments (master_id, client_id, service_id, date_time, price) 
                               VALUES (:master_id, :client_id, :service_id, :date_time, :price)";
    $stmt_insert_appointment = $pdo->prepare($sql_insert_appointment);
    $stmt_insert_appointment->bindValue(':master_id', $master_id);
    $stmt_insert_appointment->bindValue(':client_id', $client_id); // Используем существующего клиента
    $stmt_insert_appointment->bindValue(':service_id', $service_id);
    $stmt_insert_appointment->bindValue(':date_time', $date . ' ' . $time);
    $stmt_insert_appointment->bindValue(':price', 1000.00); // Пример цены
    $stmt_insert_appointment->execute();

    // Завершаем транзакцию
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Если произошла ошибка, откатываем транзакцию
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
