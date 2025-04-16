-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: amvera-dadreu-run-salondb
-- Время создания: Апр 16 2025 г., 22:22
-- Версия сервера: 8.1.0
-- Версия PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `SalonDB`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Appointments`
--

CREATE TABLE `Appointments` (
  `id_appointment` int NOT NULL,
  `id_master_service` int NOT NULL,
  `client_id` int NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Appointments`
--

INSERT INTO `Appointments` (`id_appointment`, `id_master_service`, `client_id`, `date_time`) VALUES
(1, 1, 23, '2025-04-01 09:30:00'),
(2, 1, 23, '2025-04-02 09:45:00'),
(3, 4, 12, '2025-03-26 13:00:00'),
(4, 4, 23, '2025-03-31 10:00:00'),
(5, 4, 23, '2025-04-01 10:15:00'),
(6, 4, 23, '2025-03-30 13:00:00'),
(7, 2, 23, '2025-04-14 09:15:00'),
(8, 6, 10, '2025-04-17 12:00:00'),
(9, 5, 15, '2025-03-18 10:00:00'),
(10, 5, 16, '2025-03-31 12:15:00'),
(11, 5, 23, '2025-03-31 15:45:00'),
(12, 5, 23, '2025-04-01 10:00:00'),
(13, 5, 23, '2025-04-02 11:00:00'),
(14, 5, 23, '2025-04-02 15:45:00'),
(15, 6, 14, '2025-03-26 15:00:00'),
(16, 6, 32, '2025-04-02 14:45:00'),
(17, 6, 23, '2025-04-10 14:30:00'),
(18, 6, 23, '2025-04-11 13:30:00'),
(19, 7, 23, '2025-04-05 14:45:00');

-- --------------------------------------------------------

--
-- Структура таблицы `Bots`
--

CREATE TABLE `Bots` (
  `id_bot` int NOT NULL,
  `bot_token` varchar(255) NOT NULL COMMENT 'Токен Telegram-бота',
  `bot_username` varchar(255) NOT NULL COMMENT 'Имя бота (например, @BotName)',
  `salon_id` int NOT NULL COMMENT 'ID салона, к которому привязан бот',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Bots`
--

INSERT INTO `Bots` (`id_bot`, `bot_token`, `bot_username`, `salon_id`, `created_at`) VALUES
(3, '8168606272:AAFuikWYy8UKjzK3iuyMjRtWHCdS1KKECbE', '@ReqHand_bot', 1, '2025-04-16 21:10:01'),
(4, '7922175259:AAFthA1LcUs8Oh5wh01z3eQyr3uBh2F9w8I', '@ReqHandSec_bot', 2, '2025-04-16 21:10:01');

-- --------------------------------------------------------

--
-- Структура таблицы `Clients`
--

CREATE TABLE `Clients` (
  `id_clients` int NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `telegram_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Clients`
--

INSERT INTO `Clients` (`id_clients`, `full_name`, `phone`, `telegram_id`) VALUES
(8, 'Драчёв Андрей Дмитриевич', '79125965745', NULL),
(9, 'Драчёв Андрей Григорьевич', '79125965767', NULL),
(10, 'Тепляков Игорь Дмитриевич', '79125965749', NULL),
(12, 'Силуанов Геннадий Викторович', '79125965758', NULL),
(13, 'Медведев Алексей Ефимович', '79125965642', NULL),
(14, 'Силуанов Геннадий Ефремович', '79125976422', NULL),
(15, 'Константинов Микки Константинович', '79794774461', NULL),
(16, 'Сергеев Валерий Фёдорович', '79995994523', NULL),
(18, 'Самойлов Сармат Игнатович', '79563422233', NULL),
(23, 'Драчёв Андрей Станиславович', '79125965744', 806176907),
(32, 'Глебов Игорь Александрович', '79125951905', 5069005058);

-- --------------------------------------------------------

--
-- Структура таблицы `ConfirmationCodes`
--

CREATE TABLE `ConfirmationCodes` (
  `id_confirmation_code` int NOT NULL,
  `phone` varchar(20) NOT NULL,
  `telegram_id` bigint NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `ConfirmationCodes`
--

INSERT INTO `ConfirmationCodes` (`id_confirmation_code`, `phone`, `telegram_id`, `code`, `created_at`) VALUES
(64, '79125965744', 806176907, '813262', '2025-04-04 22:08:37'),
(65, '79125965744', 806176907, '515720', '2025-04-04 22:25:55'),
(66, '79125965744', 806176907, '518552', '2025-04-05 23:03:36'),
(67, '79125965744', 806176907, '618736', '2025-04-06 00:33:57'),
(68, '79125965744', 806176907, '615783', '2025-04-11 19:28:10'),
(69, '79125965744', 806176907, '748092', '2025-04-11 19:43:42'),
(70, '79125965744', 806176907, '686377', '2025-04-12 21:08:55'),
(71, '79125965744', 806176907, '966441', '2025-04-12 21:24:29'),
(72, '79125965744', 806176907, '921902', '2025-04-16 19:25:29');

-- --------------------------------------------------------

--
-- Структура таблицы `Logs`
--

CREATE TABLE `Logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` varchar(50) NOT NULL,
  `action` text NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Logs`
--

INSERT INTO `Logs` (`id`, `user_id`, `role`, `action`, `timestamp`) VALUES
(1, 2, 'master', 'Удалил запись с ID 42', '2025-04-12 20:33:11'),
(2, 2, 'master', 'Обновил доступность услуг', '2025-04-12 20:33:22'),
(3, 2, 'master', 'Обновил доступность услуг', '2025-04-12 20:33:31'),
(4, 2, 'master', 'Добавил услугу с ID 13', '2025-04-12 20:33:44'),
(5, 2, 'master', 'Удалил услугу с ID 13', '2025-04-12 20:33:49'),
(6, 2, 'master', 'Создал запись для клиента с ID 23', '2025-04-12 21:04:22'),
(7, 23, 'client', 'Авторизация клиента', '2025-04-12 21:24:44'),
(8, 23, 'client', 'Создал запись для клиента с ID 23', '2025-04-12 21:25:33'),
(9, 2, 'master', 'Удалил запись с ID 20', '2025-04-16 19:22:17'),
(10, 2, 'master', 'Обновил доступность услуг', '2025-04-16 19:22:23'),
(11, 2, 'master', 'Добавил услугу с ID 14', '2025-04-16 19:22:43'),
(12, 23, 'client', 'Авторизация клиента', '2025-04-16 19:25:38'),
(13, 2, 'master', 'Обновил запись с ID 8', '2025-04-16 19:38:38'),
(14, 2, 'master', 'Удалил услугу с ID 14', '2025-04-16 19:38:50'),
(15, 2, 'master', 'Обновил доступность услуг', '2025-04-16 19:38:54'),
(16, 2, 'master', 'Сохранил расписание', '2025-04-16 19:52:34'),
(17, 2, 'master', 'Сохранил расписание', '2025-04-16 19:56:43'),
(18, 2, 'master', 'Сохранил расписание', '2025-04-16 19:57:21'),
(19, 2, 'master', 'Мастер авторизовался в салоне 1', '2025-04-16 22:21:09'),
(20, 0, 'system', 'Инициализирована сессия для бота 7922175259:AAFthA1LcUs8Oh5wh01z3eQyr3uBh2F9w8I, salon_id: 2', '2025-04-16 22:22:14');

-- --------------------------------------------------------

--
-- Структура таблицы `Masters`
--

CREATE TABLE `Masters` (
  `id_masters` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salon_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Masters`
--

INSERT INTO `Masters` (`id_masters`, `full_name`, `phone`, `password`, `salon_id`) VALUES
(1, 'Алексей Сидоров', '791111111111', '$2y$10$p.lga3EoHi5VweYxF0ceDeXVqDrs7cm5owi0XYAD0.WsfmW5q6g2u', 2),
(2, 'Екатерина Морозова', '79222222222', '$2y$10$2q5QJ7VTl8o10oEimAXdJ.5t4eiTwDiezKMdOEoTUgFTLT6yQeraa', 1),
(3, 'Дмитрий Орлов', '79333333333', '$2y$10$HxBiMHmly.1ZRz1FLX3uqe3L8X2zb0vjDtf/gMgalv8YoIwvjRvoK', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `MasterSchedule`
--

CREATE TABLE `MasterSchedule` (
  `id_master_schedule` int NOT NULL,
  `master_id` int NOT NULL,
  `day_of_week` enum('Вс','Пн','Вт','Ср','Чт','Пт','Сб') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_day_off` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `MasterSchedule`
--

INSERT INTO `MasterSchedule` (`id_master_schedule`, `master_id`, `day_of_week`, `start_time`, `end_time`, `is_day_off`) VALUES
(42, 3, 'Пн', '11:00:00', '20:00:00', 0),
(43, 3, 'Вт', '12:00:00', '20:00:00', 0),
(44, 3, 'Ср', '13:00:00', '20:00:00', 0),
(45, 3, 'Чт', '14:00:00', '20:00:00', 0),
(46, 3, 'Пт', '15:00:00', '20:00:00', 0),
(47, 3, 'Сб', '16:00:00', '20:00:00', 0),
(48, 3, 'Вс', '17:00:00', '20:00:00', 0),
(203, 1, 'Пн', '09:15:00', '18:00:00', 0),
(204, 1, 'Вт', '09:30:00', '18:00:00', 0),
(205, 1, 'Ср', '09:45:00', '18:00:00', 0),
(206, 1, 'Чт', '09:00:00', '14:00:00', 0),
(207, 1, 'Пт', '09:15:00', '18:00:00', 0),
(208, 1, 'Сб', '09:30:00', '18:00:00', 0),
(209, 1, 'Вс', '20:45:00', '18:00:00', 0),
(265, 2, 'Пн', '09:00:00', '18:00:00', 0),
(266, 2, 'Вт', '10:00:00', '18:00:00', 0),
(267, 2, 'Ср', '11:00:00', '18:00:00', 0),
(268, 2, 'Чт', '12:00:00', '18:00:00', 0),
(269, 2, 'Пт', '13:00:00', '18:00:00', 0),
(270, 2, 'Сб', '13:00:00', '18:00:00', 0),
(271, 2, 'Вс', '09:00:00', '11:00:00', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `MasterServices`
--

CREATE TABLE `MasterServices` (
  `id_master_service` int NOT NULL,
  `master_id` int NOT NULL,
  `service_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `is_available` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `MasterServices`
--

INSERT INTO `MasterServices` (`id_master_service`, `master_id`, `service_id`, `price`, `duration`, `is_available`) VALUES
(1, 1, 1, 1000.00, 40, 1),
(2, 2, 1, 1200.00, 45, 1),
(3, 3, 2, 1500.00, 60, 1),
(4, 1, 4, 1200.00, 90, 1),
(5, 2, 3, 3000.00, 120, 1),
(6, 2, 7, 1200.00, 60, 1),
(7, 2, 8, 800.00, 60, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `Salons`
--

CREATE TABLE `Salons` (
  `id_salon` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Salons`
--

INSERT INTO `Salons` (`id_salon`, `name`, `address`) VALUES
(1, 'Салон 1', 'ул. Примерная, д. 1'),
(2, 'Салон Красоты №2', 'ул. Образцовая, д. 2, г. Екатеринбург');

-- --------------------------------------------------------

--
-- Структура таблицы `Services`
--

CREATE TABLE `Services` (
  `id_service` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `salon_id` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Services`
--

INSERT INTO `Services` (`id_service`, `name`, `salon_id`) VALUES
(1, 'Стрижка мужская', 1),
(2, 'Стрижка женская', 1),
(3, 'Окрашивание волос', 1),
(4, 'Маникюр', 1),
(5, 'Педикюр', 1),
(6, 'Массаж лица', 1),
(7, 'Формирование бороды', 1),
(8, 'Опасное бритьё', 1),
(13, 'цуа', 1),
(14, 'Формовка бородыйцу', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD PRIMARY KEY (`id_appointment`),
  ADD KEY `id_master_service` (`id_master_service`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_date_time` (`date_time`);

--
-- Индексы таблицы `Bots`
--
ALTER TABLE `Bots`
  ADD PRIMARY KEY (`id_bot`),
  ADD UNIQUE KEY `bot_token` (`bot_token`),
  ADD KEY `salon_id` (`salon_id`);

--
-- Индексы таблицы `Clients`
--
ALTER TABLE `Clients`
  ADD PRIMARY KEY (`id_clients`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_telegram_id` (`telegram_id`);

--
-- Индексы таблицы `ConfirmationCodes`
--
ALTER TABLE `ConfirmationCodes`
  ADD PRIMARY KEY (`id_confirmation_code`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_telegram_id` (`telegram_id`);

--
-- Индексы таблицы `Logs`
--
ALTER TABLE `Logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Masters`
--
ALTER TABLE `Masters`
  ADD PRIMARY KEY (`id_masters`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `fk_masters_salon` (`salon_id`);

--
-- Индексы таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  ADD PRIMARY KEY (`id_master_schedule`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `idx_master_day` (`master_id`,`day_of_week`);

--
-- Индексы таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  ADD PRIMARY KEY (`id_master_service`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_master_service_available` (`master_id`,`service_id`,`is_available`);

--
-- Индексы таблицы `Salons`
--
ALTER TABLE `Salons`
  ADD PRIMARY KEY (`id_salon`);

--
-- Индексы таблицы `Services`
--
ALTER TABLE `Services`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `fk_services_salon` (`salon_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Appointments`
--
ALTER TABLE `Appointments`
  MODIFY `id_appointment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT для таблицы `Bots`
--
ALTER TABLE `Bots`
  MODIFY `id_bot` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `Clients`
--
ALTER TABLE `Clients`
  MODIFY `id_clients` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `ConfirmationCodes`
--
ALTER TABLE `ConfirmationCodes`
  MODIFY `id_confirmation_code` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT для таблицы `Logs`
--
ALTER TABLE `Logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `Masters`
--
ALTER TABLE `Masters`
  MODIFY `id_masters` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  MODIFY `id_master_schedule` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=272;

--
-- AUTO_INCREMENT для таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  MODIFY `id_master_service` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `Salons`
--
ALTER TABLE `Salons`
  MODIFY `id_salon` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `Services`
--
ALTER TABLE `Services`
  MODIFY `id_service` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD CONSTRAINT `Appointments_ibfk_1` FOREIGN KEY (`id_master_service`) REFERENCES `MasterServices` (`id_master_service`) ON DELETE CASCADE,
  ADD CONSTRAINT `Appointments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `Clients` (`id_clients`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Bots`
--
ALTER TABLE `Bots`
  ADD CONSTRAINT `Bots_ibfk_1` FOREIGN KEY (`salon_id`) REFERENCES `Salons` (`id_salon`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `Masters`
--
ALTER TABLE `Masters`
  ADD CONSTRAINT `fk_masters_salon` FOREIGN KEY (`salon_id`) REFERENCES `Salons` (`id_salon`) ON DELETE CASCADE,
  ADD CONSTRAINT `Masters_ibfk_1` FOREIGN KEY (`salon_id`) REFERENCES `Salons` (`id_salon`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  ADD CONSTRAINT `MasterSchedule_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id_masters`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  ADD CONSTRAINT `masterservices_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id_masters`) ON DELETE CASCADE,
  ADD CONSTRAINT `masterservices_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id_service`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Services`
--
ALTER TABLE `Services`
  ADD CONSTRAINT `fk_services_salon` FOREIGN KEY (`salon_id`) REFERENCES `Salons` (`id_salon`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
