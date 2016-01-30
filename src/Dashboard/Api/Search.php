<?php


namespace Extasy\Dashboard\Api;


use Extasy\CMS;
use Extasy\sitemap\Models\SearchResultModel;
use Extasy\sitemap\Models\SitemapModel;
use \Extasy\acl\ACLUser;

class Search extends \Extasy\Users\Api\ApiOperation
{
    const EventName = 'dashboard.search';
    const MethodName = 'dashboard.search';
    protected $results = [];
    protected $requiredACLRights = [\CMSAuth::AdministratorRoleName];
    protected $requiredParams = ['request'];
    protected $searchPhrase = '';

    protected function action()
    {
        $this->searchPhrase = $this->getParam('request');
        $this->searchUsers();
        $this->searchSitemap();
        $results = \EventController::callEvent(self::EventName, [$this->searchPhrase, $this->results]);
        foreach ( $results as $row ) {
            $this->results = array_merge( $this->results, $row );
        }

        return [
            'items' => $this->results
        ];
    }

    protected function searchUsers()
    {
        try {
            ACLUser::checkCurrentUserGrants([\UserAccount::PermissionName]);
            $items = \UsersDBManager::searchByLogin($this->searchPhrase);
        } catch (\Exception $e) {
            $items = [];
        }

        foreach ($items as $row) {
            $route = CMS::getInstance()->getDispatcher()->getNamed('dashboard.users.manage');
            $add = new SearchResultModel();
            $add->title = $row['login'];
            $add->icon = 'glyphicon glyphicon-user';
            $add->link = $route->buildUrl() . '?id=' . $row['id'];
            $this->results[] = $add;
        }
    }

    protected function searchSitemap()
    {
        try {
            ACLUser::checkCurrentUserGrants([SitemapModel::PermissionName]);
            $items = \Sitemap_Sample::search($this->searchPhrase, 0, 10);
        } catch (\Exception $e) {
            $items = [];
        }
        foreach ($items as $row) {
            $isScript = !empty( $row['script_admin_url']);
            $add = new SearchResultModel();
            $add->title = $row['name'];
            $add->icon = 'glyphicon glyphicon-user';
            //
            if ( !$isScript  ) {
                $route = CMS::getInstance()->getDispatcher()->getNamed('dashboard.sitemap.manage');
                $add->link = $route->buildUrl() . '?id=' . $row['id'];
            } else {
                $add->link = sprintf('http://%s%s',CMS::getDashboardWWWRoot(),$row['script_admin_url']);
            }
            $this->results[] = $add;
        }
    }
} 