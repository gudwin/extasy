TRUNCATE `system_register`;
INSERT INTO `system_register` (`parent`,`name`,`value`,`type`,`comment`)
VALUES 
(0,'AppData','','branch','Ветвь для данных приложений'),
(1,'test1','','branch','Test folder comment'),
(2,'value1','This is a simple value','string','Test comment'),
(0,'Applications','','branch','Приложения'),
(0,'System','','branch','Системная информация'),
(5,'types','','branch','Информация о типах')