# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.9)
# Database: chapterboard
# Generation Time: 2012-09-18 17:56:12 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table albums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `albums`;

CREATE TABLE `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `file_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table announcements
# ------------------------------------------------------------

DROP TABLE IF EXISTS `announcements`;

CREATE TABLE `announcements` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `user_id` bigint(20) DEFAULT NULL,
  `post_until` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`post_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table announcements_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `announcements_groups`;

CREATE TABLE `announcements_groups` (
  `announcement_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `announcement_id` (`announcement_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table backup_queue
# ------------------------------------------------------------



# Dump of table budget_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `budget_categories`;

CREATE TABLE `budget_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table budget_expected
# ------------------------------------------------------------

DROP TABLE IF EXISTS `budget_expected`;

CREATE TABLE `budget_expected` (
  `budget_id` int(11) NOT NULL,
  `budget_category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  UNIQUE KEY `budget_id` (`budget_id`,`budget_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table budget_transactions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `budget_transactions`;

CREATE TABLE `budget_transactions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `budget_id` int(11) NOT NULL,
  `budget_category_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `check_no` int(11) NOT NULL,
  `amount` float(10,2) NOT NULL,
  `created` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table budgets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `budgets`;

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `starting_balance` float(10,2) NOT NULL,
  `uncharged_dues` float(10,2) NOT NULL,
  `expected_fees` float(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table calendars
# ------------------------------------------------------------

DROP TABLE IF EXISTS `calendars`;

CREATE TABLE `calendars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table campaign_donations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campaign_donations`;

CREATE TABLE `campaign_donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL,
  `deposit_account_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `card_type` varchar(255) DEFAULT NULL,
  `item_label` varchar(255) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `collection_fee` float(10,2) DEFAULT NULL,
  `amount_payable` float(10,2) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table campaigns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `campaigns`;

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `deposit_account_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `body` text,
  `picture` varchar(255) DEFAULT NULL,
  `goal` float(10,2) DEFAULT NULL,
  `show_goal` tinyint(1) DEFAULT NULL,
  `payment_options` text,
  `payment_free_entry` tinyint(1) DEFAULT NULL,
  `expires` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table chapters
# ------------------------------------------------------------

DROP TABLE IF EXISTS `chapters`;

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `new` int(11) NOT NULL DEFAULT '1',
  `search_name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `founded` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `search_name` (`search_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(64) NOT NULL DEFAULT '',
  `user_id` bigint(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `thread` varchar(255) DEFAULT NULL,
  `body` text,
  `status` tinyint(1) DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `like_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`,`object_type`,`status`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table courses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `courses`;

CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `professor` varchar(255) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` binary(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table deposit_accounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `deposit_accounts`;

CREATE TABLE `deposit_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `routing_number` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table deposit_transactions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `deposit_transactions`;

CREATE TABLE `deposit_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_type` varchar(255) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `deposit_id` int(11) DEFAULT NULL,
  `deposit_account_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `card_type` varchar(255) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `collection_fee` float(10,2) DEFAULT NULL,
  `amount_payable` float(10,2) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `polymorphic` (`object_type`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table deposits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `deposits`;

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `deposit_account_id` int(11) DEFAULT NULL,
  `deposited_on` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `calendar_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `shared` tinyint(4) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` longtext NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `start_day` varchar(32) NOT NULL DEFAULT '',
  `start_time` varchar(32) NOT NULL DEFAULT '',
  `end_time` varchar(32) NOT NULL DEFAULT '',
  `end_day` varchar(32) NOT NULL DEFAULT '',
  `all_day` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(255) NOT NULL DEFAULT '',
  `mappable` tinyint(4) NOT NULL,
  `lat` varchar(12) NOT NULL DEFAULT '',
  `long` varchar(12) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `repeats` tinyint(1) DEFAULT NULL,
  `period` varchar(255) DEFAULT NULL,
  `period_option` varchar(255) DEFAULT NULL,
  `until` varchar(255) DEFAULT NULL,
  `until_date` varchar(255) DEFAULT NULL,
  `until_occurrences` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `child_n` int(11) DEFAULT NULL,
  `rsvp` tinyint(4) NOT NULL,
  `reminder` tinyint(4) NOT NULL,
  `reminder_unit` varchar(24) NOT NULL DEFAULT '',
  `reminder_value` tinyint(4) NOT NULL,
  `reminder_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `calendar_id` (`calendar_id`,`user_id`,`status`,`start`),
  KEY `status_start` (`status`,`start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table files
# ------------------------------------------------------------

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `created` datetime DEFAULT '2010-12-18 11:32:35',
  `object_id` int(11) NOT NULL,
  `object_type` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `size` int(11) DEFAULT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `extension` varchar(255) NOT NULL DEFAULT '',
  `filepath` varchar(255) DEFAULT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`,`object_type`),
  KEY `album_thumbnail_search` (`site_id`,`object_type`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table finance_charge_members
# ------------------------------------------------------------

DROP TABLE IF EXISTS `finance_charge_members`;

CREATE TABLE `finance_charge_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `finance_charge_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `amount` float(8,2) NOT NULL,
  `created` datetime NOT NULL,
  `paid` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `finance_charge_id` (`finance_charge_id`,`site_id`,`user_id`,`paid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table finance_charges
# ------------------------------------------------------------

DROP TABLE IF EXISTS `finance_charges`;

CREATE TABLE `finance_charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `deposit_account_id` int(11) DEFAULT NULL,
  `budget_id` int(11) NOT NULL,
  `amount` float(10,2) NOT NULL,
  `due` date NOT NULL,
  `title` varchar(128) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `late_fee` float DEFAULT NULL,
  `late_fee_type` varchar(255) DEFAULT NULL,
  `late_fee_assessed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`due`,`user_id`),
  KEY `budget_id` (`budget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table finance_payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `finance_payments`;

CREATE TABLE `finance_payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `finance_charge_id` int(11) unsigned NOT NULL,
  `site_id` int(11) unsigned NOT NULL,
  `deposit_account_id` int(11) DEFAULT NULL,
  `transaction_id` bigint(20) unsigned NOT NULL COMMENT 'Merchant transaction id.',
  `user_id` int(11) unsigned NOT NULL,
  `amount` float(10,2) NOT NULL,
  `collection_fee` float(10,2) DEFAULT NULL,
  `amount_payable` float(10,2) DEFAULT NULL,
  `type` varchar(16) NOT NULL DEFAULT '',
  `card_type` varchar(255) DEFAULT NULL,
  `check_no` varchar(255) DEFAULT NULL,
  `note` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `received` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_charge_id` (`finance_charge_id`,`site_id`,`user_id`,`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table folders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `folders`;

CREATE TABLE `folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `user_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `national` binary(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` binary(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table forums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forums`;

CREATE TABLE `forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated` datetime NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`status`,`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table group_rules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_rules`;

CREATE TABLE `group_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `resource` varchar(32) NOT NULL DEFAULT '',
  `privilege` varchar(32) NOT NULL DEFAULT '',
  `resource_ids` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(36) NOT NULL DEFAULT '',
  `static_key` varchar(64) NOT NULL DEFAULT '',
  `site_id` int(11) NOT NULL,
  `sms_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`static_key`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table groups_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups_users`;

CREATE TABLE `groups_users` (
  `group_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  UNIQUE KEY `group_id` (`group_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table invites
# ------------------------------------------------------------

DROP TABLE IF EXISTS `invites`;

CREATE TABLE `invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `email` varchar(128) NOT NULL DEFAULT '',
  `group_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL DEFAULT '',
  `token` varchar(128) NOT NULL DEFAULT '',
  `reminder_sent` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`token`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text,
  `members` text,
  `groups` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table messages_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messages_users`;

CREATE TABLE `messages_users` (
  `message_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` binary(1) DEFAULT NULL,
  `unread` binary(1) DEFAULT NULL,
  KEY `message_id` (`message_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table notifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `object_type` varchar(64) NOT NULL DEFAULT '',
  `object_id` bigint(20) NOT NULL,
  `value` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`object_type`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table payment_deposits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_deposits`;

CREATE TABLE `payment_deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table payment_settlements
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_settlements`;

CREATE TABLE `payment_settlements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reconciled` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) unsigned NOT NULL,
  `site_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `amount` float(10,2) NOT NULL,
  `rate` float(10,2) NOT NULL,
  `payable` float(10,2) NOT NULL,
  `payment_deposit_id` int(11) NOT NULL,
  `payment_settlement_id` int(11) NOT NULL DEFAULT '0',
  `method` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`user_id`),
  KEY `deposit_id` (`payment_deposit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table payrally_betas
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payrally_betas`;

CREATE TABLE `payrally_betas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `referrer` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table poll_choices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poll_choices`;

CREATE TABLE `poll_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL DEFAULT '',
  `votes` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table poll_votes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `poll_votes`;

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `poll_choice_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_id` (`poll_id`,`poll_choice_id`,`user_id`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table polls
# ------------------------------------------------------------

DROP TABLE IF EXISTS `polls`;

CREATE TABLE `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL,
  `private` tinyint(1) DEFAULT '0',
  `votes` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table recruits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recruits`;

CREATE TABLE `recruits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `list` tinyint(1) NOT NULL,
  `bid_status` int(11) DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `phone` varchar(15) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `about` text NOT NULL,
  `facebook` varchar(255) NOT NULL DEFAULT '',
  `year` varchar(128) NOT NULL DEFAULT '',
  `major` varchar(255) DEFAULT NULL,
  `housing` varchar(128) NOT NULL DEFAULT '',
  `referral` varchar(128) NOT NULL DEFAULT '',
  `hometown` varchar(255) DEFAULT '',
  `hometown_searchable` varchar(255) DEFAULT '',
  `high_school` varchar(255) DEFAULT '',
  `high_school_searchable` varchar(255) DEFAULT '',
  `file_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `votes` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL,
  `like_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`status`,`list`,`updated`,`comment_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table reminders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `reminders`;

CREATE TABLE `reminders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `reminder_unit` varchar(36) NOT NULL DEFAULT '',
  `reminder_value` tinyint(4) NOT NULL,
  `reminder_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  `key` varchar(25) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `weight` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table roles_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `roles_users`;

CREATE TABLE `roles_users` (
  `role_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table schools
# ------------------------------------------------------------

DROP TABLE IF EXISTS `schools`;

CREATE TABLE `schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `new` tinyint(4) NOT NULL DEFAULT '1',
  `search_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `search_name` (`search_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table service_events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `service_events`;

CREATE TABLE `service_events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table service_hours
# ------------------------------------------------------------

DROP TABLE IF EXISTS `service_hours`;

CREATE TABLE `service_hours` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `site_id` int(11) NOT NULL,
  `hours` decimal(10,2) NOT NULL,
  `dollars` decimal(10,2) NOT NULL,
  `notes` longtext NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`,`user_id`,`site_id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `session_id` varchar(128) NOT NULL DEFAULT '',
  `last_activity` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table shares
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shares`;

CREATE TABLE `shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table signups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `signups`;

CREATE TABLE `signups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `rsvp` tinyint(4) NOT NULL,
  `note` text NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table site_payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `site_payments`;

CREATE TABLE `site_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `amount` float(10,2) NOT NULL,
  `note` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table site_signups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `site_signups`;

CREATE TABLE `site_signups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school` varchar(255) NOT NULL DEFAULT '',
  `chapter` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(64) NOT NULL DEFAULT '',
  `last_name` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `phone` varchar(16) NOT NULL DEFAULT '',
  `finances` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `terms` tinyint(3) unsigned NOT NULL,
  `confirmed` int(11) NOT NULL DEFAULT '0',
  `confirm_token` varchar(255) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sites
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sites`;

CREATE TABLE `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT 'chapter',
  `user_id` bigint(20) NOT NULL,
  `school_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `slug_lower` varchar(255) DEFAULT NULL,
  `chapter_name` varchar(255) DEFAULT NULL,
  `founded` date DEFAULT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT '',
  `renewal_date` date NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `collections_enabled` tinyint(1) DEFAULT '0',
  `fundraising_enabled` tinyint(1) DEFAULT '0',
  `parson_bishop` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `suspend_date` date NOT NULL,
  `suspend_message` text NOT NULL,
  `fee_annual` float(10,2) NOT NULL,
  `fee_credit` float(10,2) NOT NULL,
  `fee_echeck` float(10,2) NOT NULL DEFAULT '6.00',
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`),
  KEY `chapter_id` (`chapter_id`),
  KEY `status` (`status`),
  KEY `renewal_date` (`renewal_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sms`;

CREATE TABLE `sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `groups` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `sent_from` varchar(255) DEFAULT NULL,
  `send_count` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `ref_id` varchar(255) DEFAULT NULL,
  `reply_id` varchar(255) DEFAULT NULL,
  `worker_id` varchar(255) DEFAULT NULL,
  `worker_timeout` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reply_id` (`reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sms_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sms_log`;

CREATE TABLE `sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table system_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `system_messages`;

CREATE TABLE `system_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `all_sites` tinyint(4) NOT NULL,
  `site_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `post_until` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `all` (`post_until`,`all_sites`,`site_id`,`chapter_id`,`school_id`,`created`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table topic_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `topic_history`;

CREATE TABLE `topic_history` (
  `user_id` bigint(20) NOT NULL,
  `topic_id` bigint(20) NOT NULL,
  `last_viewed` datetime NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`topic_id`),
  KEY `user_id_2` (`user_id`,`topic_id`,`last_viewed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table topics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `topics`;

CREATE TABLE `topics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `locked` int(11) DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `comment_count` int(11) NOT NULL,
  `archive_date` datetime DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `like_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`forum_id`,`status`,`sticky`,`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_profiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_profiles`;

CREATE TABLE `user_profiles` (
  `user_id` int(10) unsigned NOT NULL,
  `student_id` varchar(32) NOT NULL DEFAULT '',
  `scroll_number` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `birthday` date NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_carrier` varchar(255) DEFAULT '',
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(55) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `home_address1` varchar(255) DEFAULT NULL,
  `home_address2` varchar(255) DEFAULT NULL,
  `home_city` varchar(255) DEFAULT NULL,
  `home_state` varchar(255) DEFAULT NULL,
  `home_zip` varchar(255) DEFAULT NULL,
  `shirt_size` varchar(20) DEFAULT NULL,
  `emergency1_name` varchar(64) NOT NULL DEFAULT '',
  `emergency1_phone` varchar(32) NOT NULL DEFAULT '',
  `emergency2_name` varchar(64) NOT NULL DEFAULT '',
  `emergency2_phone` varchar(32) NOT NULL DEFAULT '',
  `school_year` varchar(32) NOT NULL DEFAULT '',
  `pledge_date` date DEFAULT NULL,
  `initiation_date` date DEFAULT NULL,
  `initiation_year` int(4) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `major` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `birthday` (`birthday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` int(11) NOT NULL,
  `site_id` int(11) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `picture` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `agreement` datetime NOT NULL,
  `help` text NOT NULL,
  `logins` int(11) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `type` varchar(15) DEFAULT NULL,
  `searchname` varchar(255) DEFAULT NULL,
  `calendar_token` varchar(255) NOT NULL DEFAULT '',
  `topic_history` datetime NOT NULL,
  `event_notify` tinyint(4) DEFAULT '1',
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `searchname` (`searchname`),
  KEY `calendar_token` (`calendar_token`),
  KEY `old_id` (`old_id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table version
# ------------------------------------------------------------

DROP TABLE IF EXISTS `version`;

CREATE TABLE `version` (
  `id` int(11) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table votes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `votes`;

CREATE TABLE `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `object_type` varchar(255) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voting` (`object_type`,`object_id`),
  KEY `votes` (`object_type`,`object_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
