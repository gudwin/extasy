<?php
// Создаем таблицу в БД
$sql = <<<SQL
	DROP TABLE IF EXISTS `basic_document`;
	CREATE TABLE `basic_document` (
		`id` INT NOT NULL auto_increment,
		`name` VARCHAR(255) NOT NULL,
		`content` TEXT NOT NULL,
		PRIMARY KEY (`id`)
	);
	INSERT INTO `basic_document` SET `id`=1,`name`="test name",`content` = "test content";
SQL;
$dumper = new CDumper();
$dumper->import($sql);


require_once dirname( __FILE__ ) . '/test_document.php';