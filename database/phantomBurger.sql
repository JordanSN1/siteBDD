-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 12 nov. 2024 à 09:18
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

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

-- --------------------------------------------------------

--
-- Structure de la table `boissons`
--

DROP TABLE IF EXISTS `boissons`;
CREATE TABLE IF NOT EXISTS `boissons` (
  `boisson_id` int NOT NULL AUTO_INCREMENT,
  `picture` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `prix` decimal(10,2) NOT NULL,
  PRIMARY KEY (`boisson_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `boissons`
--

INSERT INTO `boissons` (`boisson_id`, `picture`, `name`, `description`, `prix`) VALUES
(1, 'coca.jpg', 'Coca-Cola', 'Boisson gazeuse classique', '2.50'),
(2, 'sprite.jpg', 'Sprite', 'Boisson gazeuse au citron', '2.50'),
(3, 'fanta.jpg', 'Fanta', 'Boisson gazeuse à l\'orange', '2.50');

-- --------------------------------------------------------

--
-- Structure de la table `burgers`
--

DROP TABLE IF EXISTS `burgers`;
CREATE TABLE IF NOT EXISTS `burgers` (
  `burger_id` int NOT NULL AUTO_INCREMENT,
  `picture` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `prix` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`burger_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `burgers`
--

INSERT INTO `burgers` (`burger_id`, `picture`, `name`, `description`, `prix`) VALUES
(1, 'burger_classic.jpg', 'Classic Burger', 'Un burger intemporel avec steak juteux, fromage fondant, laitue croquante, tomates fraîches et sauce spéciale.', '8.99'),
(2, 'burger_cheese.jpg', 'Cheeseburger', 'Délicieux cheeseburger avec viande grillée, fromage fondant et légumes frais.', '9.49'),
(3, 'burger_bbq.jpg', 'BBQ Burger', 'Explosion de saveurs avec sauce barbecue, oignons caramélisés et bacon croustillant.', '10.99');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `commande_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `burger_id` int DEFAULT NULL,
  `quantite` int NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commande_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `burger_id` (`burger_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int NOT NULL AUTO_INCREMENT,
  `burger_id` int DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` int DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `burger_id` (`burger_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`comment_id`, `burger_id`, `username`, `comment_text`, `comment_date`, `rating`) VALUES
(21, 1, 'nabil', 'yum yum', '2024-11-10 12:34:49', 0),
(22, 1, 'nabil.saied', 'hhhhhhh', '2024-11-10 12:46:58', 0),
(23, 1, 'ilyas', 'yum yum c\'est trop bon', '2024-11-10 12:52:37', 5),
(24, 2, 'jordon', 'Leur burger est vraiment décevant.', '2024-11-10 13:02:09', 1),
(25, 3, 'nabil', 'c\'est trop dégeuleuse', '2024-11-12 09:13:28', 1),
(26, 3, 'nabil', 'c\'est pas hallal', '2024-11-12 09:17:59', 1),
(27, 3, 'nabil', 'c\'est du halouf', '2024-11-12 09:18:09', 1);

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `description` text,
  `prix` decimal(10,2) NOT NULL,
  `burger_id` int NOT NULL,
  `boisson_id` int NOT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `fk_burger` (`burger_id`),
  KEY `fk_boisson` (`boisson_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`menu_id`, `name`, `picture`, `description`, `prix`, `burger_id`, `boisson_id`) VALUES
(1, 'Menu Classique', NULL, 'Un menu avec un burger classique et une boisson gazeuse', '12.99', 1, 1),
(2, 'Menu Cheeseburger', NULL, 'Un cheeseburger accompagné d\'une boisson légère', '13.49', 2, 2),
(3, 'Menu BBQ', NULL, 'Un burger BBQ avec une boisson sucrée', '14.49', 3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `nom_role` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `nom_role` (`nom_role`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`role_id`, `nom_role`) VALUES
(1, 'Admin'),
(2, 'Modérateur'),
(3, 'Utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `utilisateur_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role_id` int DEFAULT '1',
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role_id`, `date_inscription`) VALUES
(1, 'Admin', 'Admin', 'Admin@phantomBurger.com', 'admin', 1, '2024-11-05 14:48:01'),
(2, 'Moderateur', 'Moderateur', 'Moderateur@phantomBurger.com', 'Moderateur', 1, '2024-11-05 14:48:01');
COMMIT;

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `message` text,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
