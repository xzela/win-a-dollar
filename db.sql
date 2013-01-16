CREATE TABLE `session_hash` (
  `hash_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(256) NOT NULL,
  `number_hash` varchar(256) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `winner` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`hash_id`)
) ENGINE=MyISAM AUTO_INCREMENT=729 DEFAULT CHARSET=latin1;
