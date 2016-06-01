-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 01 Juin 2016 à 16:45
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `train-commander-bdd`
--

-- --------------------------------------------------------

--
-- Structure de la table `connections`
--

CREATE TABLE IF NOT EXISTS `connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` timestamp NOT NULL,
  `stationid` int(11) NOT NULL,
  `segmentid` int(11) NOT NULL,
  `pathid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `connections`
--

INSERT INTO `connections` (`id`, `start_time`, `stationid`, `segmentid`, `pathid`) VALUES
(1, '2016-06-30 05:00:00', 1, 1, 1),
(2, '2016-06-30 05:15:00', 2, 3, 1),
(4, '2016-06-30 07:00:00', 10, 11, 9),
(6, '2016-06-30 09:30:00', 30, 8, 6),
(7, '2016-06-30 09:40:00', 33, 7, 6),
(8, '2016-06-30 07:25:00', 10, 5, 10),
(9, '2016-06-30 08:45:00', 20, 6, 11),
(10, '2016-06-30 05:30:00', 1, 1, 12),
(11, '2016-06-30 05:40:00', 2, 3, 12),
(12, '2016-06-30 06:00:00', 1, 1, 13),
(13, '2016-06-30 06:10:00', 2, 3, 13),
(14, '2016-06-30 06:30:00', 1, 1, 14),
(15, '2016-06-30 06:40:00', 2, 3, 14),
(16, '2016-06-30 14:00:00', 1, 1, 15),
(17, '2016-06-30 14:10:00', 2, 3, 15);

-- --------------------------------------------------------

--
-- Structure de la table `employee`
--

CREATE TABLE IF NOT EXISTS `employee` (
  `id_employee` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_employee`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Contenu de la table `employee`
--

INSERT INTO `employee` (`id_employee`, `firstname`, `lastname`, `email`, `password`) VALUES
(1, 'Pierre', 'JOUGLET', 'Pierre.JOUGLET@supinfo.com', '085c946de284abbf45f0d14c0afd8a0bb8ad8860'),
(2, 'TEST', 'TEST', 'unmondesansmensonges@gmail.com', '1038a789486efdc0a2c1b59e8255ea95e15fce34');

-- --------------------------------------------------------

--
-- Structure de la table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cost` double NOT NULL,
  `start_time` timestamp NOT NULL,
  `end_time` timestamp NOT NULL,
  `start_station` varchar(50) NOT NULL,
  `end_station` varchar(50) NOT NULL,
  `userid` int(11) NOT NULL,
  `command_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `history`
--

INSERT INTO `history` (`id`, `cost`, `start_time`, `end_time`, `start_station`, `end_station`, `userid`, `command_time`) VALUES
(1, 99999, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Valenciennes', 'Metal%20Land', 2, '2016-05-31 14:17:50'),
(2, 99999, '0000-00-00 00:00:00', '2016-06-29 22:00:00', 'Valenciennes', 'Metal Land', 2, '2016-05-31 14:17:50'),
(3, 99999, '0000-00-00 00:00:00', '2016-06-29 22:00:00', 'Valenciennes', 'Metal Land', 2, '2016-05-31 14:17:50'),
(4, 99999, '1970-01-01 00:00:01', '2016-06-29 22:00:00', 'Valenciennes', 'Metal Land', 2, '2016-05-31 14:17:50'),
(5, 99999, '1970-01-01 00:00:01', '2016-06-30 04:13:22', 'Valenciennes', 'Metal Land', 2, '2016-05-31 14:17:50');

-- --------------------------------------------------------

--
-- Structure de la table `paths`
--

CREATE TABLE IF NOT EXISTS `paths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_national` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Contenu de la table `paths`
--

INSERT INTO `paths` (`id`, `is_national`, `label`) VALUES
(1, 0, 'Valenciennes - Lille'),
(2, 1, 'Lille Paris'),
(3, 1, 'Paris Marseille'),
(4, 0, 'Valenciennes Denain'),
(5, 0, 'Denain Saint-Amand-les-Eaux'),
(6, 0, 'Marseille La Ciotat'),
(7, 0, 'Marseille Aubagne'),
(8, 0, 'Aubagne La Ciotat'),
(9, 1, 'Lille Marseille'),
(10, 1, 'Lille Paris'),
(11, 1, 'Paris Marseille'),
(12, 0, 'Valenciennes - Lille 7h30'),
(13, 0, 'Valenciennes - Lille 8h00'),
(14, 0, 'Valenciennes - Lille 8h30'),
(15, 0, 'Valenciennes - Lille 16h00');

-- --------------------------------------------------------

--
-- Structure de la table `segments`
--

CREATE TABLE IF NOT EXISTS `segments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cost` double NOT NULL,
  `duree` int(11) NOT NULL,
  `start_stationid` int(11) NOT NULL,
  `end_stationid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Contenu de la table `segments`
--

INSERT INTO `segments` (`id`, `cost`, `duree`, `start_stationid`, `end_stationid`) VALUES
(1, 2, 600, 1, 2),
(2, 3, 900, 1, 3),
(3, 6, 1800, 2, 10),
(4, 8, 900, 2, 3),
(5, 10, 3600, 10, 20),
(6, 20, 10800, 20, 30),
(7, 4, 600, 33, 34),
(8, 3, 360, 30, 33),
(9, 2, 650, 30, 35),
(10, 4, 960, 35, 34),
(11, 50, 7200, 10, 30);

-- --------------------------------------------------------

--
-- Structure de la table `stations`
--

CREATE TABLE IF NOT EXISTS `stations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `zoneid` int(11) NOT NULL,
  `is_national` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Contenu de la table `stations`
--

INSERT INTO `stations` (`id`, `name`, `zoneid`, `is_national`) VALUES
(1, 'Valenciennes', 1, 0),
(2, 'Saint-Amand-les-Eaux', 1, 0),
(3, 'Denain', 1, 0),
(10, 'Lille', 1, 1),
(20, 'Paris', 2, 1),
(30, 'Marseille', 3, 1),
(33, 'Cassis', 3, 0),
(34, 'La Ciotat', 3, 0),
(35, 'Aubagne', 3, 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(255) COLLATE utf8_bin NOT NULL,
  `newsletter` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `active` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_bin NOT NULL,
  `fbid` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `googleid` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `newsletter`, `email`, `password`, `active`, `ip`, `fbid`, `googleid`) VALUES
(2, 'Pierre', 'JOUGLET', 0, 'Pierre.JOUGLET@supinfo.com', '085c946de284abbf45f0d14c0afd8a0bb8ad8860', 0, '', 'bite', 'truc'),
(3, 'TEST', 'TEST', 1, 'unmondesansmensonges@gmail.com', '1038a789486efdc0a2c1b59e8255ea95e15fce34', 1, '127.0.0.1', 'test', NULL),
(4, 'a', 'a', 1, 'a@a.a', '1038a789486efdc0a2c1b59e8255ea95e15fce34', 1, '', NULL, 'a'),
(5, 'a2', 'a2', 0, NULL, NULL, 1, '', 'efbuysf53452', NULL),
(6, 'a3', 'a3', 0, NULL, NULL, 1, '', 'efbuysf53453', NULL),
(7, 'a3', 'a3', 0, NULL, NULL, 1, '', NULL, 'efbuysf53453'),
(8, 'a3', 'a3', 0, NULL, NULL, 1, '', NULL, 'efbuysf53453'),
(9, 'a3', 'a3', 0, NULL, NULL, 1, '', NULL, 'efbuysf53453');

-- --------------------------------------------------------

--
-- Structure de la table `zone`
--

CREATE TABLE IF NOT EXISTS `zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `zone`
--

INSERT INTO `zone` (`id`, `label`) VALUES
(1, 'Nord'),
(2, 'Île-de-France'),
(3, 'Bouches-du-Rhône');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
