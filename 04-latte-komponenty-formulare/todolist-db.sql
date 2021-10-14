-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Struktura tabulky `tag`
--

CREATE TABLE `tag` (
                       `tag_id` int(11) NOT NULL,
                       `title` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci COMMENT='Tabulka obsahující tagy pro označování úkolů';

--
-- Vypisuji data pro tabulku `tag`
--

INSERT INTO `tag` (`tag_id`, `title`) VALUES
(1, 'Důležité'),
(3, 'Práce'),
(2, 'Škola');

-- --------------------------------------------------------

--
-- Struktura tabulky `todo`
--

CREATE TABLE `todo` (
                        `todo_id` int(11) NOT NULL,
                        `title` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
                        `description` text COLLATE utf8mb4_czech_ci NOT NULL,
                        `deadline` date DEFAULT NULL,
                        `completed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci COMMENT='Tabulka obsahující jednotlivé úkoly';

--
-- Vypisuji data pro tabulku `todo`
--

INSERT INTO `todo` (`todo_id`, `title`, `description`, `deadline`, `completed`) VALUES
(1, 'Todo 1', 'Lorem ipsum...', NULL, 0),
(2, 'Todo 2', '', NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `todo_item`
--

CREATE TABLE `todo_item` (
                             `todo_item_id` int(11) NOT NULL,
                             `todo_id` int(11) NOT NULL,
                             `title` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
                             `completed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `todo_tag`
--

CREATE TABLE `todo_tag` (
                            `todo_id` int(11) NOT NULL,
                            `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `todo_tag`
--

INSERT INTO `todo_tag` (`todo_id`, `tag_id`) VALUES
(1, 1),
(1, 3);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `tag`
--
ALTER TABLE `tag`
    ADD PRIMARY KEY (`tag_id`),
    ADD UNIQUE KEY `title` (`title`);

--
-- Klíče pro tabulku `todo`
--
ALTER TABLE `todo`
    ADD PRIMARY KEY (`todo_id`);

--
-- Klíče pro tabulku `todo_item`
--
ALTER TABLE `todo_item`
    ADD PRIMARY KEY (`todo_item_id`),
    ADD KEY `todo_id` (`todo_id`);

--
-- Klíče pro tabulku `todo_tag`
--
ALTER TABLE `todo_tag`
    ADD PRIMARY KEY (`todo_id`,`tag_id`),
    ADD KEY `tag_id` (`tag_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `tag`
--
ALTER TABLE `tag`
    MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `todo`
--
ALTER TABLE `todo`
    MODIFY `todo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `todo_item`
--
ALTER TABLE `todo_item`
    MODIFY `todo_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `todo_item`
--
ALTER TABLE `todo_item`
    ADD CONSTRAINT `todo_item_ibfk_1` FOREIGN KEY (`todo_id`) REFERENCES `todo` (`todo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `todo_tag`
--
ALTER TABLE `todo_tag`
    ADD CONSTRAINT `todo_tag_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `todo_tag_ibfk_2` FOREIGN KEY (`todo_id`) REFERENCES `todo` (`todo_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
