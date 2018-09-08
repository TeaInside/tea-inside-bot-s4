-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL,
  `telegram_file_id` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `md5_checksum` varchar(32) NOT NULL,
  `sha1_checksum` varchar(40) NOT NULL,
  `absolute_hash` varchar(73) NOT NULL,
  `hit_count` varchar(73) NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absolute_hash` (`absolute_hash`),
  UNIQUE KEY `file_telegram_id` (`telegram_file_id`),
  KEY `md5_checksum` (`md5_checksum`),
  KEY `sha1_checksum` (`sha1_checksum`),
  FULLTEXT KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_admin`;
CREATE TABLE `group_admin` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `group_admin_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_admin_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_history`;
CREATE TABLE `group_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(64) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `name` (`name`),
  KEY `username` (`username`),
  KEY `link` (`link`),
  KEY `photo` (`photo`),
  CONSTRAINT `group_history_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_messages`;
CREATE TABLE `group_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `telegram_msg_id` bigint(20) NOT NULL,
  `reply_to_msg_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `group_messages_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_messages_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_messages_data`;
CREATE TABLE `group_messages_data` (
  `group_message_id` bigint(20) NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `file_id` bigint(20) DEFAULT NULL,
  `type` varchar(32) NOT NULL,
  KEY `group_message_id` (`group_message_id`),
  KEY `file_id` (`file_id`),
  CONSTRAINT `group_messages_data_ibfk_3` FOREIGN KEY (`group_message_id`) REFERENCES `group_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_messages_data_ibfk_4` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` varchar(64) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `msg_count` bigint(20) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `username` (`username`),
  KEY `link` (`link`),
  KEY `photo` (`photo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private_messages`;
CREATE TABLE `private_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(64) NOT NULL,
  `telegram_msg_id` bigint(20) NOT NULL,
  `reply_to_msg_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `private_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private_messages_data`;
CREATE TABLE `private_messages_data` (
  `private_message_id` bigint(20) NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `file_id` bigint(20) DEFAULT NULL,
  `type` varchar(32) NOT NULL,
  KEY `private_message_id` (`private_message_id`),
  KEY `file_id` (`file_id`),
  FULLTEXT KEY `text` (`text`),
  CONSTRAINT `private_messages_data_ibfk_2` FOREIGN KEY (`private_message_id`) REFERENCES `private_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `private_messages_data_ibfk_4` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sudoers`;
CREATE TABLE `sudoers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(64) NOT NULL,
  `password` varchar(128) DEFAULT NULL,
  `session_timeout` bigint(20) DEFAULT '-1',
  `created_at` datetime NOT NULL,
  `latest_logged_in` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sudoers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `user_history`;
CREATE TABLE `user_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(64) NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `username` (`username`),
  KEY `photo` (`photo`),
  CONSTRAINT `user_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` varchar(64) NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_bot` tinyint(4) DEFAULT '0',
  `private_message_count` bigint(20) NOT NULL DEFAULT '0',
  `group_message_count` bigint(20) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `username` (`username`),
  KEY `photo` (`photo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2018-09-08 16:30:55
