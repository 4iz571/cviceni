
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Struktura tabulky `cart`
--

CREATE TABLE `cart` (
                        `cart_id` int(11) NOT NULL,
                        `user_id` int(11) DEFAULT NULL,
                        `last_modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `cart_item`
--

CREATE TABLE `cart_item` (
                             `cart_item_id` int(11) NOT NULL,
                             `product_id` int(11) NOT NULL,
                             `cart_id` int(11) NOT NULL,
                             `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

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
-- Struktura tabulky `forgotten_password`
--

CREATE TABLE `forgotten_password` (
                                      `forgotten_password_id` int(11) NOT NULL,
                                      `user_id` int(11) NOT NULL,
                                      `code` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL,
                                      `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `permission`
--

CREATE TABLE `permission` (
                              `permission_id` int(11) NOT NULL,
                              `role_id` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL,
                              `resource_id` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL,
                              `action` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
                              `type` set('allow','deny') COLLATE utf8mb4_czech_ci NOT NULL DEFAULT 'allow'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `permission`
--

INSERT INTO `permission` (`permission_id`, `role_id`, `resource_id`, `action`, `type`) VALUES
(22, 'admin', 'Admin:Category', '', 'allow'),
(21, 'admin', 'Admin:Dashboard', '', 'allow'),
(24, 'admin', 'Admin:Product', '', 'allow'),
(12, 'admin', 'Category', '', 'allow'),
(23, 'admin', 'Product', '', 'allow'),
(28, 'authenticated', 'Admin:Product', '', 'allow'),
(36, 'authenticated', 'Front:Cart', '', 'allow'),
(4, 'authenticated', 'Front:Error', '', 'allow'),
(5, 'authenticated', 'Front:Error4xx', '', 'allow'),
(6, 'authenticated', 'Front:Homepage', '', 'allow'),
(34, 'authenticated', 'Front:Product', '', 'allow'),
(9, 'authenticated', 'Front:User', 'login', 'allow'),
(10, 'authenticated', 'Front:User', 'logout', 'allow'),
(27, 'authenticated', 'Product', '', 'allow'),
(26, 'guest', 'Admin:Product', '', 'allow'),
(35, 'guest', 'Front:Cart', '', 'allow'),
(1, 'guest', 'Front:Error', '', 'allow'),
(2, 'guest', 'Front:Error4xx', '', 'allow'),
(3, 'guest', 'Front:Homepage', '', 'allow'),
(33, 'guest', 'Front:Product', '', 'allow'),
(15, 'guest', 'Front:User', 'facebookLogin', 'allow'),
(13, 'guest', 'Front:User', 'forgottenPassword', 'allow'),
(7, 'guest', 'Front:User', 'login', 'allow'),
(8, 'guest', 'Front:User', 'logout', 'allow'),
(11, 'guest', 'Front:User', 'register', 'allow'),
(14, 'guest', 'Front:User', 'renewPassword', 'allow'),
(25, 'guest', 'Product', '', 'allow');

-- --------------------------------------------------------

--
-- Struktura tabulky `product`
--

CREATE TABLE `product` (
                           `product_id` int(11) NOT NULL,
                           `category_id` smallint(5) UNSIGNED DEFAULT NULL,
                           `title` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
                           `url` varchar(100) COLLATE utf8mb4_czech_ci NOT NULL,
                           `description` text COLLATE utf8mb4_czech_ci NOT NULL,
                           `price` decimal(10,2) NOT NULL,
                           `photo_extension` varchar(10) COLLATE utf8mb4_czech_ci NOT NULL,
                           `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci COMMENT='Tabulka s nabízenými produkty';

--
-- Vypisuji data pro tabulku `product`
--

INSERT INTO `product` (`product_id`, `category_id`, `title`, `url`, `description`, `price`, `photo_extension`, `available`) VALUES
(1, NULL, 'aTestovací produkt2', 'testovaci-produkt', 'Lorem ipsum...', '100.00', '', 1),
(3, NULL, 'test', 'test', '+++', '11.00', 'jpeg', 1),
(6, NULL, 'test', 'testx', 'qaa', '1.00', '', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `resource`
--

CREATE TABLE `resource` (
    `resource_id` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci COMMENT='Tabulka obsahující seznam zdrojů';

--
-- Vypisuji data pro tabulku `resource`
--

INSERT INTO `resource` (`resource_id`) VALUES
('Admin:Category'),
('Admin:Dashboard'),
('Admin:Error4xx'),
('Admin:Product'),
('Category'),
('Front:Cart'),
('Front:Error'),
('Front:Error4xx'),
('Front:Homepage'),
('Front:Product'),
('Front:User'),
('Product');

-- --------------------------------------------------------

--
-- Struktura tabulky `role`
--

CREATE TABLE `role` (
    `role_id` varchar(50) COLLATE utf8mb4_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Vypisuji data pro tabulku `role`
--

INSERT INTO `role` (`role_id`) VALUES
('admin'),
('authenticated'),
('guest');

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
                        `user_id` int(11) NOT NULL,
                        `name` varchar(40) COLLATE utf8mb4_czech_ci NOT NULL,
                        `email` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
                        `facebook_id` varchar(100) COLLATE utf8mb4_czech_ci DEFAULT NULL,
                        `role_id` varchar(50) COLLATE utf8mb4_czech_ci DEFAULT NULL,
                        `password` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci COMMENT='Tabulka s daty uživatelů';

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `cart`
--
ALTER TABLE `cart`
    ADD PRIMARY KEY (`cart_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `cart_item`
--
ALTER TABLE `cart_item`
    ADD PRIMARY KEY (`cart_item_id`),
    ADD UNIQUE KEY `product_id` (`product_id`,`cart_id`),
    ADD KEY `cart_id` (`cart_id`);

--
-- Klíče pro tabulku `category`
--
ALTER TABLE `category`
    ADD PRIMARY KEY (`category_id`);

--
-- Klíče pro tabulku `forgotten_password`
--
ALTER TABLE `forgotten_password`
    ADD PRIMARY KEY (`forgotten_password_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `permission`
--
ALTER TABLE `permission`
    ADD PRIMARY KEY (`permission_id`),
    ADD UNIQUE KEY `role_id` (`role_id`,`resource_id`,`action`,`type`),
    ADD KEY `permission_ibfk_1` (`resource_id`);

--
-- Klíče pro tabulku `product`
--
ALTER TABLE `product`
    ADD PRIMARY KEY (`product_id`),
    ADD UNIQUE KEY `url` (`url`),
    ADD KEY `category_id` (`category_id`),
    ADD KEY `available` (`available`);

--
-- Klíče pro tabulku `resource`
--
ALTER TABLE `resource`
    ADD PRIMARY KEY (`resource_id`);

--
-- Klíče pro tabulku `role`
--
ALTER TABLE `role`
    ADD PRIMARY KEY (`role_id`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `email` (`email`),
    ADD UNIQUE KEY `facebook_id` (`facebook_id`),
    ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `cart`
--
ALTER TABLE `cart`
    MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `cart_item`
--
ALTER TABLE `cart_item`
    MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
    MODIFY `category_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `forgotten_password`
--
ALTER TABLE `forgotten_password`
    MODIFY `forgotten_password_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `permission`
--
ALTER TABLE `permission`
    MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pro tabulku `product`
--
ALTER TABLE `product`
    MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `cart`
--
ALTER TABLE `cart`
    ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `cart_item`
--
ALTER TABLE `cart_item`
    ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `forgotten_password`
--
ALTER TABLE `forgotten_password`
    ADD CONSTRAINT `forgotten_password_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `permission`
--
ALTER TABLE `permission`
    ADD CONSTRAINT `permission_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`resource_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `permission_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `product`
--
ALTER TABLE `product`
    ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL;

--
-- Omezení pro tabulku `user`
--
ALTER TABLE `user`
    ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;
