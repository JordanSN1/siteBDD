-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 03 déc. 2024 à 09:39
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26


CREATE DATABASE IF NOT EXISTS `phantomburger` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `phantomburger`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `phantomburger`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `UpdateStockForMenuOrder`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateStockForMenuOrder` (IN `order_id` INT)   BEGIN
    -- Mise à jour des stocks des burgers pour les menus commandés
    UPDATE burgers sb
    JOIN burgers_appartient ba ON sb.burger_id = ba.burger_id
    JOIN commandes_details cd ON ba.menu_id = cd.menu_id
    SET sb.stock = sb.stock - cd.quantite
    WHERE cd.commande_id = order_id;

    -- Mise à jour des stocks des boissons pour les menus commandés
    UPDATE boissons sb
    JOIN boissons_appartient ba ON sb.boisson_id = ba.boisson_id
    JOIN commandes_details cd ON ba.menu_id = cd.menu_id
    SET sb.stock = sb.stock - cd.quantite
    WHERE cd.commande_id = order_id;

    -- Vérification des stocks négatifs pour les burgers
    IF EXISTS (
        SELECT 1
        FROM burgers
        WHERE stock < 0
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuffisant pour un ou plusieurs burgers.';
    END IF;

    -- Vérification des stocks négatifs pour les boissons
    IF EXISTS (
        SELECT 1
        FROM boissons
        WHERE stock < 0
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuffisant pour une ou plusieurs boissons.';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `boissons`
--

DROP TABLE IF EXISTS `boissons`;
CREATE TABLE IF NOT EXISTS `boissons` (
  `boisson_id` int NOT NULL AUTO_INCREMENT,
  `picture` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `prix` decimal(15,2) DEFAULT NULL,
  `stock` int DEFAULT '0',
  PRIMARY KEY (`boisson_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `boissons`
--

INSERT INTO `boissons` (`boisson_id`, `picture`, `name`, `description`, `prix`, `stock`) VALUES
(1, 'coca.jpg', 'Coca-Cola', 'Boisson gazeuse classique', '2.50', 600),
(2, 'sprite.jpg', 'Sprite', 'Boisson gazeuse au citron', '2.50', 698),
(3, 'fanta.jpg', 'Fanta', 'Boisson gazeuse à l\'orange', '2.50', 895);

-- --------------------------------------------------------

--
-- Structure de la table `boissons_appartient`
--

DROP TABLE IF EXISTS `boissons_appartient`;
CREATE TABLE IF NOT EXISTS `boissons_appartient` (
  `boisson_id` int NOT NULL,
  `menu_id` int NOT NULL,
  PRIMARY KEY (`boisson_id`,`menu_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `boissons_appartient`
--

INSERT INTO `boissons_appartient` (`boisson_id`, `menu_id`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `burgers`
--

DROP TABLE IF EXISTS `burgers`;
CREATE TABLE IF NOT EXISTS `burgers` (
  `burger_id` int NOT NULL AUTO_INCREMENT,
  `picture` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `prix` decimal(15,2) DEFAULT NULL,
  `description` text,
  `stock` int DEFAULT '0',
  PRIMARY KEY (`burger_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `burgers`
--

INSERT INTO `burgers` (`burger_id`, `picture`, `name`, `prix`, `description`, `stock`) VALUES
(1, 'burger_classic.jpg', 'Classic Burger', '21.99', 'Un burger intemporel avec steak juteux, fromage fondant, laitue croquante, tomates fraîches et sauce spéciale.', 600),
(2, 'burger_cheese.jpg', 'Cheeseburger', '24.99', 'Délicieux cheeseburger avec viande grillée, fromage fondant et légumes frais.', 697),
(3, 'burger_bbq.jpg', 'BBQ Burger', '25.99', 'Explosion de saveurs avec sauce barbecue, oignons caramélisés et bacon croustillant.', 896);

-- --------------------------------------------------------

--
-- Structure de la table `burgers_appartient`
--

DROP TABLE IF EXISTS `burgers_appartient`;
CREATE TABLE IF NOT EXISTS `burgers_appartient` (
  `burger_id` int NOT NULL,
  `menu_id` int NOT NULL,
  PRIMARY KEY (`burger_id`,`menu_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `burgers_appartient`
--

INSERT INTO `burgers_appartient` (`burger_id`, `menu_id`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `commandes_details`
--

DROP TABLE IF EXISTS `commandes_details`;
CREATE TABLE IF NOT EXISTS `commandes_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int NOT NULL,
  `menu_id` int DEFAULT NULL,
  `quantite` int NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `boisson_id` int DEFAULT NULL,
  `burger_id` int DEFAULT NULL,
  `utilisateur_id_` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`),
  KEY `menu_id` (`menu_id`),
  KEY `fk_boisson_id` (`boisson_id`),
  KEY `fk_burger_id` (`burger_id`),
  KEY `fk_utilisateur_id` (`utilisateur_id_`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commandes_details`
--

INSERT INTO `commandes_details` (`id`, `commande_id`, `menu_id`, `quantite`, `prix_unitaire`, `boisson_id`, `burger_id`, `utilisateur_id_`, `timestamp`) VALUES
(58, 48, 2, 1, '12.99', NULL, NULL, 3, '2024-12-03 00:42:22'),
(57, 47, NULL, 1, '2.50', 2, NULL, 3, '2024-12-03 00:37:21'),
(56, 47, 2, 1, '12.99', NULL, NULL, 3, '2024-12-03 00:37:21'),
(59, 48, NULL, 1, '2.50', 2, NULL, 3, '2024-12-03 00:42:22'),
(60, 48, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 00:42:22'),
(61, 48, NULL, 1, '24.99', NULL, 2, 3, '2024-12-03 00:42:22'),
(62, 49, 2, 1, '12.99', NULL, NULL, 3, '2024-12-03 00:49:01'),
(63, 49, NULL, 1, '2.50', 2, NULL, 3, '2024-12-03 00:49:01'),
(64, 49, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 00:49:01'),
(65, 49, NULL, 1, '24.99', NULL, 2, 3, '2024-12-03 00:49:01'),
(66, 50, 2, 1, '12.99', NULL, NULL, 3, '2024-12-03 00:59:02'),
(67, 50, NULL, 1, '2.50', 2, NULL, 3, '2024-12-03 00:59:02'),
(68, 50, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 00:59:02'),
(69, 50, NULL, 1, '24.99', NULL, 2, 3, '2024-12-03 00:59:02'),
(70, 51, 2, 1, '12.99', NULL, NULL, 3, '2024-12-03 01:00:37'),
(71, 52, 2, 1, '12.99', NULL, NULL, 3, '2024-12-03 01:03:09'),
(72, 53, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 01:03:58'),
(73, 54, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 01:06:07'),
(74, 55, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 01:12:24'),
(75, 56, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 01:13:57'),
(76, 57, 3, 1, '13.99', NULL, NULL, 3, '2024-12-03 08:59:47'),
(77, 57, NULL, 1, '2.50', 3, NULL, 3, '2024-12-03 08:59:47');

--
-- Déclencheurs `commandes_details`
--
DROP TRIGGER IF EXISTS `update_boisson_stock_after_insert`;
DELIMITER $$
CREATE TRIGGER `update_boisson_stock_after_insert` AFTER INSERT ON `commandes_details` FOR EACH ROW BEGIN
    -- Vérifier si le boisson_id est non nul (cela devrait toujours être le cas si les données sont valides)
    IF NEW.boisson_id IS NOT NULL THEN
        -- Mettre à jour le stock de la boisson en soustrayant la quantité commandée
        UPDATE boissons
        SET stock = stock - NEW.quantite
        WHERE boisson_id = NEW.boisson_id;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_burger_stock_after_insert`;
DELIMITER $$
CREATE TRIGGER `update_burger_stock_after_insert` AFTER INSERT ON `commandes_details` FOR EACH ROW BEGIN
    -- Vérifier si le burger_id est non nul (cela devrait toujours être le cas si les données sont valides)
    IF NEW.burger_id IS NOT NULL THEN
        -- Mettre à jour le stock du burger en soustrayant la quantité commandée
        UPDATE burgers
        SET stock = stock - NEW.quantite
        WHERE burger_id = NEW.burger_id;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_menu_stock_after_insert`;
DELIMITER $$
CREATE TRIGGER `update_menu_stock_after_insert` AFTER INSERT ON `commandes_details` FOR EACH ROW BEGIN
    -- Vérifier si le boisson_id est non nul (cela devrait toujours être le cas si les données sont valides)
    IF NEW.menu_id IS NOT NULL THEN
        -- Mettre à jour le stock de la boisson en soustrayant la quantité commandée
        UPDATE menus
        SET stock = stock - NEW.quantite
        WHERE menu_id = NEW.menu_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `comment_text` text,
  `comment_date` datetime DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `burger_id` int NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `burger_id` (`burger_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `contact_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `datemsg` datetime DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `est_commander`
--

DROP TABLE IF EXISTS `est_commander`;
CREATE TABLE IF NOT EXISTS `est_commander` (
  `burger_id` int NOT NULL,
  `commande_id` int NOT NULL,
  PRIMARY KEY (`burger_id`,`commande_id`),
  KEY `commande_id` (`commande_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `description` text,
  `prix` decimal(15,2) DEFAULT NULL,
  `stock` int DEFAULT '0',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`menu_id`, `name`, `picture`, `description`, `prix`, `stock`) VALUES
(1, 'Menu Classic', 'menu_classic.jpg', 'Un menu comprenant le Classic Burger et une boisson Coca-Cola.', '11.99', 600),
(2, 'Menu Cheeseburger', 'menu_cheese.jpg', 'Un menu comprenant le Cheeseburger et une boisson Sprite.', '12.99', 696),
(3, 'Menu BBQ', 'menu_bbq.jpg', 'Un menu comprenant le BBQ Burger et une boisson Fanta.', '13.99', 893);

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id_transaction` int NOT NULL AUTO_INCREMENT,
  `titulaire_carte` varchar(255) NOT NULL,
  `numero_carte` varchar(16) NOT NULL,
  `type_carte` varchar(50) NOT NULL,
  `date_expiration` date NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `commande_id` int NOT NULL,
  `utilisateur_id_` int NOT NULL,
  PRIMARY KEY (`id_transaction`),
  KEY `utilisateur_id_` (`utilisateur_id_`),
  KEY `commande_id` (`commande_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id_transaction`, `titulaire_carte`, `numero_carte`, `type_carte`, `date_expiration`, `cvv`, `commande_id`, `utilisateur_id_`) VALUES
(1, 'Berlin', '2313244243424242', 'Visa', '2024-12-02', '234', 15, 3),
(2, 'Nabil ', '2323231313131313', 'Visa', '2024-12-03', '121', 16, 3),
(8, 'Berlin', '1216512313213212', 'Visa', '2024-12-10', '444', 22, 3),
(9, 'Berlin', '2323232323131313', 'Visa', '2024-12-02', '232', 23, 3),
(10, 'Dos Santos Manuel', '1212121212121212', 'Visa', '2024-12-03', '323', 24, 3),
(11, 'Dos Santos Manuel', '2313213131313131', 'Visa', '2024-12-12', '233', 33, 3),
(12, 'Bonn', '3424242424242444', 'Visa', '2024-12-12', '232', 34, 3),
(13, 'Dos Santos Manuel', '2232323232323232', 'Visa', '2024-12-04', '232', 35, 3),
(14, 'Berlin', '2312352123212211', 'RuPay', '2024-12-13', '242', 36, 3),
(15, 'Dos Santos Manuel', '1212121212121212', 'Visa', '2024-12-12', '212', 37, 3),
(16, 'Berlin', '2323232323233232', 'Visa', '2024-12-04', '232', 38, 3),
(17, 'Berlin', '2323232323233232', 'Visa', '2024-12-04', '232', 39, 3),
(18, 'Bonn', '2323232323232323', 'Visa', '2024-12-10', '232', 40, 3),
(19, 'Berlin', '1212121212121212', 'Visa', '2024-12-11', '232', 41, 3),
(20, 'Dos Santos Manuel', '7777777777777777', 'RuPay', '2024-12-19', '232', 42, 3),
(21, 'Dos Santos Manuel', '1323232323232312', 'Visa', '2024-12-05', '112', 46, 3),
(22, 'Dos Santos Manuel', '2323232323232323', 'Visa', '2024-12-13', '232', 47, 3),
(23, 'Dos Santos Manuel', '1212132313231321', 'Visa', '2024-12-11', '121', 48, 3),
(24, 'Dos Santos Manuel', '2323232323232323', 'Visa', '2024-12-04', '322', 49, 3),
(25, 'Dos Santos Manuel', '2323232323232332', 'Visa', '2024-12-12', '232', 50, 3),
(26, 'Dos Santos Manuel', '2323232323232332', 'Visa', '2024-12-11', '212', 51, 3),
(27, 'Berlin', '2323232323232323', 'Visa', '2024-12-19', '232', 52, 3),
(28, 'Dos Santos Manuel', '2323232323232323', 'Visa', '2024-12-20', '232', 53, 3),
(29, 'Dos Santos Manuel', '2323232323232323', 'Visa', '2024-12-12', '232', 54, 3),
(30, 'Bonn', '1212121212121212', 'RuPay', '2024-12-20', '212', 55, 3),
(31, 'Berlin', '2323232323232323', 'Visa', '2024-12-04', '223', 56, 3),
(32, 'Saied Nabile', '2323232323293923', 'RuPay', '2024-12-11', '232', 57, 3);

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `utilisateur_id_` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `date_inscription` datetime DEFAULT NULL,
  `role_id` int NOT NULL,
  PRIMARY KEY (`utilisateur_id_`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id_`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`, `role_id`) VALUES
(1, 'Admin', 'Admin', 'admin@phantomburger.com', 'admin', '2024-11-28 10:43:10', 1),
(2, 'Modérateur', 'Modérateur', 'moderateur@phantomburger.com', 'moderateur', '2024-11-28 10:43:10', 2),
(3, 'Manuel', 'Dos Santos', 'm.dossantosataide@ecoles-epsi.net', '$2y$10$xBJ4NWi3H/zV/Uvv1Jz3HuLg29zIr2ZKKhrNIOscEmiLBshvzZ3.2', '2024-12-02 21:21:43', 3);
--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'moderateur'),
(3, 'utilisateur');
-- --------------------------------------------------------

