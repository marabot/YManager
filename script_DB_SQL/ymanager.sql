-- phpMyAdmin SQL Dump
-- version 4.2.6deb1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Dim 07 Juin 2015 à 18:35
-- Version du serveur :  5.5.43-0ubuntu0.14.10.1
-- Version de PHP :  5.5.12-2ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `ymanager`
--

-- --------------------------------------------------------

--
-- Structure de la table `bot`
--

CREATE TABLE IF NOT EXISTS `bot` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lastHarvest` int(50) NOT NULL,
  `createDate` int(50) NOT NULL,
  `user_id` varchar(100) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Structure de la table `botChannel`
--

CREATE TABLE IF NOT EXISTS `botChannel` (
  `channelId` varchar(255) NOT NULL,
  `botId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(11) NOT NULL,
  `channelId` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `refresh_token` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `bot`
--
ALTER TABLE `bot`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `botChannel`
--
ALTER TABLE `botChannel`
 ADD PRIMARY KEY (`channelId`,`botId`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `bot`
--
ALTER TABLE `bot`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
