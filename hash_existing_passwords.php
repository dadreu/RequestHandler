<?php
// Подключение к базе данных через config.php
include 'config.php';

try {
    // Получаем всех мастеров
    $stmt = $pdo->query("SELECT id_masters, password FROM Masters");
    $masters = $stmt->fetchAll();

    // Проверяем, являются ли пароли уже хэшированными
    foreach ($masters as $master) {
        $password = $master['password'];
        $id = $master['id_masters'];

        // Проверяем, не является ли пароль уже хэшем (хэш обычно длиннее 60 символов)
        if (strlen($password) < 60 || !password_get_info($password)['algo']) {
            // Пароль не хэширован, создаём хэш
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Обновляем пароль в базе данных
            $update_stmt = $pdo->prepare("UPDATE Masters SET password = :hashed_password WHERE id_masters = :id");
            $update_stmt->execute([
                'hashed_password' => $hashed_password,
                'id' => $id
            ]);

            echo "Пароль для мастера ID $id успешно хэширован.\n";
        } else {
            echo "Пароль для мастера ID $id уже хэширован, пропускаем.\n";
        }
    }

    echo "Обработка завершена.\n";
} catch (Exception $e) {
    die('Ошибка: ' . $e->getMessage());
}
?>