-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 192.168.50.42:3307
-- Время создания: Мар 10 2025 г., 01:16
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

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
CREATE DATABASE IF NOT EXISTS `SalonDB` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `SalonDB`;

-- --------------------------------------------------------

--
-- Структура таблицы `Appointments`
--

CREATE TABLE `Appointments` (
  `id` int NOT NULL,
  `service_id` int NOT NULL,
  `client_id` int NOT NULL,
  `date_time` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Appointments`
--

INSERT INTO `Appointments` (`id`, `service_id`, `client_id`, `date_time`, `price`) VALUES
(1, 1, 1, '2025-03-15 10:30:00', '1000.00'),
(2, 3, 2, '2025-03-16 12:00:00', '1200.00');

-- --------------------------------------------------------

--
-- Структура таблицы `Clients`
--

CREATE TABLE `Clients` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Clients`
--

INSERT INTO `Clients` (`id`, `full_name`, `phone`) VALUES
(1, 'Сидоров Алексей', '+79991234567'),
(2, 'Марина Кузнецова', '+79881234567');

-- --------------------------------------------------------

--
-- Структура таблицы `MasterAppointments`
--

CREATE TABLE `MasterAppointments` (
  `master_id` int NOT NULL,
  `time` datetime NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `service_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `MasterAppointments`
--

INSERT INTO `MasterAppointments` (`master_id`, `time`, `client_name`, `service_id`, `price`) VALUES
(1, '2025-03-15 10:30:00', 'Сидоров Алексей', 1, '1000.00'),
(2, '2025-03-16 12:00:00', 'Марина Кузнецова', 3, '1200.00');

-- --------------------------------------------------------

--
-- Структура таблицы `Masters`
--

CREATE TABLE `Masters` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `monday_start` time DEFAULT NULL,
  `monday_end` time DEFAULT NULL,
  `tuesday_start` time DEFAULT NULL,
  `tuesday_end` time DEFAULT NULL,
  `wednesday_start` time DEFAULT NULL,
  `wednesday_end` time DEFAULT NULL,
  `thursday_start` time DEFAULT NULL,
  `thursday_end` time DEFAULT NULL,
  `friday_start` time DEFAULT NULL,
  `friday_end` time DEFAULT NULL,
  `saturday_start` time DEFAULT NULL,
  `saturday_end` time DEFAULT NULL,
  `sunday_start` time DEFAULT NULL,
  `sunday_end` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Masters`
--

INSERT INTO `Masters` (`id`, `full_name`, `monday_start`, `monday_end`, `tuesday_start`, `tuesday_end`, `wednesday_start`, `wednesday_end`, `thursday_start`, `thursday_end`, `friday_start`, `friday_end`, `saturday_start`, `saturday_end`, `sunday_start`, `sunday_end`) VALUES
(1, 'Иванов Иван Иванович', '09:00:00', '18:00:00', '09:00:00', '18:00:00', '09:00:00', '18:00:00', '09:00:00', '18:00:00', '09:00:00', '18:00:00', NULL, NULL, NULL, NULL),
(2, 'Петров Петр Петрович', '10:00:00', '19:00:00', '10:00:00', '19:00:00', '10:00:00', '19:00:00', '10:00:00', '19:00:00', '10:00:00', '19:00:00', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `Services`
--

CREATE TABLE `Services` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `description` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Services`
--

INSERT INTO `Services` (`id`, `name`, `price`, `duration`, `description`, `status`) VALUES
(1, 'Стрижка мужская', '1000.00', 40, 'Классическая мужская стрижка', 'active'),
(2, 'Стрижка женская', '1500.00', 60, 'Женская стрижка с укладкой', 'active'),
(3, 'Маникюр', '1200.00', 50, 'Классический маникюр с покрытием', 'active'),
(4, 'Педикюр', '1400.00', 70, 'Педикюр с массажем', 'active');

-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('master','client') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Users`
--

INSERT INTO `Users` (`id`, `username`, `password`, `role`) VALUES
(1, 'master', '1234', 'master'),
(2, 'client', '1234', 'client');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Индексы таблицы `Clients`
--
ALTER TABLE `Clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Индексы таблицы `MasterAppointments`
--
ALTER TABLE `MasterAppointments`
  ADD PRIMARY KEY (`master_id`,`time`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `Masters`
--
ALTER TABLE `Masters`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Services`
--
ALTER TABLE `Services`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Appointments`
--
ALTER TABLE `Appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `Clients`
--
ALTER TABLE `Clients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `Masters`
--
ALTER TABLE `Masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `Services`
--
ALTER TABLE `Services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `Clients` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `MasterAppointments`
--
ALTER TABLE `MasterAppointments`
  ADD CONSTRAINT `masterappointments_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `masterappointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
