<?php
namespace Extasy\tests\sitemap;

use Extasy\CMS;
use Extasy\ORM\DBSimple;
use Extasy\sitemap\DashboardMenu\MenuItem;
use Extasy\sitemap\Models\SitemapModel;
use Extasy\tests\sitemap\TestDocument;
use Extasy\tests\BaseTest;
use Extasy\tests\Helper;
use Faid\Configure\Configure;

class SitemapMenuItemTest extends BaseTest
{

    const AdminUser = 'sitemapAdmin';
    const GuestUser = 'guest';
    const Title = 'Sitemap Menu';

    public function setUp()
    {
        parent::setUp();
        $model = new TestDocument();
        $model->createDatabaseTable( true );
        Helper::dbFixture(SITEMAP_TABLE, []);
        \ACL::create( SitemapModel::PermissionName);
        Configure::write( 'Sitemap', [
            'Menu' => [
                'title' => self::Title,
                'depth' => 3
            ]
        ]);
        Helper::setupUsers([
            [
                'login' => self::AdminUser,
                'rights' => [SitemapModel::PermissionName => true]
            ],
            [
                'login' => self::GuestUser,
            ]
        ]);

        $documents = [
            ['name' => 'first','sitemap' => ['count' => 1 ]],
            ['name' => 'second'],
            ['name' => 'third', 'sitemap' => [ 'parent' => 1]],
        ];
        foreach ($documents as $key => $row) {
            $documents[$key] = new TestDocument($row);
            $documents[$key]->insert();

            $sitemapModel = new SitemapModel();
            $sitemapModel->name = $row['name'];
            $sitemapModel->full_url = $row['name'];
            $sitemapModel->linkToModel($documents[$key]);
            if (isset($row['sitemap'])) {
                foreach ( $row['sitemap'] as $key=>$value ) {
                    $sitemapModel->$key = $value;
                }
            }
            $sitemapModel->insert();
        }
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testIsVisible()
    {
        \UsersLogin::login(self::GuestUser, Helper::DefaultPassword);
        $item = new MenuItem();
        $this->assertFalse($item->isVisible());

        \UsersLogin::login(self::AdminUser, Helper::DefaultPassword);

        $this->assertTrue($item->isVisible());
    }

    public function testParseData()
    {
        $item = new MenuItem();
        $actual = $item->getParseData();
        $editLinkBase = sprintf('%ssitemap/edit.php?id=', CMS::getDashboardWWWRoot());

        $expected = [
            'name' => self::Title,
            'link' => '#',
            'children' => [
                [
                    'link' => '#',
                    'name' => 'first',
                    'children' => [
                        [
                            'link' => $editLinkBase . '1',
                            'name' => 'Редактировать',
                        ],
                        [
                            'link' => '#',
                            'name' => 'third',
                            'children' => [
                                [
                                    'link' => $editLinkBase . '3',
                                    'name' => 'Редактировать',
                                ],
                            ]
                        ]
                    ]
                ],
                [
                    'link' => '#',
                    'name' => 'second',
                    'children' => [
                        [
                            'link' => $editLinkBase . '2',
                            'name' => 'Редактировать',
                        ]
                    ]
                ]
            ]

        ];
        $this->assertEquals($expected, $actual);
    }
} 