-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3307
-- Время создания: Мар 13 2025 г., 18:53
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
(21, 3, 7, 2, '2025-03-21 13:15:00', '1000.00'),
(22, 1, 8, 1, '2025-03-30 17:00:00', '1000.00'),
(23, 2, 9, 5, '2025-03-21 12:15:00', '1000.00');

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
(7, 'Женина Татьяна Олеговна', '8956845733'),
(8, 'Драчёв Андрей Дмитриевич', '89125965745'),
(9, 'Драчёв Андрей Григорьевич', '89125965767');

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
(98, 1, 'Пн', '09:15:00', '18:00:00'),
(99, 1, 'Вт', '09:30:00', '18:00:00'),
(100, 1, 'Ср', '09:45:00', '18:00:00'),
(101, 1, 'Чт', '09:00:00', '14:00:00'),
(102, 1, 'Пт', '09:15:00', '18:00:00'),
(103, 1, 'Сб', '09:30:00', '18:00:00'),
(104, 1, 'Вс', '09:45:00', '18:00:00'),
(105, 2, 'Пн', '09:00:00', '18:00:00'),
(106, 2, 'Вт', '11:30:00', '18:00:00'),
(107, 2, 'Ср', '11:00:00', '18:00:00'),
(108, 2, 'Чт', '09:00:00', '18:00:00'),
(109, 2, 'Пт', '13:45:00', '18:00:00'),
(110, 2, 'Сб', '09:00:00', '18:00:00'),
(111, 2, 'Вс', '09:00:00', '18:00:00');

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
(1, 1, 1, '1000.00', 40, 1),
(2, 2, 1, '1200.00', 45, 0),
(3, 3, 2, '1500.00', 60, 1),
(4, 1, 4, '1200.00', 90, 1),
(5, 2, 3, '3000.00', 120, 1);

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
(6, 'Массаж лица', 'Расслабляющий массаж лица');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `Clients`
--
ALTER TABLE `Clients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `Masters`
--
ALTER TABLE `Masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `MasterSchedule`
--
ALTER TABLE `MasterSchedule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT для таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Ограничения внешнего ключа таблицы `MasterServices`
--
ALTER TABLE `MasterServices`
  ADD CONSTRAINT `masterservices_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `Masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `masterservices_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `Services` (`id`) ON DELETE CASCADE;

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
