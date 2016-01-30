<?php
// define empty namespace extasy
namespace Extasy;

use Extasy\Dashboard\Api\Search;
use Faid\Configure\Configure;
use Faid\Configure\ConfigureException;
use \Faid\Dispatcher\Dispatcher;
use \CMSAuth;
use \Extasy\sitemap\Route as sitemapRoute;
use \Faid\Dispatcher\HttpRoute;
use \SystemRegisterSample;
use \UserAccount;

/**
 * @package extasycms
 */
class CMS
{
    const Version = '4.4';

    const SaltConfigureKey = 'Security.Salt';
    const DashboardConfigureKey = 'Dashboard';
    const MainDomainConfigureKey = 'MainDomain';
    const ResourcesDomainConfigureKey = 'ResourcesDomain';
    const ProfileDomainConfigureKey = 'ProfileDomain';
    const UnitTestCookieName = 'e5_tests';
    const SystemInitEvent = 'System.init';
    const FileConfigureKey = 'FilesDirectory';
    const FilesHttpRoot = 'FilesHttpRoot';

    const DispatchErrorsLog = 'Dispatcher';

    protected static $config = array();

    /**
     * @var Dispatcher
     */
    protected $dispatcher = null;

    protected $dispatchGranted = true;

    /**
     * @var CMS
     */
    protected static $instance = null;

    protected $auth = null;

    protected $activeRoute = null;

    protected $currentEnvironment = '';

    protected $loadEnvironment = false;

    public function __construct(Dispatcher $dispatcher, $environment = null)
    {
        if ( empty( self::$instance )) {
            self::$instance = $this;
        }


        \Trace::addMessage('CMS', 'CMS::load');
        $this->loadEnvironment($environment);
        \Faid\DB::checkConnection();
        SystemRegisterSample::startup();


        $this->dispatcher = $dispatcher;
        self::autoloadConfig();
        $this->initializeRoutes();
        $this->initializeApis();
        \Trace::addMessage('CMS', '`init` event');
        \EventController::callEvent(self::SystemInitEvent, $this);
        $this->processSecurity();
        \Trace::addMessage('CMS', 'Стартовало');
    }
    public function setLoadEnvironmentFlag( $value ) {
        $this->loadEnvironment = (bool)$value;
    }

    /**
     * Starts extasy CMS functions
     */
    public static function autoload(Dispatcher $dispatcher)
    {
        return self::$instance;

    }

    protected function loadEnvironment($environment = null)
    {
        if ( !$this->loadEnvironment ) {
            return ;
        }
        // Загружаем загрузчик сайта
        \Trace::addMessage('CMS', 'init.php loaded');

        $initScript = APPLICATION_PATH . '/init.php';
        if ( file_exists( $initScript) ) {
            require_once $initScript;
        } else {
            die('Couldn`t locate initial (init.php) script');
        }


        if (is_null($environment)) {
            $map = include APPLICATION_PATH . 'environmentMap.php';
            $detector = new EnvironmentDetector( $map );
            $environment = $detector->detect( $_SERVER );
        }
        $this->currentEnvironment = $environment;

        $path = SYS_ROOT . 'Environment/' . $environment . '/init.php';

        if (!file_exists($path)) {
            throw new \RuntimeException('Unable to load environment: ' . $environment);
        }
        require_once $path;
    }

    protected static function autoloadConfig()
    {
        $config = Configure::read(self::DashboardConfigureKey);
        $defaultConfig = array(
            'url' => '/admin/'
        );
        self::$config = array_merge($defaultConfig, $config);
    }

    protected function initializeRoutes()
    {

        include __DIR__ . DIRECTORY_SEPARATOR . 'dashboard_routes.php';
        $mainDomain = self::getMainDomain();
        $this->dispatcher->addRoute(new sitemapRoute());
        $this->dispatcher->addRoute(new HttpRoute(array(
            'url' => $mainDomain . '/api/',
            'controller' => '\\Extasy\\Api\\ApiController',
            'action' => 'process'
        )));
        $this->dispatcher->addRoute(new HttpRoute(array(
            'url' => 'http://Service/schedule/',
            'controller' => '\\Extasy\\Schedule\\Runner',
            'action' => 'resolveJobs'
        )));
    }

    protected function initializeApis()
    {
        \EventController::addRuntimeEventListener(\Extasy\Api\ApiOperation::EventName,
            function () {
                $controller = \Extasy\Api\ApiController::getInstance();
                $controller->add(new \Extasy\Schedule\Api\Add());
                $controller->add(new \Extasy\Schedule\Api\Cancel());
                $controller->add(new \Extasy\Schedule\Api\Latests());
                $controller->add(new \Extasy\Schedule\Api\Restart());
                $controller->add(new \Extasy\Schedule\Api\RestartServer());
                $controller->add(new \Extasy\Schedule\Api\ServerStatus());
                $controller->add(new \Extasy\Schedule\Api\StopServer());
                $controller->add(new Search());
            });
    }

    public static function getMainDomain()
    {
        return Configure::read(self::MainDomainConfigureKey);
    }
    public static function getProfileDomain() {
        return Configure::read( self::ProfileDomainConfigureKey );
    }
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function dispatch()
    {
        if (!$this->dispatchGranted) {
            return;
        }
        try {
            $this->activeRoute = $this->dispatcher->run();
        } catch (\Faid\Dispatcher\RouteException $e) {
            throw new \NotFoundException('No matching route found for url - ' . $this->dispatcher->getRequest()
                    ->url());
        }

        $this->activeRoute->dispatch();
    }

    public function getActiveRoute()
    {
        return $this->activeRoute;
    }

    public static function getDashboardWWWRoot()
    {
        if (empty(self::$config)) {
            self::autoloadConfig();
        }

        return '//' . self::$config['Domain'] . self::$config['url'];
    }

    public static function getWWWRoot()
    {
        $domain = self::getMainDomain();

        return sprintf('//%s/', $domain);
    }

    public static function getFilesPath()
    {
        return Configure::read(self::FileConfigureKey);
    }

    public static function getFilesHttpRoot()
    {
        return Configure::read(self::FilesHttpRoot);
    }
	public static function getResourcesDomain() {
		$domain = Configure::read(self::ResourcesDomainConfigureKey);
		return $domain;
	}
    public static function getResourcesUrl()
    {
        static $result = '';
        try {
            $domain = Configure::read(self::ResourcesDomainConfigureKey);
        } catch (ConfigureException $e) {
            $domain = self::getMainDomain();
        }
        if (!empty($result)) {
            return $result;
        } else {
            $result = sprintf('http://%s/resources/', $domain);

            return $result;
        }

    }

    protected function processSecurity()
    {
        $this->auth = CMSAuth::getInstance();

        // ignore security settings in command line mode
        if ($this->dispatcher->getRequest() instanceof \Faid\Request\CommandLineRequest) {
            return;
        }
        if (self::isUnitTestsRunning()) {
            return;
        }
        \Trace::addMessage('CMS', 'Проверка безопасности');
        if ($this->dispatcher->getRequest() instanceof Request) {
            $this->dispatcher->getRequest()->testForInjections();
            $this->dispatchGranted = $this->showAuthScreenIfNecessary();
        }
        \Extasy\Audit\DDosDetector::detect();
    }

    protected function showAuthScreenIfNecessary()
    {
        // Требуется вывод формы авторизации?
        $register = new \SystemRegister('System/Front-end');
        if ($register->need_cms_auth->value != 0) {
            $currentDomain = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            $exceptions = explode("\n", $register->ignore_cms_auth_4_domains->value);
            if (!in_array($currentDomain, $exceptions)) {
                return $this->auth->check();
            }
        }

        return true;
    }

    /**
     * @return CMS
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public static function isUnitTestsRunning()
    {
        $isTests = self::checkIfUnitTestHttpRequest();

        return $isTests;
    }

    public static function getUnitTestCookie()
    {
        $string = SITE_NAME . Configure::read('Security.Salt');

        return md5($string);
    }

    protected static function checkIfUnitTestHttpRequest()
    {
        $cookieSet = isset($_COOKIE[self::UnitTestCookieName]);
        if ($cookieSet) {
            $isEqual = $_COOKIE[self::UnitTestCookieName] == self::getUnitTestCookie();

            return $isEqual;
        }

        return false;
    }


}