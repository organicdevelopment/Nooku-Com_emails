CREATE TABLE `jos_emails_emails` (
  `email_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_sent` datetime NOT NULL,
  `from` varchar(50) COLLATE utf8_turkish_ci NOT NULL DEFAULT '',
  `to` varchar(50) COLLATE utf8_turkish_ci NOT NULL DEFAULT '',
  `subject` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `html` text COLLATE utf8_turkish_ci NOT NULL,
  `text` text COLLATE utf8_turkish_ci NOT NULL,
  `sent` bit(1) DEFAULT b'0',
  `message` varchar(255) COLLATE utf8_turkish_ci DEFAULT '',
  `read` bit(1) DEFAULT b'0',
  PRIMARY KEY (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

CREATE TABLE `jos_emails_layouts` (
  `layout_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `html` text COLLATE utf8_turkish_ci,
  `text` text COLLATE utf8_turkish_ci,
  PRIMARY KEY (`layout_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

CREATE TABLE `jos_emails_templates` (
  `template_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `component` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_turkish_ci NOT NULL DEFAULT '',
  `from_name` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `from_email` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `body_html` text COLLATE utf8_turkish_ci,
  `body_text` text COLLATE utf8_turkish_ci,
  `layout_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `identifier` (`component`,`type`),
  KEY `layout_id` (`layout_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

