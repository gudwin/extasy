# Создание тестового документа
DROP TABLE IF EXISTS `sitemap_test_document` ;
CREATE TABLE `sitemap_test_document` (
	`id` INT NOT NULL auto_increment,
	`name` VARCHAR(255) not null,
	PRIMARY KEY (`id`)
);


# Очищаем таблицу
TRUNCATE `sitemap`;
DELETE FROM `sitemap` WHERE `name`="test language";
DELETE FROM `sitemap` WHERE `name`="test language1";
DELETE FROM `sitemap` WHERE `name`="test language2";

INSERT INTO `sitemap` SET `name`="index",`script`="scripts/index.php",`url_key`="",`full_url`="///",`parent`="0";
INSERT INTO `sitemap` SET `name`="test language",`script`="scripts/noop.php",`url_key`="/lang_test",`full_url`="///lang_test/",`parent`="0";
set @a = (SELECT `id` FROM `sitemap` WHERE `full_url`="///lang_test/");
INSERT INTO `sitemap` SET `name`="test language",`script`="scripts/noop.php",`url_key`="1",`full_url`="///lang_test/1/",`parent`=@a;
INSERT INTO `sitemap` SET `name`="test language",`script`="scripts/noop.php",`url_key`="2",`full_url`="///lang_test/2/",`parent`=@a;

