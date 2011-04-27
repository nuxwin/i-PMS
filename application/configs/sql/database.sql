-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 27, 2011 at 04:03 AM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-7+squeeze1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `cid` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(13) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` tinytext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `cid` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(13) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(13) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_on` int(10) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE IF NOT EXISTS `forums` (
  `fid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `count_threads` int(10) unsigned NOT NULL DEFAULT '0',
  `count_posts` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_date` int(10) unsigned NOT NULL DEFAULT '0',
  `lastposter_username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastposter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lastthread_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lastthread_subject` varchar(130) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fposts`
--

CREATE TABLE IF NOT EXISTS `fposts` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `reply_to` int(10) unsigned NOT NULL,
  `fid` smallint(5) unsigned NOT NULL,
  `subject` varchar(130) COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_on` int(10) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `posthash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `tid` (`tid`),
  KEY `fid` (`fid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fthreads`
--

CREATE TABLE IF NOT EXISTS `fthreads` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` smallint(5) unsigned NOT NULL,
  `subject` varchar(130) COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_on` int(10) NOT NULL,
  `firstpost_id` int(10) unsigned NOT NULL,
  `lastpost_date` int(10) NOT NULL,
  `lastposter_username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastposter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `count_views` int(100) unsigned NOT NULL DEFAULT '0',
  `count_replies` int(100) unsigned NOT NULL DEFAULT '0',
  `is_closed` int(1) unsigned NOT NULL DEFAULT '0',
  `is_sticky` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `fid` (`fid`,`is_sticky`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `pid` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(13) unsigned NOT NULL DEFAULT '0',
  `title` varchar(130) COLLATE utf8_unicode_ci NOT NULL,
  `teaser` TINYTEXT COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `categorie` varchar(32) COLLATE utf8_unicode_ci DEFAULT '',
  `created_on` int(10) unsigned NOT NULL,
  `allow_comments` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `sid` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`sid`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `sid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` int(1) NOT NULL DEFAULT '0',
  `last_login_on` int(10) unsigned NOT NULL DEFAULT '0',
  `firstname` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastname` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  KEY `auth` (`username`, `password`,`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `wid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `options` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_active` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`wid`),
  KEY `widget` (`wid`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
