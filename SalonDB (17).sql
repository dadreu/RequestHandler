-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: gpjj.ru
-- Время создания: Апр 05 2025 г., 22:26
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
  `master_id` int NOT NULL,
  `client_id` int NOT NULL,
  `service_id` int NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Appointments`
--

INSERT INTO `Appointments` (`id_appointment`, `master_id`, `client_id`, `service_id`, `date_time`) VALUES
(24, 2, 10, 3, '2025-03-17 13:45:00'),
(26, 1, 12, 4, '2025-03-26 13:00:00'),
(28, 2, 14, 7, '2025-03-26 15:00:00'),
(29, 2, 15, 3, '2025-03-18 10:00:00'),
(30, 2, 16, 3, '2025-03-31 12:15:00'),
(37, 2, 23, 3, '2025-03-31 15:45:00'),
(42, 2, 23, 3, '2025-03-31 09:15:00'),
(43, 2, 23, 3, '2025-04-01 10:00:00'),
(45, 1, 23, 4, '2025-03-31 10:00:00'),
(46, 1, 23, 1, '2025-04-01 09:30:00'),
(47, 1, 23, 4, '2025-04-01 10:15:00'),
(48, 1, 23, 4, '2025-03-30 13:00:00'),
(50, 2, 23, 3, '2025-04-02 11:00:00'),
(52, 2, 23, 3, '2025-04-02 15:45:00'),
(53, 1, 23, 1, '2025-04-02 09:45:00'),
(56, 2, 32, 7, '2025-04-02 14:45:00'),
(58, 2, 23, 8, '2025-04-05 14:45:00');

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
(65, '79125965744', 806176907, '515720', '2025-04-04 22:25:55');

-- --------------------------------------------------------

--
-- Структура таблицы `Masters`
--

CREATE TABLE `Masters` (
  `id_masters` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Masters`
--

INSERT INTO `Masters` (`id_masters`, `full_name`, `phone`, `password`) VALUES
(1, 'Алексей Сидоров', '791111111111', 'password1'),
(2, 'Екатерина Морозова', '79222222222', 'password2'),
(3, 'Дмитрий Орлов', '79333333333', 'password3');

-- --------------------------------------------------------

--
-- Структура таблицы `MasterSchedule`
--

CREATE TABLE `MasterSchedule` (
  `id_master_schedule` int NOT NULL,
  `master_id` int NOT NULL,
  `day_of_week` enum('Вс','Пн','Вт','Ср','Чт','Пт','Сб') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `MasterSchedule`
--

INSERT INTO `MasterSchedule` (`id_master_schedule`, `master_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(42, 3, 'Пн', '11:00:00', '20:00:00'),
(43, 3, 'Вт', '12:00:00', '20:00:00'),
(44, 3, 'Ср', '13:00:00', '20:00:00'),
(45, 3, 'Чт', '14:00:00', '20:00:00'),
(46, 3, 'Пт', '15:00:00', '20:00:00'),
(47, 3, 'Сб', '16:00:00', '20:00:00'),
(48, 3, 'Вс', '17:00:00', '20:00:00'),
(203, 1, 'Пн', '09:15:00', '18:00:00'),
(204, 1, 'Вт', '09:30:00', '18:00:00'),
(205, 1, 'Ср', '09:45:00', '18:00:00'),
(206, 1, 'Чт', '09:00:00', '14:00:00'),
(207, 1, 'Пт', '09:15:00', '18:00:00'),
(208, 1, 'Сб', '09:30:00', '18:00:00'),
(209, 1, 'Вс', '20:45:00', '18:00:00'),
(210, 2, 'Пн', '09:00:00', '18:00:00'),
(211, 2, 'Вт', '10:00:00', '18:00:00'),
(212, 2, 'Ср', '11:00:00', '18:00:00'),
(213, 2, 'Чт', '12:00:00', '18:00:00'),
(214, 2, 'Пт', '13:00:00', '18:00:00'),
(215, 2, 'Сб', '13:00:00', '18:00:00'),
(216, 2, 'Вс', '09:00:00', '11:00:00');

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
-- Структура таблицы `Services`
--

CREATE TABLE `Services` (
  `id_service` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Services`
--

INSERT INTO `Services` (`id_service`, `name`) VALUES
(4, 'Маникюр'),
(6, 'Массаж лица'),
(3, 'Окрашивание волос'),
(8, 'Опасное бритьё'),
(5, 'Педикюр'),
(2, 'Стрижка женская'),
(1, 'Стрижка мужская'),
(7, 'Формирование бороды');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD PRIMARY KEY (`id_appointment`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_date_time` (`date_time`);

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
-- Индексы таблицы `Masters`
--
ALTER TABLE `Masters`
  ADD PRIMARY KEY (`id_masters`),
  ADD UNIQUE KEY `phone` (`phone`);

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
-- Индексы таблицы `Services`
--
ALTER TABLE `Services`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `idx_name` (`name`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Appointments`
--
ALTER TABLE `Appointments`
  MODIFY `id_appointment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT для таблицы `Clients`
--
ALTER TABLE `Clients`
  MODIFY `id_clients` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `ConfirmationCodes`
--
ALTER TABLE `ConfirmationCodes`
  MODIFY `id_confirmation_code` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT для таблицы `Masters`
--
ALTER TABLE `Masters`
  MODIFY `id_masters` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  MODIFY `id_master_schedule` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT для таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  MODIFY `id_master_service` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `Services`
--
ALTER TABLE `Services`
  MODIFY `id_service` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD CONSTRAINT `Appointments_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id_masters`) ON DELETE CASCADE,
  ADD CONSTRAINT `Appointments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `Clients` (`id_clients`) ON DELETE CASCADE,
  ADD CONSTRAINT `Appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id_service`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
