-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost
-- Verze serveru: 10.3.22-MariaDB-log
-- Verze PHP: 7.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Struktura tabulky `prispevky`
--

CREATE TABLE `prispevky` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` varchar(300) COLLATE utf8mb4_czech_ci NOT NULL,
  `autor` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prispevky`
--
ALTER TABLE `prispevky`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prispevky`
--
ALTER TABLE `prispevky`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
