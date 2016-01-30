DROP TABLE IF EXISTS `custom_config_schema`;
DROP TABLE IF EXISTS `custom_config_groups`;
DROP TABLE IF EXISTS `custom_config_items`;

CREATE TABLE `custom_config_schema` (
	`id` INT NOT NULL auto_increment,
	`name` VARCHAR(40) not null COMMENT 'Имя схемы',
	`title` VARCHAR(80) NOT NULL default'' COMMENT 'Название для пользователя',
	`sitemapId` INT NOT NULL default 0 COMMENT 'Индекс в таблице SITEMAP',
	PRIMARY KEY (`id`)
) charset="utf8";

CREATE TABLE `custom_config_groups` (
	`id` INT NOT NULL auto_increment,
	`title` VARCHAR(80) NOT NULL default '' COMMENT 'Название вкладки для пользователя',
	`schemaId` INT not null COMMENT 'Индекс схемы',
	`order` INT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `search_by_schema` (`schemaId`)
) charset="utf8";
CREATE TABLE `custom_config_items` (
	`id` INT NOT NULL auto_increment,
	`name` VARCHAR(40) NOT NULL COMMENT 'Имя контрола (системное)',
	`schemaId` INT not null COMMENT 'Индекс схемы',
	`tabId` INT not null COMMENT 'Индекс вкладки',
	`xtype` VARCHAR(40) NOT NULL COMMENT 'Тип контрола',
	`config` TEXT null COMMENT 'Доп. параметы для контрола',
	`title` VARCHAR(80) NOT NULL COMMENT 'Подпись для пользователя',
	`value` TEXT NOT NULL COMMENT 'Текущеее значение',
	`order` INT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `search_by_schema` (`schemaId`)
) charset="utf8";
INSERT INTO `custom_config_schema` SET `title`="Тестовый конфиг",`name`="test";

INSERT INTO `custom_config_groups` SET `title`="Основные данные",`schemaId`="1",`order`=1;
INSERT INTO `custom_config_groups` SET `title`="SEO",`schemaId`="1",`order`=2;

INSERT INTO `custom_config_items` SET `title`="Название страницы",`schemaID`="1",`name`="name",`xtype`="inputfield",`value`="1",`tabId`=1,`order`=1;
INSERT INTO `custom_config_items` SET `title`="Подзаголовок",`schemaId`="1",`name`="subname",`xtype`="inputfield",`value`="2",`tabId`=1,`order`=2;
INSERT INTO `custom_config_items` SET `title`="SEO=title",`schemaId`="1",`name`="seo_title",`xtype`="inputfield",`value`="3",`tabId`=2,`order`=3;