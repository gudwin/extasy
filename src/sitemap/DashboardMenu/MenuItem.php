<?php


namespace Extasy\sitemap\DashboardMenu;


use Extasy\acl\ACLUser;
use Extasy\Dashboard\Menu\MenuItemInterface;
use Extasy\sitemap\Models\SitemapModel;
use Faid\Configure\Configure;

class MenuItem implements MenuItemInterface
{
    const ConfigureKey = 'Sitemap.Menu';
    protected $title = '';
    protected $depth = 1;
    public function __construct() {
        $config = Configure::read( self::ConfigureKey );
        $this->depth = $config['depth'];
        $this->title = $config['title'];
    }
    public function getParseData()
    {
        $result = \SitemapMenu::selectMenu(0, $this->depth, false);

        $result = $this->iterateMenu($result);
        $result = [
            'name' => $this->title,
            'link' => '#',
            'children' => $result
        ];
        return $result;
    }

    protected function iterateMenu($children)
    {
        foreach ($children as $key => $row) {


            $isEditable = !empty( $row['script_admin_url']) || !empty( $row['document_name']);
            if ( !$isEditable ) {
                unset( $children[ $key ]);
                continue;
            }
            $item = new SitemapModel( $row );
            $children[$key] = [
                'name' => $row['name'],
                'link' => '#',
                'children' => [
                    ['name' => 'Редактировать','link' => $item->getEditLink(),]
                ],
            ];
            if (!empty($row['aChild'])) {
                $children[$key]['children'] = array_merge($children[$key]['children'], $this->iterateMenu($row['aChild']));
            }

        }

        return $children;
    }

    public function isVisible()
    {
        return ACLUser::hasUserRights([SitemapModel::PermissionName]);
    }

    public function getChildren()
    {
        return null;
    }
} 