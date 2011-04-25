SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(13) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` tinytext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
  `cid` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(13) DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(13) unsigned NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` int(10) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `forums` (
  `fid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `count_threads` int(10) unsigned NOT NULL DEFAULT '0',
  `count_posts` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_date` int(10) NOT NULL DEFAULT '0',
  `lastposter_username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastposter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lastthread_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lastthread_subject` varchar(130) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `fposts` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `reply_to` int(10) unsigned NOT NULL DEFAULT '0',
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(130) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_on` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `posthash` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`pid`),
  KEY `tid` (`tid`),
  KEY `fid` (`fid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `fthreads` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(130) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_on` int(10) NOT NULL DEFAULT '0',
  `firstpost_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost_date` int(10) NOT NULL DEFAULT '0',
  `lastposter_username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastposter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `count_views` int(100) unsigned NOT NULL DEFAULT '0',
  `count_replies` int(100) unsigned NOT NULL DEFAULT '0',
  `is_closed` int(1) unsigned NOT NULL DEFAULT '0',
  `is_sticky` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `fid` (`fid`,`is_sticky`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `pid` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(13) unsigned DEFAULT '0',
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `teaser` text COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `categorie` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `created_on` int(10) NOT NULL,
  `allow_comments` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `session` (
  `id` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `save_path` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`,`save_path`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tokens` (
  `user_id` int(13) unsigned NOT NULL DEFAULT '0',
  `action` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_on` datetime NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `last_login_on` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `options` mediumtext COLLATE utf8_unicode_ci,
  `is_active` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
