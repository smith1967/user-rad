-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 24, 2015 at 03:20 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `radius`
--

-- --------------------------------------------------------

--
-- Table structure for table `group_config`
--

CREATE TABLE IF NOT EXISTS `group_config` (
  `gid` smallint(6) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(10) NOT NULL,
  `group_desc` varchar(100) NOT NULL,
  `upload` varchar(10) NOT NULL,
  `download` varchar(10) NOT NULL,
  `expire` varchar(100) NOT NULL,
  `simultaneous` varchar(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE IF NOT EXISTS `register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `gid` smallint(6) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `comfirm` enum('Y','N','C') NOT NULL,
  `pid` varchar(13) NOT NULL,
  `department` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hostname` varchar(50) NOT NULL,
  `status` char(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stdtemp`
--

CREATE TABLE IF NOT EXISTS `stdtemp` (
  `std_id` varchar(13) CHARACTER SET utf8 NOT NULL,
  `pid` varchar(13) CHARACTER SET utf8 NOT NULL,
  `fname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `lname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `groupname` varchar(25) CHARACTER SET utf8 NOT NULL,
  UNIQUE KEY `std_id` (`std_id`)
) ENGINE=MyISAM DEFAULT CHARSET=tis620;

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--

CREATE TABLE IF NOT EXISTS `usergroup` (
  `UserName` varchar(64) NOT NULL DEFAULT '',
  `GroupName` varchar(64) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '1',
  KEY `UserName` (`UserName`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usergrouptemp`
--

CREATE TABLE IF NOT EXISTS `usergrouptemp` (
  `UserName` varchar(64) NOT NULL DEFAULT '',
  `GroupName` varchar(64) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '1',
  KEY `UserName` (`UserName`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(13) CHARACTER SET utf8 NOT NULL,
  `password` varchar(13) CHARACTER SET utf8 NOT NULL,
  `fname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `lname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `groupname` varchar(25) CHARACTER SET utf8 NOT NULL,
  `pid` varchar(13) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'Y',
  `startban` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hostname` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `std_id` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=tis620 AUTO_INCREMENT=259 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_temp`
--

CREATE TABLE IF NOT EXISTS `users_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(13) CHARACTER SET utf8 NOT NULL,
  `password` varchar(13) CHARACTER SET utf8 NOT NULL,
  `fname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `lname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `groupname` varchar(25) CHARACTER SET utf8 NOT NULL,
  `pid` varchar(13) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'Y',
  `startban` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hostname` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `std_id` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=tis620 AUTO_INCREMENT=57 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
