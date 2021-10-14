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
-- Struktura tabulky `category`
--

CREATE TABLE `category` (
  `category_id` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
  `description` varchar(300) COLLATE utf8mb4_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci COMMENT='Kategorie poznámek';

-- --------------------------------------------------------

--
-- Struktura tabulky `note`
--

CREATE TABLE `note` (
  `note_id` int(10) UNSIGNED NOT NULL,
  `category_id` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
  `text` text COLLATE utf8mb4_czech_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Klíče pro tabulku `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
  MODIFY `category_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `notes`
--
ALTER TABLE `note`
  MODIFY `note_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `notes`
--
ALTER TABLE `note`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
