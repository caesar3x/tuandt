-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2013 at 12:08 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pmas`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE IF NOT EXISTS `admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `role` tinyint(4) DEFAULT '0',
  `token` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `hidden` tinyint(4) DEFAULT '0',
  `deleted` tinyint(4) DEFAULT '0',
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`id`, `username`, `first_name`, `last_name`, `password`, `email`, `status`, `role`, `token`, `created_at`, `updated_at`, `hidden`, `deleted`, `note`) VALUES
(1, 'admin', 'admin', 'admin', '$2y$13$YjQxY2QyOTBiMzM1ZWYzMeNv8CAWW8x9qAri3WyAqBMs9t0HkEFp6', 'admin@mail.com', 1, 5, 'fd128ed136ae101695ed6ff2a0798a97', 1379417939, 1379417939, 0, 0, ''),
(5, NULL, '', '', '$2y$13$YjQxY2QyOTBiMzM1ZWYzMeNv8CAWW8x9qAri3WyAqBMs9t0HkEFp6', 'admin+1@mail.com', 1, 2, 'd41d8cd98f00b204e9800998ecf8427e', 1379426189, 1379426189, 0, 0, ''),
(6, 'danjan', NULL, NULL, '$2y$13$YjQxY2QyOTBiMzM1ZWYzMeNv8CAWW8x9qAri3WyAqBMs9t0HkEFp6', 'admin+2@mail.com', 0, 2, 'f4fb2a0b0915fce9c5590db14dbe7918', 1379304951, 1379304951, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `country_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `name`, `deleted`) VALUES
(1, 'Hongkong', 0),
(2, 'United State', 0);

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE IF NOT EXISTS `device` (
  `device_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `price` decimal(12,4) DEFAULT NULL,
  `condition_id` int(11) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`device_id`, `name`, `brand`, `model`, `type_id`, `price`, `condition_id`, `currency`, `deleted`) VALUES
(1, 'Device test 1', 'dsadasdas', 'dsadasdsa', 1, 323232.0000, 0, 'USD', 0);

-- --------------------------------------------------------

--
-- Table structure for table `device_condition`
--

CREATE TABLE IF NOT EXISTS `device_condition` (
  `condition_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `recycler` varchar(20) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`condition_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `device_condition`
--

INSERT INTO `device_condition` (`condition_id`, `name`, `recycler`, `deleted`) VALUES
(1, 'Tdm Condition Example 1', 'tdm', 0),
(2, 'Tdm Condition Example 2', 'tdm', 0),
(3, 'Recycler Condition Example 1', 'recycler', 0),
(4, 'Recycler Condition Example 2', 'recycler', 0);

-- --------------------------------------------------------

--
-- Table structure for table `device_type`
--

CREATE TABLE IF NOT EXISTS `device_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `device_type`
--

INSERT INTO `device_type` (`type_id`, `name`, `deleted`) VALUES
(1, 'Device Type Examle 1', 0),
(2, 'Device Type Examle 2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `exchange`
--

CREATE TABLE IF NOT EXISTS `exchange` (
  `country_id` tinyint(4) NOT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `exchange_rate` varchar(255) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recycler`
--

CREATE TABLE IF NOT EXISTS `recycler` (
  `recycler_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `country_id` tinyint(4) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `address` text,
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`recycler_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `recycler`
--

INSERT INTO `recycler` (`recycler_id`, `name`, `country_id`, `deleted`, `email`, `telephone`, `address`, `website`) VALUES
(1, 'dasdasds', 2, 0, 'datnt@gmail.com', 'das', 'asdasdasds', 'dasd'),
(2, 'dsadsa', 1, 0, 'dsadsa@gmail.com', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `recycler_device`
--

CREATE TABLE IF NOT EXISTS `recycler_device` (
  `device_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recycler_id` bigint(20) DEFAULT NULL,
  `exchange_price` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE IF NOT EXISTS `resources` (
  `resource_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT '0',
  `name` text NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`resource_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `hidden`, `deleted`, `sort_order`, `name`, `path`) VALUES
(1, 0, 0, 0, 'Dashboard', 'application\\index\\index'),
(2, 0, 0, 0, 'Login', 'application\\login\\index'),
(3, 0, 0, 0, 'Authentication', 'application\\login\\auth'),
(4, 0, 0, 0, 'Logout', 'application\\logout\\index'),
(5, 0, 0, 0, 'Manage Users', 'application\\user\\index');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT '0',
  `parent_id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `resource_ids` longtext NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `created_at`, `updated_at`, `hidden`, `deleted`, `sort_order`, `parent_id`, `role`, `name`, `resource_ids`) VALUES
(1, 0, 0, 0, 0, 1, 0, 'guest', 'Guest', 'a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}'),
(2, 0, 0, 0, 0, 2, 0, 'manager', 'Manager', 'a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}'),
(3, 0, 0, 0, 0, 3, 0, 'editor', 'Editor', ''),
(4, 0, 0, 0, 0, 4, 0, 'admin', 'Admin', ''),
(5, 0, 0, 0, 0, 5, 0, 'super_admin', 'Super Admin', '');

-- --------------------------------------------------------

--
-- Table structure for table `tdm_device`
--

CREATE TABLE IF NOT EXISTS `tdm_device` (
  `device_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` tinyint(4) DEFAULT NULL,
  `exchange_price` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
