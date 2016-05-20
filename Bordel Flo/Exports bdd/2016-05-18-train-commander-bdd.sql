-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 18 Mai 2016 à 16:20
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

DROP TABLE IF EXISTS `connections`;
CREATE TABLE IF NOT EXISTS `connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` timestamp NOT NULL,
  `stationid` int(11) NOT NULL,
  `segmentid` int(11) NOT NULL,
  `pathid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `connections`
--

INSERT INTO `connections` (`id`, `start_time`, `stationid`, `segmentid`, `pathid`) VALUES
(1, '2016-06-30 05:00:00', 1, 1, 1),
(2, '2016-06-30 05:15:00', 2, 3, 1),
(4, '2016-06-30 07:00:00', 10, 11, 9),
(6, '2016-06-30 09:30:00', 30, 8, 6),
(7, '2016-06-30 09:40:00', 33, 7, 6);

-- --------------------------------------------------------

--
-- Structure de la table `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cost` double NOT NULL,
  `start_time` timestamp NOT NULL,
  `end_time` timestamp NOT NULL,
  `start_station` varchar(50) NOT NULL,
  `end_station` varchar(50) NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `paths`
--

DROP TABLE IF EXISTS `paths`;
CREATE TABLE IF NOT EXISTS `paths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_national` tinyint(1) NOT NULL DEFAULT '0',
  `label` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

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
(9, 1, 'Lille Marseille');

-- --------------------------------------------------------

--
-- Structure de la table `segments`
--

DROP TABLE IF EXISTS `segments`;
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

DROP TABLE IF EXISTS `stations`;
CREATE TABLE IF NOT EXISTS `stations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `zoneid` int(11) NOT NULL,
  `is_national` tinyint(1) NOT NULL DEFAULT '0',
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
-- Structure de la table `zone`
--

DROP TABLE IF EXISTS `zone`;
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
