-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 31 Octobre 2012 à 17:40
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Structure de la table `nos_form`
--

CREATE TABLE IF NOT EXISTS `nos_form` (
  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) NOT NULL,
  `form_virtual_name` varchar(31) NOT NULL,
  `form_manager_id` int(10) unsigned DEFAULT NULL,
  `form_client_email_field_id` int(10) unsigned DEFAULT NULL,
  `form_layout` text NOT NULL,
  `form_created_at` datetime NOT NULL,
  `form_updated_at` datetime NOT NULL,
  PRIMARY KEY (`form_id`)
)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nos_form_answer`
--

CREATE TABLE IF NOT EXISTS `nos_form_answer` (
  `answer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `answer_form_id` int(10) unsigned NOT NULL,
  `answer_ip` varchar(40) NOT NULL,
  `answer_created_at` datetime NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `response_form_id` (`answer_form_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nos_form_answer_field`
--

CREATE TABLE IF NOT EXISTS `nos_form_answer_field` (
  `anfi_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `anfi_answer_id` int(10) unsigned NOT NULL,
  `anfi_field_id` int(10) unsigned NOT NULL,
  `anfi_value` varchar(255) NOT NULL,
  PRIMARY KEY (`anfi_id`),
  UNIQUE KEY `anfi_answer_id` (`anfi_answer_id`,`anfi_field_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nos_form_field`
--

CREATE TABLE IF NOT EXISTS `nos_form_field` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_form_id` int(10) unsigned NOT NULL,
  `field_type` varchar(100) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_message` text NOT NULL,
  `field_virtual_name` varchar(31) NOT NULL,
  `field_choices` text NOT NULL,
  `field_created_at` datetime NOT NULL,
  `field_mandatory` tinyint(1) NOT NULL,
  `field_default_value` varchar(255) NOT NULL,
  `field_details` text NOT NULL,
  `field_style` enum('p','h1','h2','h3') NOT NULL,
  `field_width` tinyint(4) NOT NULL,
  `field_height` tinyint(4) NOT NULL,
  `field_limited_to` int(11) NOT NULL,
  `field_origin` varchar(31) NOT NULL,
  `field_origin_var` varchar(31) NOT NULL,
  `field_technical_id` varchar(32) NOT NULL,
  `field_technical_css` varchar(100) NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY `field_form_id` (`field_form_id`)
)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=669 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
