-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Окт 30 2016 г., 17:14
-- Версия сервера: 10.1.16-MariaDB
-- Версия PHP: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `testbase`
--

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'ID пользователя',
  `login` varchar(20) COLLATE utf32_unicode_ci NOT NULL COMMENT 'Логин пользователя',
  `password` varchar(255) COLLATE utf32_unicode_ci NOT NULL COMMENT 'Пароль пользователя',
  `ip` varchar(12) COLLATE utf32_unicode_ci NOT NULL COMMENT 'IP пользователя',
  `dateOfRegister` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
  `email` varchar(255) COLLATE utf32_unicode_ci NOT NULL COMMENT 'Email, указанный при регистрации',
  `sex` set('Male','Female','','') COLLATE utf32_unicode_ci NOT NULL COMMENT 'Пол пользователя',
  `city` varchar(64) COLLATE utf32_unicode_ci NOT NULL COMMENT 'Город пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `ip`, `dateOfRegister`, `email`, `sex`, `city`) VALUES
(1, 'first', '$2y$10$j92MBmBk2CJorwYaTqUFcOK3XtOXJKPudFM2URVzOhn5J1MkEueCW', '192.168.1.1', '2016-10-29 14:54:32', 'email@email.com', 'Male', 'London'),
(2, 'second', '$2y$10$5IaX.c8YVAfOpSYlDTl6Eu2akI2EnFnzidmfcOK9vsoOz0ULTfBPy', '192.165.2.1', '2016-10-29 18:15:19', 'second@second.com', 'Female', 'London'),
(3, 'third', '$2y$10$y9Z2.CkTiyF6xBCtZsjx3umpdxGCNK3V/hVgUaB5pb6j.qUW62MYO', '198:1:1:1', '2016-10-29 19:38:24', 'third@third.com', 'Female', 'Moscow'),
(6, 'mister', '$2y$10$EIC5hnIGOIiE./i.flmFnurJZBxZ/48osoejMcziPqP1qLL5Mv3rC', '123:22:12:12', '2016-10-29 19:49:45', 'one@one.com', 'Male', 'SPB'),
(7, 'nomansk', '$2y$10$fDsDd36ViEF7n1vyJAYDjepW64r171x/2VhAHjXUsdhur9KE832PG', '1:1:1:1', '2016-10-30 19:07:14', 'hah@hah.com', 'Male', 'City'),
(8, 'JaneDoe', '$2y$10$R9Wlek4gvkzOQdeKIgpLxekag6xYY7Dsk1BUkjSkff0Ae8bKQpXHu', '::1', '2016-10-30 19:09:56', 'janedoe@example.com', 'Female', 'SomeCity');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID пользователя', AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
