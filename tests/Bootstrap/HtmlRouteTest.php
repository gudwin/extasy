<?php


namespace Extasy\tests\Bootstrap;


use Extasy\Dashboard\HtmlRoute;
use Extasy\Request;
use Extasy\tests\Helper;
use Extasy\tests\system_register\Restorator;

class HtmlRouteTest extends \Extasy\tests\BaseTest
{
    const RootLogin = 'root';
    const Password = 'a123456!';

    const FileName = '/hello-world.tpl';
    const UnknownFileName = '/unknown.tpl';

    const ParamName = '1234';

    public function setUp()
    {
        parent::setUp();
        Restorator::restore();
        Helper::setupUsers([
            [
                'login' => self::RootLogin,
                'password' => self::Password,
                'rights' => [\CMSAuth::AdministratorRoleName => true]
            ]
        ]);
        \UsersLogin::login(self::RootLogin, self::Password);

    }

    protected function getConfigFixture()
    {
        return [
            'url' => 'hello_world/:paramName',
            'path' => __DIR__ . self::FileName
        ];
    }

    protected function getRequestFixture()
    {
        $result = new Request();
        $result->uri('hello_world/' . self::ParamName);

        return $result;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithoutArgument()
    {
        $config = $this->getConfigFixture();
        unset($config['path']);
        new HtmlRoute($config);
    }

    public function testWithoutPermissions()
    {
        \UsersLogin::logout();
        //
        $route = new HtmlRoute($this->getConfigFixture());
        $route->test($this->getRequestFixture());
        $route->prepareRequest();
        $route->dispatch();

        $this->expectOutputRegex('#cms_auth#');


    }

    /**
     * @expectedException \Faid\View\Exception
     */
    public function testFileNotFound()
    {
        $config = $this->getConfigFixture();
        $config['path'] = __DIR__ . self::UnknownFileName;

        $route = new HtmlRoute($config);
        $route->test($this->getRequestFixture());

        $route->dispatch();


    }

    public function testHtmlRoute()
    {
        $route = new HtmlRoute($this->getConfigFixture());
        $route->test($this->getRequestFixture());
        $route->prepareRequest();
        $route->dispatch();

        $this->expectOutputString(sprintf( 'Hello world!Value:%s', self::ParamName ));

    }
} 