<?php
use \Extasy\tests\Helper;

\Extasy\tests\system_register\Restorator::restore();
require_once CLASS_PATH . 'dumper/dumper.class.php';
$sql = <<<SQL
	DROP TABLE IF EXISTS acl_actions ;
	DROP TABLE IF EXISTS acl_grants;
	CREATE TABLE acl_actions (
		`id` INT NOT NULL auto_increment,
		`name` varchar(40) not null COMMENT 'Имя события',
		`title` varchar(80) null COMMENT 'Подпись к событию',
		`parentId` int not null,
		`fullPath` varchar(255) not null,
		PRIMARY KEY (`id`),
		index `searchParent` (`parentId`),
		index `searchChild` (`parentId`,`name`),
		index `searchPath` (`fullPath`)
	);
	CREATE TABLE acl_grants (
		`actionId` int not null COMMENT 'Индекс события',
		`entity` varchar(80) not null COMMENT 'Имя объекта',
		 unique `search` (`actionId`,`entity`),
		 index search_by_entity (`entity`)
	);

SQL;
$dumper = new CDumper();
$dumper->import($sql);



