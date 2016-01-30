-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 12 2014 г., 13:14
-- Версия сервера: 5.5.33
-- Версия PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- База данных: `owndb2`
--

-- --------------------------------------------------------

--
-- Структура таблицы `acl_actions`
--

DROP TABLE IF EXISTS `acl_actions`;
CREATE TABLE `acl_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8 NOT NULL COMMENT 'Имя события',
  `title` varchar(80) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Подпись к событию',
  `parentId` int(11) NOT NULL,
  `fullPath` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `searchParent` (`parentId`),
  KEY `searchChild` (`parentId`,`name`),
  KEY `searchPath` (`fullPath`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=64 ;

--
-- Дамп данных таблицы `acl_actions`
--

INSERT INTO `acl_actions` (`id`, `name`, `title`, `parentId`, `fullPath`) VALUES
(28, 'Auditor', 'Аудитор журнала безопасности', 0, 'Auditor'),
(29, 'Administrator', 'Администратор', 0, 'Administrator'),
(49, 'Users', 'Пользователи', 29, 'Administrator/Users'),
(50, 'System Administrator', 'Системный администратор', 0, 'System Administrator');

-- --------------------------------------------------------

--
-- Структура таблицы `acl_grants`
--

DROP TABLE IF EXISTS `acl_grants`;
CREATE TABLE `acl_grants` (
  `actionId` int(11) NOT NULL COMMENT 'Индекс события',
  `entity` varchar(80) NOT NULL COMMENT 'Имя объекта',
  UNIQUE KEY `search` (`actionId`,`entity`),
  KEY `search_by_entity` (`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `acl_grants`
--

INSERT INTO `acl_grants` (`actionId`, `entity`) VALUES
(50, 'users100');

-- --------------------------------------------------------

--
-- Структура таблицы `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `description` text,
  `critical` tinyint(1) NOT NULL DEFAULT '1',
  `enable_logging` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `name`, `description`, `critical`, `enable_logging`) VALUES
(1, 'Errors.Runtime', '', 0, 1),
(11, 'Schedule.runtimeTime', '', 0, 1),
(13, 'Users.login', '', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `audit_records`
--

DROP TABLE IF EXISTS `audit_records`;
CREATE TABLE `audit_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT '0000-00-00 00:00:00',
  `log_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_login` varchar(40) DEFAULT NULL,
  `short` text,
  `full` text,
  `ip` binary(16) DEFAULT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `search_by_user_name` (`user_login`),
  KEY `search_by_user` (`user_id`),
  KEY `search_by_datetime` (`date`),
  KEY `log_id` (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7674 ;

--
-- Дамп данных таблицы `audit_records`
--

INSERT INTO `audit_records` (`id`, `date`, `log_id`, `user_id`, `user_login`, `short`, `full`, `ip`, `viewed`) VALUES
(7645, '2014-08-10 11:39:40', 1, 0, '', 'No matching route found for url - http://owndb2/api/', 'exception ''NotFoundException'' with message ''No matching route found for url - http://owndb2/api/'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php:92\nStack trace:\n#0 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#1 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7646, '2014-08-10 11:42:45', 1, 0, '', 'No matching route found for url - http://owndb2/resources/extasy/css/auth/style.css', 'exception ''NotFoundException'' with message ''No matching route found for url - http://owndb2/resources/extasy/css/auth/style.css'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php:93\nStack trace:\n#0 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#1 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7647, '2014-08-10 11:46:07', 1, 0, '', 'No matching route found for url - http://owndb2/resources/extasy/css/auth/style.css', 'exception ''NotFoundException'' with message ''No matching route found for url - http://owndb2/resources/extasy/css/auth/style.css'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php:93\nStack trace:\n#0 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#1 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7648, '2014-08-10 11:46:18', 1, 0, '', 'No matching route found for url - http://owndb2/resources/extasy/css/auth/style.css', 'exception ''NotFoundException'' with message ''No matching route found for url - http://owndb2/resources/extasy/css/auth/style.css'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php:93\nStack trace:\n#0 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#1 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7649, '2014-08-10 12:13:40', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7650, '2014-08-10 12:13:40', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#8 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7651, '2014-08-10 12:13:59', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7652, '2014-08-10 12:13:59', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#8 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7653, '2014-08-10 12:17:03', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7654, '2014-08-10 12:17:03', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#8 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7655, '2014-08-10 12:17:31', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7656, '2014-08-10 12:17:31', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#8 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7657, '2014-08-10 12:17:55', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7658, '2014-08-10 12:17:55', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#8 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7659, '2014-08-10 12:19:13', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7660, '2014-08-10 12:19:13', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Str0nger_D3fens...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Str0nger_D3fens...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#8 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7661, '2014-08-10 12:20:07', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''\\Columns\\UserGr...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''\\Columns\\UserGr...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7662, '2014-08-10 12:20:07', 13, 0, '', 'Class column`\\Columns\\UserGrants` not found', 'exception ''NotFoundException'' with message ''Class column`\\Columns\\UserGrants` not found'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php:97\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''rights'', NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''\\Columns\\UserGr...'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''\\Columns\\UserGr...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#8 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#11 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#12 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7663, '2014-08-10 12:24:27', 13, 0, '', 'DAO_Tcategory2::__construct  :smi_id', 'exception ''ForbiddenException'' with message ''DAO_Tcategory2::__construct  :smi_id'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/Columns/Category2.php:47\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(99): Extasy\\Columns\\Category2->__construct(''smi_id'', Array, NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''smi_id'', NULL)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''\\Columns\\UserGr...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''\\Columns\\UserGr...'')\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#11 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#12 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#13 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7664, '2014-08-10 12:24:27', 13, 0, '', 'DAO_Tcategory2::__construct  :smi_id', 'exception ''ForbiddenException'' with message ''DAO_Tcategory2::__construct  :smi_id'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/Columns/Category2.php:47\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(99): Extasy\\Columns\\Category2->__construct(''smi_id'', Array, NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''smi_id'', NULL)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''\\Columns\\UserGr...'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''\\Columns\\UserGr...'')\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#9 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#11 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#12 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#13 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7665, '2014-08-10 12:26:58', 13, 0, '', 'Model not found  :SMI', 'exception ''NotFoundException'' with message ''Model not found  :SMI'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/Columns/Category2.php:47\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(99): Extasy\\Columns\\Category2->__construct(''smi_id'', Array, NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''smi_id'', NULL)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Category2.php'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Category2.php'')\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#9 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#11 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#12 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#13 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7666, '2014-08-10 12:26:58', 13, 0, '', 'Model not found  :SMI', 'exception ''NotFoundException'' with message ''Model not found  :SMI'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/Columns/Category2.php:47\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(99): Extasy\\Columns\\Category2->__construct(''smi_id'', Array, NULL)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/dao/data.class.php(221): DAOData->__loadElement(''smi_id'', NULL)\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Model/Model.php(37): DAOData->getObjects(''users'', Array)\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(22): Extasy\\Model\\Model->__construct(Array)\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/account.php(156): UserAccount->__construct(Array)\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php(58): UserAccount::getByLogin(''root'')\n#6 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Category2.php'')\n#7 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Category2.php'')\n#8 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#9 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#10 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#11 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#12 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#13 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7667, '2014-08-10 12:28:02', 13, 0, '', 'User not found. Login:root', 'exception ''Exception'' with message ''User not found. Login:root'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php:79\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Category2.php'')\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(54): CMSAuth->loadUser(''root'', ''Category2.php'')\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(31): CMSAuth->autoLogin()\n#3 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(22): CMSAuth->__construct()\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(52): CMSAuth::getInstance()\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(132): Extasy\\CMS->__construct(Object(Faid\\Dispatcher\\Dispatcher))\n#6 /Users/gisma/Projects/owndb2/public_html/cms.php(22): Extasy\\CMS::load(Object(Faid\\Dispatcher\\Dispatcher))\n#7 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7668, '2014-08-10 12:28:02', 13, 0, '', 'User not found. Login:root', 'exception ''Exception'' with message ''User not found. Login:root'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/Users/login/login.php:79\nStack trace:\n#0 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(66): UsersLogin::login(''root'', ''Category2.php'')\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/cms/auth/auth.class.php(92): CMSAuth->loadUser(''root'', ''Category2.php'')\n#2 /Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Controllers/Index.php(23): CMSAuth->check()\n#3 [internal function]: Extasy\\Dashboard\\Controllers\\Index->showLoginForm()\n#4 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(1922): call_user_func(Array)\n#5 /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php(97): Faid\\Dispatcher\\HttpRoute->dispatch()\n#6 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#7 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7669, '2014-08-10 12:29:27', 1, 100, 'root', '[/Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Route.php:18] - Error [2]: require_once(/Users/gisma/Projects/owndb2/application/admin.php): failed to open stream: No such file or directory', '[/Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Route.php:18] - Error [2]: require_once(/Users/gisma/Projects/owndb2/application/admin.php): failed to open stream: No such file or directory', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7670, '2014-08-10 12:29:28', 1, 100, 'root', 'Fatal error "require_once(): Failed opening required ''/Users/gisma/Projects/owndb2/application/admin.php'' (include_path=''/Users/gisma/Projects/owndb2/application/views/'')"  at : "/Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Route.php:18"', 'exception ''Exception'' with message ''Fatal error "require_once(): Failed opening required ''/Users/gisma/Projects/owndb2/application/admin.php'' (include_path=''/Users/gisma/Projects/owndb2/application/views/'')"  at : "/Users/gisma/Projects/owndb2/Vendors/Extasy/Dashboard/Route.php:18"'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/errors/Handlers.php:14\nStack trace:\n#0 [internal function]: Extasy\\errors\\Handlers::onFatalError(''require_once():...'', ''/Users/gisma/Pr...'', 18)\n#1 /Users/gisma/Projects/owndb2/Vendors/Extasy/kernel/faid-0.5.php(345): call_user_func(Array, ''require_once():...'', ''/Users/gisma/Pr...'', 18)\n#2 [internal function]: Faid\\Debug\\Debug::fatalErrorShutDown()\n#3 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7671, '2014-08-10 12:30:13', 1, 100, 'root', 'No matching route found for url - http://owndb2/admin//administrate/users.php', 'exception ''NotFoundException'' with message ''No matching route found for url - http://owndb2/admin//administrate/users.php'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php:93\nStack trace:\n#0 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#1 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7672, '2014-08-10 12:30:22', 1, 100, 'root', 'No matching route found for url - http://owndb2/admin//administrate/users.php', 'exception ''NotFoundException'' with message ''No matching route found for url - http://owndb2/admin//administrate/users.php'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php:93\nStack trace:\n#0 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#1 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0),
(7673, '2014-08-12 13:46:45', 1, 0, '', 'No matching route found for url - http://owndb2/', 'exception ''NotFoundException'' with message ''No matching route found for url - http://owndb2/'' in /Users/gisma/Projects/owndb2/Vendors/Extasy/CMS.php:93\nStack trace:\n#0 /Users/gisma/Projects/owndb2/public_html/cms.php(23): Extasy\\CMS->dispatch()\n#1 {main}', '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `document2tag`
--

DROP TABLE IF EXISTS `document2tag`;
CREATE TABLE `document2tag` (
  `document_id` int(11) NOT NULL DEFAULT '0',
  `tag_name` varchar(255) NOT NULL DEFAULT '',
  `document_name` varchar(40) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  KEY `tag_name` (`tag_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `email_log`
--

DROP TABLE IF EXISTS `email_log`;
CREATE TABLE `email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL COMMENT 'Дата отправки письма',
  `to` varchar(255) NOT NULL COMMENT 'Получатель письма',
  `subject` varchar(255) NOT NULL COMMENT 'Тема письма',
  `content` text NOT NULL COMMENT 'Содержание письма',
  `attach1` varchar(255) NOT NULL COMMENT '1-й аттач',
  `attach2` varchar(255) NOT NULL COMMENT '2-й аттач',
  `attach3` varchar(255) NOT NULL COMMENT '3-й аттач',
  `attach4` varchar(255) NOT NULL COMMENT '4-й аттач',
  `attach5` varchar(255) NOT NULL COMMENT '5-й аттач',
  `status` varchar(255) NOT NULL COMMENT 'Результат отправки',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Имя события',
  `caller_code` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `events_listeners`
--

DROP TABLE IF EXISTS `events_listeners`;
CREATE TABLE `events_listeners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL COMMENT 'Индекс события',
  `function` varchar(255) NOT NULL COMMENT 'Имя функции на исполнение',
  `class` varchar(255) NOT NULL COMMENT 'Имя класса, содержащего функцию',
  `file` varchar(255) NOT NULL COMMENT 'Имя файла, содержащего функцию',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sitemap`
--

DROP TABLE IF EXISTS `sitemap`;
CREATE TABLE `sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Имя страницы ',
  `document_name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Имя документа, отвечающего за страницу',
  `document_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Индекс документа',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Дата создания страницы',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Дата последнего обновления страницы',
  `revision_count` int(11) DEFAULT '0' COMMENT 'Количество обновлений документа',
  `script` varchar(255) NOT NULL DEFAULT '' COMMENT 'Путь к php-файлу, отвечающему за эту страницу ',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Порядковый номер в сортировке',
  `parent` int(11) NOT NULL DEFAULT '0' COMMENT 'Индекс элемента-предка для текущей страницы',
  `url_key` varchar(255) NOT NULL DEFAULT '' COMMENT 'Часть урла, которая будет добавлена в URL-у предка, для формирования адреса текущего документа',
  `full_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL-документа',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT 'Количество дочерних страниц',
  `script_admin_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'Адрес страницы админки для скрипта',
  `script_manual_order` int(11) NOT NULL DEFAULT '0' COMMENT 'Дочерние страницы этого скрипта будут сортироватьс вручную',
  `sitemap_xml_priority` float NOT NULL DEFAULT '0' COMMENT 'Sitemap.XML приоритет страницы',
  `sitemap_xml_change` enum('always','hourly','daily','weekly','monthly','yearly','never') DEFAULT NULL COMMENT 'Sitemap.XML частота обновления страницы',
  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость страницы на front-ende',
  PRIMARY KEY (`id`),
  UNIQUE KEY `full_url` (`full_url`),
  KEY `parent` (`parent`),
  KEY `document` (`document_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sitemap_history`
--

DROP TABLE IF EXISTS `sitemap_history`;
CREATE TABLE `sitemap_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Старое имя страницы',
  `url` varchar(255) NOT NULL COMMENT 'Старый url страницы',
  `date` datetime NOT NULL COMMENT 'Дата попадания в историю',
  `page_id` int(11) NOT NULL COMMENT 'Индекс страницы в таблице sitemap',
  PRIMARY KEY (`id`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sitemap_scripts_child`
--

DROP TABLE IF EXISTS `sitemap_scripts_child`;
CREATE TABLE `sitemap_scripts_child` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL COMMENT 'Путь к скрипту',
  `document_name` varchar(255) NOT NULL COMMENT 'Имя документа (из таблицы register)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sitemap_template`
--

DROP TABLE IF EXISTS `sitemap_template`;
CREATE TABLE `sitemap_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL COMMENT 'Путь к шаблону',
  `comment` text NOT NULL COMMENT 'Комментарий по шаблону',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `system_register`
--

DROP TABLE IF EXISTS `system_register`;
CREATE TABLE `system_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL COMMENT 'Индекс ряда-предка',
  `name` varchar(40) NOT NULL COMMENT 'Имя ключа',
  `type` varchar(20) NOT NULL COMMENT 'Тип ключа',
  `comment` varchar(255) DEFAULT NULL COMMENT 'Комментарий к ключу',
  `value` varchar(1024) NOT NULL COMMENT 'Значение ключа',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `search_element_by_name` (`name`,`parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2063 ;

--
-- Дамп данных таблицы `system_register`
--

INSERT INTO `system_register` (`id`, `parent`, `name`, `type`, `comment`, `value`) VALUES
(4, 0, 'Applications', 'branch', 'Приложения', ''),
(5, 0, 'System', 'branch', 'Системная информация', ''),
(6, 5, 'types', 'branch', 'Информация о типах', ''),
(7, 4, 'sitemap', 'branch', 'Работа карты сайта', ''),
(8, 7, 'support_languages', 'integer', 'Поддержка языковых версий', '0'),
(52, 7, 'sitemap.xml', 'integer', 'Поддержка генерации файла files/sitemap.xml', '1'),
(53, 5, 'Front-end', 'branch', 'Работа публичной части сайта', ''),
(54, 53, 'need_cms_auth', 'integer', 'Для просмотра фронт-енда требуется авторизация в админке', '0'),
(55, 5, 'CMS', 'branch', 'Настройки связанные с работой CMS', ''),
(56, 55, 'display_administrate_scripts', 'integer', 'Заставляет выводить административные скрипты движка', '0'),
(57, 5, 'columns', 'branch', 'Настройки колонок документов', ''),
(58, 57, 'image', 'branch', 'Изображение', ''),
(59, 58, 'max_size', 'float', 'Настройки колонок документов', '1.5'),
(60, 53, 'display_cms_links', 'string', 'Включает отображение на сайте блока ссылок на редактирование в CMS шаблона/документа', '1'),
(61, 53, 'enable_debug', 'string', 'Включает отображение окна класса Trace', '1'),
(70, 7, 'sitemap.xml.disable', 'int', 'Отключает генерацию (на случай, если у вас уже есть скрипт)', '1'),
(71, 55, 'appwizard_storage', 'string', 'Адрес хранилища модулей', 'http://extasy-cms.ru/modules/modules.xml'),
(72, 5, 'email', 'branch', 'Конфиги связанные с работой почты', ''),
(73, 72, 'enable_ssl', 'string', 'Использует SSL', '1'),
(74, 72, 'from_email', 'string', 'Какое значение записывать в поле FROM у письма', 'no-reply@belarushockey.com'),
(75, 72, 'from_name', 'string', 'Подпись для письма ', 'Почтовый робот (belsmi.by)'),
(76, 72, 'smtp_password', 'string', 'Пароль пользователя', '7V5264'),
(77, 72, 'smtp_port', 'string', 'Порт соединения', '465'),
(78, 72, 'smtp_server', 'string', 'Сервер почты', 'smtp.gmail.com'),
(79, 72, 'smtp_user', 'string', 'Имя пользователя', 'no-reply@belarushockey.com'),
(82, 72, 'use_standart_mail_function', 'int', 'Если этот флаг включен, то почта будет отсылаться с помощью функции mail()', '0'),
(83, 4, 'cconfig', 'branch', 'Модуль редактируемый конфигов', ''),
(84, 83, 'user_control_path', 'branch', 'Если cconfig не может найти контрол, он пытается найти его в листьях данной ветви.', ''),
(85, 53, 'technical_message', 'string', 'Если пользователю необоходимо авторизироваться в контрольной панели, ему будет отображено это сообщение ', ''),
(86, 53, 'cms_auth_template', 'string', 'Template for CMS authorization form. Please fill field empty if you using standart', ''),
(2061, 2000, 'registered', 'string', '', '\\Extasy\\Columns\\Datetime'),
(2018, 55, 'StandartAdd', 'branch', '', ''),
(2019, 2018, 'Documents', 'branch', '', ''),
(2062, 2000, 'email_confirmation_code', 'string', '', '\\Extasy\\Columns\\Input'),
(2047, 7, 'RootDocuments', 'branch', '', ''),
(1433, 53, 'layout_for_404', 'string', 'Дефолтный layout для страниц ошибок 404', ''),
(1625, 7, 'visible', 'string', 'Устанавливает значение по умолчанию  для вновь созданных страниц', '0'),
(2000, 1714, 'fields', 'branch', NULL, ''),
(1984, 2000, 'last_activity_date', 'string', '', '\\Extasy\\Columns\\Datetime'),
(1990, 1989, 'form', 'string', '', '120x120'),
(1991, 1989, 'medium', 'string', '', '40x40'),
(1988, 2000, 'avatar', 'branch', '', ''),
(1989, 1988, 'images', 'branch', '', ''),
(1986, 2000, 'password', 'string', '', '\\Extasy\\Columns\\Password'),
(1701, 1696, 'redirect_referer_after_login', 'string', 'Если включена данная опция, то после авторизации пользователь делает редирект на  страницу откуда пришел', '1'),
(1702, 1693, 'registration_need_email', 'string', 'Если этот флаг включен, то для регистрации ОБЯЗАТЕЛЬНО нужен email', '0'),
(1693, 4, 'users', 'branch', 'Модуль пользователей', ''),
(2060, 2000, 'new_email', 'string', '', '\\Extasy\\Columns\\Input'),
(1716, 1714, 'title', 'string', '', 'Пользователь'),
(1972, 2000, 'id', 'string', '', '\\Extasy\\Columns\\Index'),
(1992, 1989, 'normal', 'string', '', '80x80'),
(1700, 1696, 'ignore_password_field', 'string', 'Установленное поле заставляет сайт не проверять старый пароль пользователя при обновлении профиля', '1'),
(1985, 2000, 'confirmation_code', 'string', '', '\\Extasy\\Columns\\Input'),
(1714, 6, 'users', 'branch', '', ''),
(1715, 1714, 'table', 'string', '', 'users'),
(1699, 1696, 'blocked_fields', 'string', 'Список полей, которые нельзя обновить через движок', 'id,confirmation_code,email,password,login'),
(1698, 1696, 'account_registration_success_email', 'string', 'Высылать ли Email с данными пользователю', '1'),
(1694, 1693, 'ajax_registration', 'string', 'Флаг обозначает, что регистрация доступна как ajax-функция (страница регистрации не показывается)', '0'),
(1695, 1693, 'captcha_provider', 'string', '', 'kcaptcha'),
(1696, 1693, 'front-end', 'branch', 'Работа на фронтенде', ''),
(1697, 1696, 'account_confirmation', 'string', 'Если данный флаг отключен, то подтверждение регистрации не требуется', '0'),
(1420, 55, 'userRights', 'branch', '', ''),
(1993, 1989, 'tiny', 'string', '', '24x24'),
(1994, 1988, 'base_dir', 'string', '', 'users/'),
(1995, 1988, 'class', 'string', '', '\\Extasy\\Columns\\Image'),
(1475, 5, 'Errors', 'branch', 'Настройка работы с ошибками', ''),
(1476, 1475, 'layout', 'string', 'Default layout for error views', 'default'),
(1756, 57, 'tags', 'branch', 'Теги', ''),
(1757, 1756, 'cacheLifeTime', 'string', 'Время жизни кеша', '3600'),
(1758, 1756, 'cacheKey', 'string', 'Ключ по которому хранится кеш ', 'system_columns_tag'),
(1759, 1756, 'cross_table', 'string', '', 'document2tag'),
(1760, 1756, 'tag_table', 'string', '', 'tags'),
(1977, 2000, 'email', 'string', '', '\\Extasy\\Columns\\Input'),
(1976, 2000, 'login', 'string', '', '\\Extasy\\Columns\\Input'),
(1976, 2000, 'login', 'string', '', '\\Extasy\\Columns\\Input'),
(1949, 53, 'pack', 'string', 'Удалять пробелы из возвращаемого контента', '0'),
(1956, 5, 'Security', 'branch', '', ''),
(1957, 1956, 'salt', 'string', '', 'Gr3atN3ws'),
(1973, 2000, 'name', 'string', '', '\\Extasy\\Columns\\Input'),
(1961, 5, 'Audit', 'branch', '', ''),
(1962, 1961, 'notification_emails', 'string', '', 'dmitry@dd-team.org'),
(1964, 5, 'Developer', 'branch', '', ''),
(1965, 1964, 'Events', 'branch', '', ''),
(1966, 1965, 'store_caller_code', 'integer', '', '0');

-- --------------------------------------------------------

--
-- Структура таблицы `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_description` text NOT NULL,
  `seo_keywords` text NOT NULL,
  `seo_news_keywords` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL DEFAULT '',
  `last_activity_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `password` varchar(255) NOT NULL DEFAULT '',
  `confirmation_code` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `avatar_normal` varchar(255) NOT NULL DEFAULT '',
  `avatar_medium` varchar(255) NOT NULL DEFAULT '',
  `avatar_tiny` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `facebook_id` varchar(20) NOT NULL DEFAULT '',
  `vkontakte_id` varchar(20) NOT NULL DEFAULT '',
  `surname` varchar(255) NOT NULL DEFAULT '',
  `fathers_name` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `profession` varchar(255) NOT NULL DEFAULT '',
  `email_confirmation_code` varchar(32) NOT NULL DEFAULT '',
  `new_email` varchar(32) NOT NULL DEFAULT '',
  `avatar_preview` varchar(255) NOT NULL DEFAULT '',
  `persistent` int(11) NOT NULL DEFAULT '0',
  `smi_id` int(11) NOT NULL,
  `archive_access_from` date NOT NULL,
  `archive_access_to` date NOT NULL,
  `street` varchar(255) NOT NULL,
  `house` varchar(255) NOT NULL,
  `house_sub_number` varchar(255) NOT NULL,
  `flat` varchar(255) NOT NULL,
  `registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `website_url` varchar(255) NOT NULL,
  `en_priority` int(11) NOT NULL DEFAULT '0',
  `en_percent` float NOT NULL DEFAULT '0',
  `en_views` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5000 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `last_activity_date`, `password`, `confirmation_code`, `email`, `name`, `comment_count`, `avatar_normal`, `avatar_medium`, `avatar_tiny`, `avatar`, `facebook_id`, `vkontakte_id`, `surname`, `fathers_name`, `city`, `profession`, `email_confirmation_code`, `new_email`, `avatar_preview`, `persistent`, `smi_id`, `archive_access_from`, `archive_access_to`, `street`, `house`, `house_sub_number`, `flat`, `registered`, `website_url`, `en_priority`, `en_percent`, `en_views`) VALUES
(100, 'root', '2014-08-12 14:11:17', 'fcV.WEl9E5mXM', '', 'd@dd-team.org', 'Дмитрий', 0, '', '', '', 'users/4858.jpg', '1', '2', 'Шевченко', 'Сергеевич', '1222', '22232', '', '', 'users/avatar_preview/4858.jpg', 1, 1, '2014-05-07', '2015-05-07', '2', '3', '4', '5', '2014-07-18 06:29:10', 'http://habrahabr.ru/', 2, 0, 866);


CREATE TABLE `schedule_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL DEFAULT '',
  `class` varchar(255) NOT NULL DEFAULT '',
  `dateCreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `actionDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `repeatable` int(11) NOT NULL DEFAULT '0',
  `period` int(11) NOT NULL DEFAULT '0',
  `data` blob,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=440 ;
