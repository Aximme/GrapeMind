-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 04 déc. 2024 à 19:32
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `grape-mind`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `name`, `date`) VALUES
(1, '\"DiVin chocolat\" dans les caves troglodytiques d\'Ackerman', NULL),
(2, 'Vivez la Percée du vin jaune dans le Jura !', NULL),
(3, 'Vignes Toquées : un week-end épicurien autour des costières-de-nîmes', NULL),
(4, 'Festival Les Grands Crus musicaux', NULL),
(5, 'Vitiloire à Tours : fête des vins en Val de Loire', NULL),
(6, 'Luberon En Tous Sens', NULL),
(7, 'Vendangeur d’un jour® en Champagne', NULL),
(8, 'La Côte chalonnaise vibre au rythme de la Paulée', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `event_reminders`
--

CREATE TABLE `event_reminders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `reminder_date` date NOT NULL,
  `email_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `event_reminders`
--

INSERT INTO `event_reminders` (`id`, `user_id`, `event_title`, `event_id`, `reminder_date`, `email_sent`) VALUES
(1, 1, '\"DiVin chocolat\" dans les caves troglodytiques d\'Ackerman', 1, '2024-12-20', 0),
(2, 1, '\"DiVin chocolat\" dans les caves troglodytiques d\'Ackerman', 1, '2024-12-18', 0),
(3, 1, '\"DiVin chocolat\" dans les caves troglodytiques d\'Ackerman', 1, '2025-01-16', 0),
(4, 1, 'Vivez la Percée du vin jaune dans le Jura !', 2, '2024-12-19', 0),
(5, 1, 'Vignes Toquées : un week-end épicurien autour des costières-de-nîmes', 3, '2025-01-18', 0),
(6, 1, 'Festival Les Grands Crus musicaux', 4, '2025-04-07', 0),
(7, 1, '\"DiVin chocolat\" dans les caves troglodytiques d\'Ackerman', 1, '2024-12-13', 0),
(8, 1, 'Vitiloire à Tours : fête des vins en Val de Loire', 5, '2025-09-18', 0),
(9, 1, 'Luberon En Tous Sens', 6, '2025-02-27', 0),
(10, 2, 'Festival Les Grands Crus musicaux', 4, '2025-02-01', 0),
(11, 2, 'Festival Les Grands Crus musicaux', 4, '2024-12-26', 0),
(12, 2, 'Festival Les Grands Crus musicaux', 4, '2024-12-25', 0),
(13, 2, '\"DiVin chocolat\" dans les caves troglodytiques d\'Ackerman', 1, '2025-01-16', 0),
(14, 2, '\"DiVin chocolat\" dans les caves troglodytiques d\'Ackerman', 1, '2025-02-06', 0),
(15, 2, 'Vendangeur d’un jour® en Champagne', 7, '2024-12-24', 0),
(16, 2, 'La Côte chalonnaise vibre au rythme de la Paulée', 8, '2024-12-27', 0),
(17, 2, 'Vivez la Percée du vin jaune dans le Jura !', 2, '2025-02-13', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_event_name` (`name`);

--
-- Index pour la table `event_reminders`
--
ALTER TABLE `event_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `event_reminders`
--
ALTER TABLE `event_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `event_reminders`
--
ALTER TABLE `event_reminders`
  ADD CONSTRAINT `event_reminders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_reminders_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
