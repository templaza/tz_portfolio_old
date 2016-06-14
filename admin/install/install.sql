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
  `template` VARCHAR(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `home` char(7) NOT NULL,
  `protected` tinyint(3) NOT NULL,
  `layout` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `protected` tinyint(3) NOT NULL,
  `manifest_cache` text NOT NULL,
  `params` text NOT NULL,
  `published` tinyint(4) NOT NULL,
  `access` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 ;

INSERT IGNORE INTO `#__tz_portfolio_extensions` (`id`, `name`, `type`, `protected`, `manifest_cache`, `params`, `published`, `access`) VALUES
(1, 'system', 'tz_portfolio-template', 1, '{"name":"system","type":"tz_portfolio-template","creationDate":"July 17th 2015","author":"DuongTVTemplaza","copyright":"Copyright (C) 2012 TemPlaza. All rights reserved.","authorEmail":"info@templaza.com","authorUrl":"","version":"1.0","description":"TZ_PORTFOLIO_TPL_XML_DESCRIPTION","group":"","filename":"template"}', '', 1, 1);

INSERT IGNORE INTO `#__tz_portfolio_templates` (`id`, `template`, `title`, `home`, `protected`, `layout`, `params`) VALUES
(1, 'system', 'Default', '1', 1, '[{"name":"Media","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"20px 0","containertype":"","children":[{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"media","customclass":"","responsiveclass":""}]},{"name":"Information","class":"muted","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"created_date","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"vote","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"author_name","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"category","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"parent_category","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"comment_count","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"hits","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"published_date","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"modified_date","customclass":"","responsiveclass":""}]},{"name":"Title","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"none","customclass":"","responsiveclass":"","children":[{"name":"Title Detail","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","children":[{"col-xs":"","col-sm":"","col-md":"","col-lg":"","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"icons","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"title","position":"","style":"","customclass":"","responsiveclass":""}]}]}]},{"name":"Introtext","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"introtext","customclass":"","responsiveclass":""}]},{"name":"Fulltext","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"fulltext","customclass":"","responsiveclass":""}]},{"name":"Extra Fields","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"extra_fields","customclass":"","responsiveclass":""}]},{"name":"Attachments","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"attachments","customclass":"","responsiveclass":""}]},{"name":"Tags","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"tag","customclass":"","responsiveclass":""}]},{"name":"Google Map","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"gmap","customclass":"","responsiveclass":""}]},{"name":"Author Info","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"author","customclass":"","responsiveclass":""}]},{"name":"Social Network","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"social_network","customclass":"","responsiveclass":""}]},{"name":"Comments","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"comment","customclass":"","responsiveclass":""}]},{"name":"Related Articles","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"related","customclass":"","responsiveclass":""}]}]', '{"override_html_template_site":"","layout":"default","use_single_layout_builder":"1"}');