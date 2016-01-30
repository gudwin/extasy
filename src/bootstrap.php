<?php
namespace {

    use \Faid\Configure\Configure;
    use Faid\Debug\Debug;

    /**
     * Файл-загрузчик необходимых модулей
     * @author Gisma d@dd-team.org
     */
    // Стартуем кеширование вывода
    ob_start();
    // Определение корня сайта
    if (!defined('SYS_ROOT')) {
        define('SYS_ROOT', realpath(dirname(__FILE__) . '/../../../') . '/');
    }
    // БАЗОВЫЕ НАСТРОЙКИ системы (пути и прочее)
    if (!defined('HTTP_ROOT')) {
        // Установка HTTP корня сайта
        // Попытка определения текущего хоста

        if (!empty($_SERVER['HTTP_HOST'])) {
            $szHTTPRoot = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        } else {
            $szHTTPRoot = 'http://localhost/';
        }
        define('HTTP_ROOT', $szHTTPRoot);
    }
    // Webroot
    define('WEBROOT_PATH', SYS_ROOT . 'public_html/');
    // Vendor path
    define('VENDOR_PATH', SYS_ROOT . 'vendor/');
    // Путь к Extasy CMS
    define('LIB_PATH', __DIR__ . '/');
    define('EXTASY_PATH', LIB_PATH . '../');
    // Путь к приложение
    define('APPLICATION_PATH', SYS_ROOT . 'application/');
    // Путь к конфигам
    define('CFG_PATH', APPLICATION_PATH . 'Configs/');
    // Путь к шаблонам
    define('VIEW_PATH', APPLICATION_PATH . 'views/');
    // Путь к конфигам настроек
    define('SETTINGS_PATH', CFG_PATH . 'sitedata/');

    // Работа с mb_string
    ini_set('mbstring.internal_encoding', 'utf-8');

    // Алиас SYS_ROOT
    define('SYS_ADMIN_ROOT', SYS_ROOT);
    // Путь к контрольной панели
    define('CP_PATH', WEBROOT_PATH . 'admin/');

    // ACL constants
    define('ACL_TABLE', 'acl_actions');
    define('ACL_GRANT_TABLE', 'acl_grants');
    // System Register
    define('SYSTEMREGISTER_TABLE', 'system_register');
    define('SYSTEMREGISTER_BRANCH_TYPE', 'branch');
    define('SYSTEM_REGISTER_GET_CACHE', 'system_register_get');
    define('SYSTEM_REGISTER_CHILD_CACHE', 'system_register_child');
    // SITEMAP constants
    define('SITEMAP_TABLE', 'sitemap');
    define('SITEMAP_SCRIPT_CHILD_TABLE', 'sitemap_scripts_child');
    define('SITEMAP_HISTORY_TABLE', 'sitemap_history');
    // Custom Config
    define('CCONFIG', 'cconfig');
    define('CCONFIG_SCHEMA_TABLE', 'custom_config_schema');
    define('CCONFIG_TABSHEETS_TABLE', 'custom_config_groups');
    define('CCONFIG_CONTROL_TABLE', 'custom_config_items');
    define('CCONFIG_CONTROLS_PATH', LIB_PATH . 'custom_config/controls/');
    //
    define('USERS', 'users');
    define('USERS_TABLE', 'users');
    // Core packages
    require_once LIB_PATH . 'kernel/extasy.php';

    //
    Trace::start();

    //


    Configure::write('Schedule.AutoloadScriptPath', APPLICATION_PATH . 'scheduleRestart.php');

    Configure::write(
        'Exception.Handler', array('\\Extasy\\errors\\Handlers', 'onException')
    );

    Configure::write(
        'Error.Handler', array('\\Extasy\\errors\\Handlers', 'onError')
    );

    Configure::write(
        'FatalError.Handler', array('\\Extasy\\errors\\Handlers', 'onFatalError')
    );

    require_once LIB_PATH . 'ClassLocator.php';
    require_once LIB_PATH . 'Autoloader.php';

    \Extasy\Autoloader::startup();
    Debug::enable();
    \EventController::addRuntimeEventListener(\Extasy\Api\ApiOperation::EventName, function () {
        \Extasy\Audit\Api\ApiOperation::startUp();
        \Extasy\Users\UsersModule::initAPI();
    });


}