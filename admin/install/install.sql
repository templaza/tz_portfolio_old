CREATE TABLE IF NOT EXISTS `#__tz_portfolio_xref` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`fieldsid` INT NOT NULL,
`groupid` INT NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_fields_group` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`description` TEXT NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_fields` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`title` VARCHAR( 255 ) NOT NULL ,
`type` VARCHAR( 255 ) NOT NULL ,
`value` TEXT NOT NULL,
`default_value` TEXT NOT NULL,
`ordering` INT NOT NULL,
`published` TINYINT NOT NULL DEFAULT '1' ,
`description` TEXT NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_categories` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`groupid` INT NOT NULL ,
`catid` INT NOT NULL,
`images` TEXT NOT NULL,
`template_id` INT UNSIGNED NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`contentid` INT NOT NULL ,
`fieldsid` INT NOT NULL,
`value` TEXT NOT NULL,
`images` TEXT NOT NULL ,
`imagetitle` VARCHAR( 255 ) NOT NULL,
`ordering` INT NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_xref_content` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`contentid` INT NOT NULL ,
`groupid` INT NOT NULL ,
`images` TEXT NOT NULL ,
`images_hover` TEXT NOT NULL,
`gallery` TEXT NOT NULL ,
`video` TEXT NOT NULL ,
`type` VARCHAR( 25 ) NOT NULL,
`imagetitle` VARCHAR( 255 ) NOT NULL,
`gallerytitle` TEXT NOT NULL ,
`videotitle` TEXT NOT NULL,
`videothumb` TEXT NOT NULL,
`attachfiles` TEXT NOT NULL ,
`attachtitle` TEXT NOT NULL,
`attachold` TEXT NOT NULL,
`audio` TEXT NULL,
`audiothumb` TEXT NULL,
`audiotitle` VARCHAR(255) NULL,
`quote_author` VARCHAR(255) NOT NULL,
`quote_text` TEXT NOT NULL,
`link_url` VARCHAR( 1000 ) NOT NULL ,
`link_title` VARCHAR( 1000 ) NOT NULL,
`link_attribs` VARCHAR(5120) NOT NULL,
`template_id` INT UNSIGNED NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_tags` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY  KEY,
`name` VARCHAR ( 255 ) NOT NULL,
`published` TINYINT NOT NULL,
`description` TEXT NOT NULL,
`attribs` VARCHAR(5120) NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__tz_portfolio_tags_xref` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`tagsid` INT NOT NULL ,
`contentid` INT NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__tz_portfolio_users` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`usersid` INT NOT NULL ,
`images` TEXT NOT NULL ,
`url` TEXT NOT NULL,
`gender` VARCHAR( 3 ) NOT NULL ,
`twitter` TEXT NOT NULL ,
`facebook` TEXT NOT NULL ,
`google_one` TEXT NOT NULL,
`description` TEXT NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plugin` (
`id`  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`contentid` INT NOT NULL ,
`pluginid` INT NOT NULL,
`params` TEXT NULL
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_templates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `home` char(7) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM  DEFAULT CHARSET=utf8;