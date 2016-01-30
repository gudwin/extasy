<?php
namespace Extasy\Dashboard\Controllers;

use Extasy\Dashboard\Menu\ItemList;
use Extasy\Dashboard\Views\MenuHelper;
use Faid\Configure\ConfigureException;
use Faid\Exception;
use Faid\View\View;
use \Trace;
use \Faid\Configure\Configure;
use \Extasy\Audit\Record;
use Extasy\acl\ModelHelper;
use \Extasy\acl\ACLUser;
class Menu {
	const AutoloadMenuItemsConfigureKey = 'Extasy.Dashboard.Menu.Items';
	const FilterName =  'Extasy.Dashboard.FilterMenu';
    /**
     * @var \Extasy\Dashboard\Menu\ItemList
     */
    protected $items;

	protected $tplFile = '';

	protected $view = NULL;


	public function __construct() {
		$this->items = new ItemList();
		$this->autoloadMenuItems( );

		$tplPath     = sprintf('%s/../Views/menu.tpl', __DIR__);
		$this->view  = new View($tplPath);
		//
		$this->view->set('inDashboard', false );
	}

	public function getItems() {
		return $this->items;
	}

	public function setTemplate($tplFile) {
		if (!ACLUser::hasUserRights( [\CMSAuth::AdministratorRoleName ]) ) {
			return ;
		}
		if ( file_exists(VIEW_PATH . $tplFile . '.tpl') ) {
			$this->view->set('tplFile', \Extasy\CMS::getDashboardWWWRoot() . 'administrate/template-manager.php?tplFile=' . $tplFile);
		}
	}
	public function setDashboardFlag( ) {
		$this->view->set('inDashboard', true);
	}
	public function setAdminUrl( $adminUrl ) {
		if ( !empty($adminUrl) ) {
			$this->view->set('adminUrl', $adminUrl);
		}
	}
	public function setSitemapInfo($urlInfo) {

		$adminUrl = '';
		// Если это скрипт и у него обозначен admin_page
		if ( !empty($urlInfo[ 'script' ]) && !empty($urlInfo[ 'script_admin_url' ]) ) {
			$adminUrl = \Extasy\CMS::getDashboardWWWRoot() . $urlInfo[ 'script_admin_url' ];
		} // или если это документ
		elseif ( !empty($urlInfo[ 'document_name' ]) ) {

			if ( ModelHelper::isEditable( $urlInfo['document_name'] )) {
				$adminUrl = \Extasy\CMS::getDashboardWWWRoot() . 'sitemap/edit.php?id=' . $urlInfo[ 'id' ];
			}
		}
		if ( !empty( $adminUrl )) {
			$this->setAdminUrl( $adminUrl );
		}

	}


	public function render() {

		$this->initUserParseData();
		$this->initTraceButton();
		$this->initMenuItems();
		$this->initAdministrativeMenu( );
		$this->initAudit();

		return $this->view->render();
	}
	protected function autoloadMenuItems( ) {
		try {
			$menuItems = Configure::read(self::AutoloadMenuItemsConfigureKey);
		} catch( ConfigureException $e ) {
			return ;
		}

		$menuItems = \EventController::callFilter( self::FilterName, $menuItems );

		foreach ( $menuItems as $item ) {
			$this->items->add( $item );
		}
	}
	protected function initAudit( ) {
		try {
			$isAuditEnabled = \CMSAuth::getInstance()->isAuditor( \UsersLogin::getCurrentSession() );
            $this->view->addHelper( new MenuHelper(),'menuRenderer' );
			$this->view->set('auditMessages', $isAuditEnabled);
		} catch (\Exception $e ) {

		}
	}
	protected function initMenuItems( ) {
		$this->view->set('menuItems', $this->items->getParseData());
	}
	protected function initAdministrativeMenu( ) {
		$auth = \CMSAuth::getInstance();
		if ( $auth->isSuperAdmin( \UsersLogin::getCurrentUser()) ) {
			$this->view->set('showAdministrativeMenu', true);
		}

	}

	protected function initUserParseData() {
		$user = \UsersLogin::getCurrentSession();
		if ( is_object( $user )) {
			$userData = $user->getData();
			$this->view->set('currentUser', $userData);
		}

	}

	protected function initTraceButton() {
		if ( Trace::enabled() ) {
			$this->view->set( 'traceOutput' ,true);
		}
	}
}