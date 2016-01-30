<?php
use \Faid\UParser;
use \Faid\DB;
use \Faid\Page;
use \UsersLogin;
use \Extasy\Dashboard\Controllers\Menu;
use \Extasy\acl\ACLUser;

//************************************************************//
//                                                            //
//                  Базовый класс Страницы                    //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: gis2002@inbox.ru                              //
//                                                            //
//  Разработчик: Gisma (13.01.2006)                           //
//  Модифицирован:  27.06.2006  by Gisma                      //
//                                                            //
//************************************************************//
class extasyPage extends Page
{
	const DashboardMenuKey = '/System/Front-end/enable_dashboard_menu';
	
    const ErrorKey = 'cms_error';
    protected $systemConfig = null;

    protected $htmlResponse = '';

    /**
     * @var \Extasy\Dashboard\Controllers\Menu
     */
    protected $menu;

    /**
     * @var \Extasy\Model\Model
     */
    protected $document = null;

    protected $aclActionList = array();

    const debugCode = 1;

    ///////////////////////////////////////////////////////////////////////////
    // Public methods
    /**
     *   Данный метод на вход принимает текстовое сообщение, которое он должен доставить до пользователя
     * @return null
     */
    public static function addAlert($szMessage)
    {
        if (empty($_SESSION['__page_alert'])) {
            $_SESSION['__page_alert'] = array();
        }
        $_SESSION['__page_alert'][] = $szMessage;
    }

    public static function addError($szError)
    {
        if (empty($_SESSION[self::ErrorKey])) {
            $_SESSION[self::ErrorKey] = [];
        }
        $_SESSION[self::ErrorKey][] = !empty($_SESSION[self::ErrorKey]) ? ($_SESSION[self::ErrorKey] . '<br/>' . htmlspecialchars($szError)) : htmlspecialchars($szError);
    }


    public function process()
    {
        ACLUser::checkCurrentUserGrants($this->aclActionList);

        return parent::process();
    }

    ///////////////////////////////////////////////////////////////////////////
    // Protected methods
    /**
     * Данный метод завершает работу страницы и просто выводит алерты
     */
    protected function output()
    {
        $this->showAlerts();
        $this->loadSystemConfig();
        $this->outputDebugResults();
        $this->outputDashboardMenu();
        if (!empty($this->htmlResponse)) {
            print $this->htmlResponse;
        }
        parent::output();
    }

    protected function outputDashboardMenu()
    {
        if ( ! $this->testIfDashboardMenuShouldBeShown() ) {
        	return ;
        }
        
        
       	$this->initilizeDashboardMenu();
        $dashboardCode = $this->menu->render();
        $this->insertIntoResponse($dashboardCode);

    }
    protected function testIfDashboardMenuShouldBeShown() {
    	$user = UsersLogin::getCurrentSession();
    	if ( empty( $user )) {
    		return false;
    	}
    	if ( !CMSAuth::isAdmin( $user )) {
    		return false;
    	}
    	$isMenuActivated = SystemRegisterHelper::getValue( self::DashboardMenuKey );
    	if ( !$isMenuActivated ) {
    		return false;
    	}
    	return true;
    }

    protected function initilizeDashboardMenu()
    {
        $this->menu = new Menu();
    }

    protected function outputDebugResults()
    {
        $this->loadSystemConfig();
        $value = intval($this->systemConfig->enable_debug->value);
        if ($value) {
            if (CMSAuth::getInstance()->isLogined()) {
                Trace::setDisabled(false);
                $this->insertIntoResponse(Trace::finish());
            }

        }
    }

    protected function loadSystemConfig()
    {
        if (empty($this->systemConfig)) {
            $this->systemConfig = new SystemRegister('/System/Front-end/');
        }
    }


    /**
     *
     * Enter description here ...
     *
     * @param unknown_type $szFileName
     * @param unknown_type $szMimeType
     * @param unknown_type $szContent
     */
    protected function sendFile($szFileName, $szMimeType = '', $szContent = '')
    {
        if (sizeof(func_get_args()) == 1) {
            require_once LIB_PATH . 'kernel/functions/mime.func.php';
            $szMimeType = mimeHelper::returnMIMEType($szFileName);
            $szContent = file_get_contents($szFileName);
            $szFileName = basename($szFileName);
        }
        httpHelper::sendFile($szFileName, $szMimeType, $szContent);
    }

    /**
     *
     * Enter description here ...
     */
    protected function showAlerts()
    {
        $processAlerts = function ($key, $path) {
            $result = '';
            $messages = !empty($_SESSION[$key]) ? $_SESSION[$key] : [];
            $_SESSION[$key] = [];

            foreach ($messages as $row) {
                $result .= UParser::parsePHPFile(
                    $path,
                    array(
                        'message' => $row
                    )
                );
            }

            return $result;
        };

        $messages = $processAlerts('__page_alert', VIEW_PATH . 'blocks/Alerts/message.tpl');
        $messages .= $processAlerts(self::ErrorKey, VIEW_PATH . 'blocks/Alerts/error.tpl');
        $this->insertIntoResponse( $messages );
    }

    protected function insertIntoResponse($insert)
    {
        $postfix = '</body>';
        $this->htmlResponse = str_replace($postfix, $insert . $postfix, $this->htmlResponse);
    }

    protected function setupAclActionsRequiredForAction($actionList)
    {
        $this->aclActionList = $actionList;
    }
}

?>