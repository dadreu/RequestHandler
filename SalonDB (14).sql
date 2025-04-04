-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: amvera-dadreu-run-salondb
-- Время создания: Апр 04 2025 г., 21:42
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
  `id` int NOT NULL,
  `master_id` int NOT NULL,
  `client_id` int NOT NULL,
  `service_id` int NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Appointments`
--

INSERT INTO `Appointments` (`id`, `master_id`, `client_id`, `service_id`, `date_time`) VALUES
(24, 2, 10, 3, '2025-03-17 13:45:00'),
(26, 1, 12, 4, '2025-03-26 13:00:00'),
(28, 2, 14, 7, '2025-03-26 15:00:00'),
(29, 2, 15, 3, '2025-03-18 10:00:00'),
(30, 2, 16, 3, '2025-03-31 12:15:00'),
(37, 2, 23, 3, '2025-03-31 15:45:00'),
(38, 2, 23, 7, '2025-03-31 11:15:00'),
(42, 2, 23, 3, '2025-03-31 09:15:00'),
(43, 2, 23, 3, '2025-04-01 10:00:00'),
(45, 1, 23, 4, '2025-03-31 10:00:00'),
(46, 1, 23, 1, '2025-04-01 09:30:00'),
(47, 1, 23, 4, '2025-04-01 10:15:00'),
(48, 1, 23, 4, '2025-03-30 13:00:00'),
(50, 2, 23, 3, '2025-04-02 11:00:00'),
(52, 2, 23, 3, '2025-04-02 15:45:00'),
(53, 1, 23, 1, '2025-04-02 09:45:00'),
(56, 2, 32, 7, '2025-04-02 14:45:00');

-- --------------------------------------------------------

--
-- Структура таблицы `Clients`
--

CREATE TABLE `Clients` (
  `id` int NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `telegram_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Clients`
--

INSERT INTO `Clients` (`id`, `full_name`, `phone`, `telegram_id`) VALUES
(8, 'Драчёв Андрей Дмитриевич', '79125965745', NULL),
(9, 'Драчёв Андрей Григорьевич', '79125965767', NULL),
(10, 'Тепляков Игорь Дмитриевич', '79125965749', NULL),
(12, 'Силуанов Геннадий Викторович', '79125965758', NULL),
(13, 'Медведев Алексей Ефимович', '79125965642', NULL),
(14, 'Силуанов Геннадий Ефремович', '79125976422', NULL),
(15, 'Константинов Микки Константинович', '79794774461', NULL),
(16, 'Сергеев Валерий Фёдорович', '79995994523', NULL),
(18, 'Самойлов Сармат Игнатович', '79563422233', NULL),
(23, 'Драчёв Андрей Станиславович', '79125965744', '806176907'),
(32, 'Глебов Игорь Александрович', '79125951905', '5069005058');

-- --------------------------------------------------------

--
-- Структура таблицы `ConfirmationCodes`
--

CREATE TABLE `ConfirmationCodes` (
  `id` int NOT NULL,
  `phone` varchar(20) NOT NULL,
  `telegram_id` bigint NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `ConfirmationCodes`
--

INSERT INTO `ConfirmationCodes` (`id`, `phone`, `telegram_id`, `code`, `created_at`) VALUES
(4, '89125965744', 806176907, '470293', '2025-03-31 01:01:43'),
(5, '89125965744', 806176907, '835921', '2025-03-31 01:13:44'),
(6, '89125965744', 806176907, '279690', '2025-03-31 01:37:31'),
(7, '89125965744', 806176907, '521346', '2025-03-31 01:41:27'),
(9, '89125965744', 5069005058, '905160', '2025-03-31 01:52:09'),
(10, '89125965749', 5069005058, '233321', '2025-03-31 01:53:00'),
(11, '89125965744', 5069005058, '135626', '2025-03-31 02:20:15'),
(12, '89125965744', 5069005058, '487007', '2025-03-31 02:20:37'),
(13, '89125965749', 5069005058, '705952', '2025-03-31 02:22:39'),
(14, '89222222222', 5069005058, '286920', '2025-03-31 02:53:21'),
(15, '89125965744', 5069005058, '972061', '2025-03-31 03:08:48'),
(16, '89563422233', 806176907, '143687', '2025-03-31 03:15:56'),
(17, '+79125965744', 806176907, '269418', '2025-03-31 15:35:01'),
(18, '+79125951905', 5069005058, '268693', '2025-03-31 15:36:09'),
(19, '+79125951905', 5069005058, '569251', '2025-03-31 15:37:00'),
(20, '+79125965744', 806176907, '830310', '2025-03-31 15:37:29'),
(21, '+79125965744', 806176907, '101698', '2025-03-31 15:53:42'),
(22, '79125965744', 806176907, '211167', '2025-03-31 16:42:59'),
(23, '79125965744', 806176907, '267812', '2025-03-31 16:44:44'),
(24, '79125965744', 806176907, '608704', '2025-03-31 17:10:54'),
(25, '79125965744', 806176907, '510107', '2025-03-31 17:12:15'),
(26, '79125965744', 806176907, '110077', '2025-03-31 18:20:12'),
(27, '79125965744', 806176907, '353076', '2025-03-31 19:20:21'),
(28, '79125965744', 806176907, '962121', '2025-03-31 19:23:42'),
(29, '79125965744', 806176907, '582447', '2025-03-31 19:24:34'),
(30, '79223664227', 1044612930, '672058', '2025-03-31 20:38:33'),
(31, '79223664227', 1044612930, '567553', '2025-03-31 20:40:28'),
(32, '79223664227', 1044612930, '340049', '2025-03-31 20:44:08'),
(33, '79223664227', 1044612930, '200364', '2025-03-31 20:45:07'),
(34, '79223664227', 1044612930, '861199', '2025-03-31 20:51:15'),
(35, '79222431205', 5922773631, '197777', '2025-03-31 21:00:07'),
(36, '79222431205', 5922773631, '887290', '2025-03-31 21:02:01'),
(37, '79222431205', 5922773631, '415635', '2025-03-31 21:02:18'),
(38, '79125965744', 806176907, '851061', '2025-03-31 22:28:50'),
(39, '79125965744', 806176907, '871174', '2025-03-31 22:33:38'),
(40, '79125965744', 806176907, '331892', '2025-03-31 23:12:35'),
(41, '79125965744', 806176907, '170104', '2025-03-31 23:29:15'),
(42, '79125965744', 806176907, '335034', '2025-03-31 23:40:33'),
(43, '79125965744', 806176907, '753725', '2025-03-31 23:44:39'),
(44, '79125965744', 806176907, '305141', '2025-03-31 23:45:45'),
(45, '79125965744', 806176907, '382812', '2025-03-31 23:58:22'),
(46, '79125965744', 806176907, '941355', '2025-04-01 12:28:36'),
(47, '79125965744', 806176907, '625497', '2025-04-01 12:29:26'),
(48, '79125965744', 806176907, '669110', '2025-04-01 19:11:39'),
(49, '79125965744', 806176907, '993491', '2025-04-01 19:42:37'),
(50, '79125965744', 806176907, '192609', '2025-04-01 19:44:45'),
(51, '79125965744', 806176907, '184861', '2025-04-01 19:52:06'),
(52, '79125965744', 806176907, '659431', '2025-04-01 20:01:04'),
(53, '79125965744', 806176907, '681529', '2025-04-01 21:01:47'),
(54, '79125965744', 806176907, '970008', '2025-04-01 21:49:17'),
(55, '79125951905', 806176907, '971215', '2025-04-01 22:01:08'),
(56, '79125951905', 5069005058, '785566', '2025-04-01 22:14:20'),
(57, '79125951905', 5069005058, '645288', '2025-04-01 22:16:54'),
(58, '79125951905', 5069005058, '325317', '2025-04-01 22:18:37'),
(59, '79125965744', 806176907, '743056', '2025-04-02 10:36:36'),
(60, '79125965744', 806176907, '856870', '2025-04-02 18:41:11'),
(61, '79125965744', 806176907, '944240', '2025-04-02 21:51:12'),
(62, '79125965744', 806176907, '566180', '2025-04-02 21:52:19'),
(63, '79125965744', 806176907, '636757', '2025-04-02 22:33:50');

-- --------------------------------------------------------

--
-- Структура таблицы `Masters`
--

CREATE TABLE `Masters` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Masters`
--

INSERT INTO `Masters` (`id`, `full_name`, `phone`, `password`) VALUES
(1, 'Алексей Сидоров', '791111111111', 'password1'),
(2, 'Екатерина Морозова', '79222222222', 'password2'),
(3, 'Дмитрий Орлов', '79333333333', 'password3');

-- --------------------------------------------------------

--
-- Структура таблицы `MasterSchedule`
--

CREATE TABLE `MasterSchedule` (
  `id` int NOT NULL,
  `master_id` int NOT NULL,
  `day_of_week` enum('Вс','Пн','Вт','Ср','Чт','Пт','Сб') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `MasterSchedule`
--

INSERT INTO `MasterSchedule` (`id`, `master_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(42, 3, 'Пн', '11:00:00', '20:00:00'),
(43, 3, 'Вт', '12:00:00', '20:00:00'),
(44, 3, 'Ср', '13:00:00', '20:00:00'),
(45, 3, 'Чт', '14:00:00', '20:00:00'),
(46, 3, 'Пт', '15:00:00', '20:00:00'),
(47, 3, 'Сб', '16:00:00', '20:00:00'),
(48, 3, 'Вс', '17:00:00', '20:00:00'),
(189, 2, 'Пн', '09:00:00', '18:00:00'),
(190, 2, 'Вт', '10:00:00', '18:00:00'),
(191, 2, 'Ср', '11:00:00', '18:00:00'),
(192, 2, 'Чт', '12:00:00', '18:00:00'),
(193, 2, 'Пт', '13:00:00', '18:00:00'),
(194, 2, 'Сб', '13:00:00', '18:00:00'),
(195, 2, 'Вс', '09:00:00', '11:00:00'),
(203, 1, 'Пн', '09:15:00', '18:00:00'),
(204, 1, 'Вт', '09:30:00', '18:00:00'),
(205, 1, 'Ср', '09:45:00', '18:00:00'),
(206, 1, 'Чт', '09:00:00', '14:00:00'),
(207, 1, 'Пт', '09:15:00', '18:00:00'),
(208, 1, 'Сб', '09:30:00', '18:00:00'),
(209, 1, 'Вс', '20:45:00', '18:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `MasterServices`
--

CREATE TABLE `MasterServices` (
  `id` int NOT NULL,
  `master_id` int NOT NULL,
  `service_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `is_available` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `MasterServices`
--

INSERT INTO `MasterServices` (`id`, `master_id`, `service_id`, `price`, `duration`, `is_available`) VALUES
(1, 1, 1, 1000.00, 40, 1),
(2, 2, 1, 1200.00, 45, 1),
(3, 3, 2, 1500.00, 60, 1),
(4, 1, 4, 1200.00, 90, 1),
(5, 2, 3, 3000.00, 120, 1),
(6, 2, 7, 1200.00, 60, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `Services`
--

CREATE TABLE `Services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Services`
--

INSERT INTO `Services` (`id`, `name`, `description`) VALUES
(1, 'Стрижка мужская', 'Классическая мужская стрижка'),
(2, 'Стрижка женская', 'Женская стрижка любой сложности'),
(3, 'Окрашивание волос', 'Окрашивание в один тон или сложные техники'),
(4, 'Маникюр', 'Классический и аппаратный маникюр'),
(5, 'Педикюр', 'Гигиенический и аппаратный педикюр'),
(6, 'Массаж лица', 'Расслабляющий массаж лица'),
(7, 'Формирование бороды', '');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `Clients`
--
ALTER TABLE `Clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `phone_2` (`phone`),
  ADD UNIQUE KEY `phone_3` (`phone`);

--
-- Индексы таблицы `ConfirmationCodes`
--
ALTER TABLE `ConfirmationCodes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Masters`
--
ALTER TABLE `Masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Индексы таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_id` (`master_id`);

--
-- Индексы таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `Services`
--
ALTER TABLE `Services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Appointments`
--
ALTER TABLE `Appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT для таблицы `Clients`
--
ALTER TABLE `Clients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `ConfirmationCodes`
--
ALTER TABLE `ConfirmationCodes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT для таблицы `Masters`
--
ALTER TABLE `Masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT для таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `Services`
--
ALTER TABLE `Services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD CONSTRAINT `Appointments_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Appointments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `Clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  ADD CONSTRAINT `MasterSchedule_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  ADD CONSTRAINT `masterservices_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `masterservices_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
