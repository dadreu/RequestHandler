-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3307
-- Время создания: Мар 13 2025 г., 02:26
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

-- --------------------------------------------------------

--
-- Структура таблицы `Appointments`
--

CREATE TABLE `Appointments` (
  `id` int NOT NULL,
  `master_id` int NOT NULL,
  `client_id` int NOT NULL,
  `service_id` int NOT NULL,
  `date_time` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Appointments`
--

INSERT INTO `Appointments` (`id`, `master_id`, `client_id`, `service_id`, `date_time`, `price`) VALUES
(16, 1, 5, 1, '2025-03-08 12:30:00', '1000.00'),
(17, 2, 6, 1, '2025-03-28 11:00:00', '1000.00'),
(18, 1, 6, 1, '2025-03-13 11:00:00', '1000.00'),
(19, 3, 6, 2, '2025-03-14 12:30:00', '1000.00'),
(20, 3, 5, 2, '2025-03-22 14:00:00', '1000.00'),
(21, 3, 7, 2, '2025-03-21 13:15:00', '1000.00');

-- --------------------------------------------------------

--
-- Структура таблицы `Clients`
--

CREATE TABLE `Clients` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Clients`
--

INSERT INTO `Clients` (`id`, `full_name`, `phone`) VALUES
(1, 'Иван Иванов', '+79001234567'),
(2, 'Анна Петрова', '+79007654321'),
(3, 'Сергей Смирнов', '+79009876543'),
(4, 'Мария Козлова', '+79005432198'),
(5, 'Драчёв Андрей Станиславович', '89125965744'),
(6, 'Женина Татьяна Олеговна', '89125965742'),
(7, 'Женина Татьяна Олеговна', '8956845733');

-- --------------------------------------------------------

--
-- Структура таблицы `Masters`
--

CREATE TABLE `Masters` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Masters`
--

INSERT INTO `Masters` (`id`, `full_name`, `phone`) VALUES
(1, 'Алексей Сидоров', '+79001112233'),
(2, 'Екатерина Морозова', '+79002223344'),
(3, 'Дмитрий Орлов', '+79003334455');

-- --------------------------------------------------------

--
-- Структура таблицы `MasterSchedule`
--

CREATE TABLE `MasterSchedule` (
  `id` int NOT NULL,
  `master_id` int NOT NULL,
  `day_of_week` enum('Пн','Вт','Ср','Чт','Пт','Сб','Вс') NOT NULL,
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
(70, 2, 'Пн', '09:00:00', '18:00:00'),
(71, 2, 'Вт', '09:00:00', '18:00:00'),
(72, 2, 'Ср', '09:00:00', '18:00:00'),
(73, 2, 'Чт', '09:00:00', '18:00:00'),
(74, 2, 'Пт', '09:00:00', '18:00:00'),
(75, 2, 'Сб', '09:00:00', '18:00:00'),
(76, 2, 'Вс', '09:00:00', '18:00:00'),
(77, 1, 'Пн', '09:00:00', '18:00:00'),
(78, 1, 'Вт', '09:00:00', '18:00:00'),
(79, 1, 'Ср', '09:00:00', '18:00:00'),
(80, 1, 'Чт', '09:00:00', '14:00:00'),
(81, 1, 'Пт', '09:00:00', '18:00:00'),
(82, 1, 'Сб', '09:00:00', '18:00:00'),
(83, 1, 'Вс', '09:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `Services`
--

CREATE TABLE `Services` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `description` text NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Services`
--

INSERT INTO `Services` (`id`, `name`, `price`, `duration`, `description`, `status`) VALUES
(1, 'Стрижка мужская', '1000.00', 40, 'Классическая мужская стрижка', 'active'),
(2, 'Стрижка женская', '1500.00', 60, 'Женская стрижка любой сложности', 'active'),
(3, 'Окрашивание волос', '3000.00', 120, 'Окрашивание в один тон или сложные техники', 'active'),
(4, 'Маникюр', '1200.00', 90, 'Классический и аппаратный маникюр', 'active'),
(5, 'Педикюр', '1800.00', 90, 'Гигиенический и аппаратный педикюр', 'active'),
(6, 'Массаж лица', '800.00', 30, 'Расслабляющий массаж лица', 'inactive');

-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','master','client') NOT NULL,
  `master_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Users`
--

INSERT INTO `Users` (`id`, `username`, `password`, `role`, `master_id`, `client_id`) VALUES
(1, 'admin', 'admin123', 'admin', NULL, NULL),
(2, 'master1', 'password1', 'master', 1, NULL),
(3, 'master2', 'password2', 'master', 2, NULL),
(4, 'master3', 'password3', 'master', 3, NULL),
(5, 'client1', 'clientpass1', 'client', NULL, 1),
(6, 'client2', 'clientpass2', 'client', NULL, 2),
(7, 'client3', 'clientpass3', 'client', NULL, 3),
(8, 'client4', 'clientpass4', 'client', NULL, 4),
(13, 'client_zhenina', '5678', 'client', NULL, 7),
(14, 'client_drachev', '1234', 'client', NULL, 5),
(15, 'client_zhenina2', '5678', 'client', NULL, 6);

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
  ADD UNIQUE KEY `phone` (`phone`);

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
-- Индексы таблицы `Services`
--
ALTER TABLE `Services`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `client_id` (`client_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Appointments`
--
ALTER TABLE `Appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT для таблицы `Clients`
--
ALTER TABLE `Clients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `Masters`
--
ALTER TABLE `Masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT для таблицы `Services`
--
ALTER TABLE `Services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Appointments`
--
ALTER TABLE `Appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `Clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  ADD CONSTRAINT `masterschedule_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `Clients` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
