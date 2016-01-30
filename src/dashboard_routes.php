<?php
use \Faid\Dispatcher\HttpRoute;
use \Extasy\Dashboard\Route as DashboardRoute;
use \Extasy\Users\registration\Dashboard\PageConfig as RegistrationPageConfig;

$map = array(
    array(
        'url' => '$',
        'controller' => '\\Extasy\\Dashboard\\Controllers\\Index',
        'action' => 'main',
    ),
    array(
        'url' => 'zoom.php',
        'controller' => '\\Extasy\\Controllers\\ZoomPage',
        'action' => 'process',
    ),
    array(
        'url' => 'administrate/testSuite/edit.php',
        'callback' => function () {
                extasyTestAdminPage::edit();
            }
    ),
    array(
        'url' => 'administrate/testSuite/quick_add',
        'callback' => function () {
                extasyTestAdminPage::quick_add();
            }
    ),
    array(
        'url' => 'administrate/testSuite/index.php',
        'callback' => function () {
                extasyTestAdminPage::main();
            }
    ),
    array(
        'url' => 'administrate/testSuite/list.php',
        'callback' => function () {
                extasyTestAdminPage::dataList();
            }
    ),
    array(
        'url' => 'administrate/testSuite/quick_add.php',
        'callback' => function () {
                extasyTestAdminPage::quick_add();
            }
    ),
    array(
        'url' => 'administrate/acl.php',
        'controller' => 'ACLAdminManageActions',
        'action' => 'process'
    ),
    array(
        'url' => 'administrate/security',
        'controller' => '\\Extasy\\Dashboard\\Controllers\\Security',
        'action' => 'process'
    ),
    array(
        'url' => 'administrate/audit',
        'callback' => function () {
                \Extasy\Audit\Controllers\Audit::startup();
            }
    ),
    array(
        'url' => 'administrate/cache.php',
        'callback' => function () {
                \Extasy\Dashboard\Controllers\Cache::startup();
            }
    ),
    array(
        'url' => 'administrate/create_section.php',
        'controller' => 'SitemapCreateSectionPage',
        'action' => 'process'
    ),
    array(
        'url' => 'administrate/manage_document.php',
        'controller' => 'ManageDocumentAdminPage',
        'action' => 'process'
    ),
    array(
        'url' => 'administrate/php_console.php',
        'controller' => 'PhpConsole',
        'action' => 'process'
    ),
    array(
        'url' => 'administrate/regedit.php',
        'controller' => 'SystemRegisterAdministrate',
        'action' => 'process',
    ),
    array(
        'url' => 'administrate/setup_events.php',
        'controller' => 'RegisterSetupEvents',
        'action' => 'process',
    ),
    array(
        'url' => 'administrate/sql.php',
        'controller' => 'AdminSqlConsole',
        'action' => 'process'
    ),
    array(
        'url' => 'administrate/template-manager.php',
        'controller' => '\\Extasy\\sitemap\\controller\\TemplateManager',
        'action' => 'process'
    ),
    array(
        'url' => 'administrate/schedule',
        'controller' => '\\Extasy\\Schedule\\ScheduleDashboard',
        'action' => 'process'
    ),
    // Custom config
    array('url' => 'cconfig/index.php', 'controller' => 'CConfigAdminIndexPage', 'action' => 'process'),
    array('url' => 'cconfig/edit.php', 'controller' => 'CConfigAdminEditPage', 'action' => 'process'),
    array('url' => 'cconfig/manage.php', 'controller' => 'CConfigAdminManagePage', 'action' => 'process'),
    // Email
    array('url' => 'email/config.php', 'controller' => 'Email_Admin_Config', 'action' => 'process'),
    array('url' => 'email/index.php', 'controller' => 'Email_Admin_Index', 'action' => 'process'),
    array('url' => 'email/logs.php', 'controller' => 'Email_Logs_Admin', 'action' => 'process'),
    // List
    array('url' => 'list/edit.php', 'controller' => 'CMS_DataManage', 'action' => 'process'),
    array('url' => 'list/index.php', 'controller' => 'CMS_Page_DataList', 'action' => 'process'),
    array('url' => 'list/order.php', 'controller' => 'Extasy\\kernel\\cms\\_pages\\ListOrder', 'action' => 'process'),
    // Index
//	array( 'url'        => 'index/index.php',
//		   'controller' => 'Extasy\\sitemap\\controller\\IndexPage',
//		   'action'     => 'process'
//	),
    // Sitemap
    array('url' => 'sitemap/aliases.php', 'controller' => 'SitemapAliasesAdmin', 'action' => 'process'),
    array(
        'url' => 'sitemap/edit.php',
        'controller' => 'SiteMap_Controller_Edit',
        'action' => 'process',
        'name' => 'dashboard.sitemap.manage'
    ),
    array('url' => 'sitemap/get-parents.php', 'controller' => 'Sitemap_Controller_GetParents', 'action' => 'process'),
    array(
        'url' => 'sitemap/getinformation.php',
        'controller' => 'SiteMap_Controller_Information',
        'action' => 'process'
    ),
    array(
        'url' => 'sitemap/go-additional.php',
        'controller' => 'SiteMap_Controller_Additional',
        'action' => 'process'
    ),
    array('url' => 'sitemap/move.php', 'controller' => 'Sitemap_MoveController', 'action' => 'process'),
    array('url' => 'sitemap/order.php', 'controller' => 'SiteMap_Controller_Order', 'action' => 'process'),
    array(
        'url' => 'sitemap/page-list.php',
        'controller' => '\\Extasy\\sitemap\\controller\\Children',
        'action' => 'process'
    ),
    array('url' => 'sitemap/search.php', 'controller' => 'Sitemap_Controller_Search', 'action' => 'process'),
    array('url' => 'sitemap/standart-add.php', 'controller' => 'SitemapStandartAddPage', 'action' => 'process'),
    // Columns
    array('url' => 'columns/tags.php', 'controller' => '\\Extasy\\Columns\\Controllers\\Tags', 'action' => 'process'),
    array(
        'url' => 'columns/htmlarea.php',
        'controller' => '\\Extasy\\Columns\\Controllers\\Htmlarea',
        'action' => 'process'
    ),
    // Users
    array(
        'url' => 'users/group_permissions/',
        'controller' => '\\Extasy\\Users\\admin\\GroupPermissions',
        'action' => 'process'
    ),
    array(
        'url' => 'users/forgot_password/email.php',
        'callback' => function () {
                \Extasy\Users\forgot\Dashboard\EmailConfig::startup();
            }
    ),
    array(
        'url' => 'users/forgot_password/new_password_email.php',
        'callback' => function () {
                \Extasy\Users\forgot\Dashboard\EmailConfig::startup();
            }
    ),
    array(
        'url' => 'users/forgot_password/index',
        'callback' => function () {
                \Extasy\Users\forgot\Dashboard\PageConfig::startup();
            }
    ),
    array(
        'url' => 'users/login/index',
        'callback' => function () {
                \Extasy\Users\login\Dashboard\PageConfig::startup();
            }
    ),
    array(
        'url' => 'users/profile/index',
        'callback' => function () {
                \Extasy\Users\profile\Dashboard\PageConfig::startup();
            }
    ),
    array(
        'url' => 'users/profile/DeleteProfileEmailConfig.php',
        'callback' => function () {
                $page = new \Extasy\Users\profile\Dashboard\DeleteProfileEmailConfig();
                $page->process();
            }
    ),
    array(
        'url' => 'users/profile/UpdateEmailConfig.php',
        'controller' => '\\Extasy\\Users\\admin\\UpdateEmailConfig',
        'action' => 'process'
    ),
    array(
        'url' => 'users/profile/UpdatePassword',
        'controller' => '\\Extasy\\Users\\admin\\UpdatePasswordConfig',
        'action' => 'process'
    ),
    array('url' => 'users/registration/confirm.php', 'controller' => 'Users_Admin_Index', 'action' => 'process'),
    array(
        'url' => 'users/registration/email.php',
        'controller' => '\Extasy\Users\registration\Dashboard\Email',
        'action' => 'process',
    ),
    array(
        'url' => 'users/registration/email-confirmation.php',
        'controller' => '\\Extasy\\Users\\registration\\Dashboard\\EmailConfirmation',
        'action' => 'process'
    ),
    array(
        'url' => 'users/registration/index',
        'callback' => function () {
                RegistrationPageConfig::startUp();
            },
    ),
    array(
        'url' => 'users/registration/success.php',
        'controller' => '\\Extasy\\Users\\registration\\Dashboard\\Success',
        'action' => 'process'
    ),
    //	array( 'url' => 'user_info/index.php', 'controller' => '\\Dashboard\\GlobalSettings', 'action' => 'process' ),
    array('url' => 'users/index.php', 'controller' => 'Users_Admin_Index', 'action' => 'process'),
    array(
        'url' => 'users/manage',
        'controller' => '\\Extasy\\Users\\admin\\AccountDashboard',
        'action' => 'process',
        'name' => 'dashboard.users.manage'
    ),
    array('url' => 'users/search', 'controller' => 'Users_Admin_Search', 'action' => 'process'),
    // Logout
    array(
        'url' => 'logout.php',
        'callback' => function () {
                $auth = \CMSAuth::getInstance();
                $auth->unAuthorize('/');
            }
    )
);
$prefix = $this->getDashboardWWWRoot();
foreach ($map as $row) {
    $row['url'] = $prefix . $row['url'];
    $route = new DashboardRoute($row);
    $this->dispatcher->addRoute($route);
}

$loginRouteConfig = array(
    'url' => $prefix . '$',
    'controller' => '\\Extasy\\Dashboard\\Controllers\\Index',
    'action' => 'showLoginForm',
);

$this->dispatcher->addRoute(new HttpRoute($loginRouteConfig));

$routes = [
    [
        'url' => 'http://Service/testFiles',
        'controller' => '\\Extasy\\Service\\Validator',
        'action' => 'testFiles'
    ],
    [
        'url' => 'http://Service/fixture',
        'controller' => '\\Extasy\\Service\\Fixture',
        'action' => 'setUp'
    ],
    [
        'url' => 'http://Service/csv',
        'controller' => '\\Extasy\\Service\\Validator',
        'action' => 'csv'
    ],
    [
        'url' => 'http://Service/models/createTable/:modelName',
        'controller' => '\\Extasy\\Service\\Models',
        'action' => 'createTable'
    ]
];
foreach ($routes as $route) {
    $this->dispatcher->addRoute(new HttpRoute($route));
}