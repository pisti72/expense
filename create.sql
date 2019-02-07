CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `password` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE `expense`.`transactions` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`user_id` INT NOT NULL ,
`category_id` INT NOT NULL ,
`amount` DECIMAL NOT NULL ,
`currency` VARCHAR( 3 ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `datetime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


$sql = "CREATE TABLE `expense`.`transactions` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `user_id` INT NOT NULL, `category_id` INT NOT NULL, `amount` DECIMAL NOT NULL, `currency` VARCHAR(3) NOT NULL) ENGINE = MyISAM;";

