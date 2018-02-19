CREATE TABLE IF NOT EXISTS `bundle_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'info',
  `title` varchar(250) NOT NULL DEFAULT '',
  `message` text NOT NULL DEFAULT '',
  `fromUser` int(11) unsigned DEFAULT NULL,
  `user` int(11) unsigned NOT NULL,
  `unread` tinyint(1) NOT NULL DEFAULT 1,
  `creationDate` bigint(20) unsigned NOT NULL,
  `modificationDate` bigint(20) unsigned DEFAULT NULL,
  `linkedElementType` ENUM('document', 'asset', 'object') DEFAULT NULL,
  `linkedElement` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;