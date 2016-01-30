<?php


namespace Extasy\tests\Dashboard\Api;


use Extasy\Dashboard\Api\Search;
use Extasy\sitemap\Models\SearchResultModel;
use Extasy\tests\Helper;

class SearchTest extends \Extasy\tests\BaseTest
{
    const PASSWORD = 'a123456!';

    public function setUp()
    {
        parent::setUp();

        \EventController::cleanUp();
        \ACL::create(\CMSAuth::SystemAdministratorRoleName);
        \ACL::create(\CMSAuth::AdministratorRoleName);
        \ACL::create(\UserAccount::PermissionName);
        \ACL::create(\Extasy\sitemap\Models\SitemapModel::PermissionName);

            Helper::dbFixtures([

            SITEMAP_TABLE => [
                ['name' => 'root', 'full_url' => '/root/','document_id' => 1],
                ['name' => '/apple/', 'full_url' => '/apple/','document_id' => 1],
                ['name' => '/banana/', 'full_url' => '/banana/','document_id' => 1],
                ['name' => 'orange', 'full_url' => '/orange/','document_id' => 1],
                ['name' => '/pineapple/', 'full_url' => '/pineapple/','document_id' => 1],
            ],
        ]);
        Helper::setupUsers([
                [
                    'login' => 'root',
                    'password' => self::PASSWORD,
                    'rights' => [\CMSAuth::SystemAdministratorRoleName => 1]
                ],
                [
                    'login' => 'clean_admin',
                    'password' => self::PASSWORD,
                    'rights' => [\CMSAuth::AdministratorRoleName => 1]
                ],
                [
                    'login' => 'users',
                    'password' => self::PASSWORD,
                    'rights' => [\CMSAuth::AdministratorRoleName => 1, \UserAccount::PermissionName => 1]
                ],
                [
                    'login' => 'sitemap',
                    'password' => self::PASSWORD,
                    'rights' => [
                        \CMSAuth::AdministratorRoleName => 1,
                        \Extasy\sitemap\Models\SitemapModel::PermissionName => 1
                    ]
                ]
            ]
        );

        \UsersLogin::logout();


    }

    public function tearDown()
    {
        parent::tearDown();
        \UsersLogin::logout();
    }

    public function testByDifferentUsers()
    {
        $map = [
            'clean_admin' => 0,
            'root' => 2,
            'users' => 1,
            'sitemap' => 1,
        ];
        foreach ($map as $user => $count) {
            \UsersLogin::login($user, self::PASSWORD);
            $api = new Search([
                'request' => 'root',
            ]);
            $response = $api->exec();

            $this->assertTrue(is_array($response));

            $this->assertEquals($count,sizeof($response['items']));

        }
    }

    public function testSearch()
    {
        \UsersLogin::login('root', self::PASSWORD);
        $api = new Search([
            'request' => 'root',
        ]);
        $response = $api->exec();

        $this->assertTrue(is_array($response));
        $this->assertEquals(sizeof($response['items']), 2);
        $this->assertEquals($response['items'][0]->title->getValue(), 'root');
        $this->assertEquals($response['items'][1]->title->getValue(), 'root');

        $this->assertUrlMatchesRoute('dashboard.users.manage', $response['items'][0]->link->getValue());
        $this->assertUrlMatchesRoute('dashboard.sitemap.manage', $response['items'][1]->link->getValue());

        $api = new Search([
            'request' => 'oran',
        ]);
        $response = $api->exec();
        $this->assertTrue(is_array($response));
        $this->assertEquals(sizeof($response['items']), 1);
        $this->assertEquals($response['items'][0]->title->getValue(), 'orange');

    }

    protected function assertUrlMatchesRoute($route, $url)
    {
        $request = new \Faid\Request\HttpRequest();
        $request->url($url);
        $route = \Extasy\CMS::getInstance()->getDispatcher()->getNamed($route, $request);

        $this->assertTrue((bool)$route->test($request));
    }

    public function testAddCustomListener()
    {
        \UsersLogin::login( 'root', self::PASSWORD );
        \EventController::addRuntimeEventListener(Search::EventName, function ($request) {

            $result = new SearchResultModel();
            $result->title = 'world!';
            $results = [$result];

            return $results;
        });
        $api = new Search([
            'request' => 'hello?',
        ]);
        $response = $api->exec();
        $this->assertEquals('world!', $response['items'][0]->title->getValue());
    }
} 