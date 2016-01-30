<?php
namespace Extasy {
	use \Faid\debug;

	class Autoloader {
		public static function startup() {
			__autoload(
				array(
					// ACL
					'acl'                             => LIB_PATH . 'acl/controller.php',
					'aclmisc'                         => LIB_PATH . 'acl/misc.php',
					'aclexception'                    => LIB_PATH . 'acl/exception.php',
					'caclgrant'                       => LIB_PATH . 'acl/control/grant.php',
					'grantcolumn'                     => LIB_PATH . 'acl/type/grant.php',
					// ACL Controllers
					'acladminmanageactions'           => LIB_PATH . 'acl/admin/index.php',
					// Email
					'email_controller'                => LIB_PATH . 'email/controller/send.php',
					'phpmailer'                       => LIB_PATH . "email/@phpmailer/class.phpmailer.php",
					'smtp'                            => LIB_PATH . "email/@phpmailer/class.smtp.php",
					'mailexception'                   => LIB_PATH . 'email/exception.php',
					'emaillogmodel'                   => LIB_PATH . 'email/log_model.php',
					'extasymailer'                    => LIB_PATH . 'email/ExtasyMailer.php',
					'Email_Admin_Config'              => LIB_PATH . 'email/admin/config.php',
					'Email_Admin_Index'               => LIB_PATH . 'email/admin/index.php',
					'Email_Logs_Admin'                => LIB_PATH . 'email/admin/logs.php',
					// Events
					'eventcontroller'                 => LIB_PATH . 'events/controller.php',
					'eventexception'                  => LIB_PATH . 'events/exception.php',
					// Sitemap-section
					'sitemapexception'                => LIB_PATH . 'sitemap/exception.php',
					'registereddocument'              => LIB_PATH . 'sitemap/model.php',
					'sitemapcontroller'               => LIB_PATH . 'sitemap/controller.php',
					'sitemap'                         => LIB_PATH . 'sitemap/manager.php',
					'sitemap_sample'                  => LIB_PATH . 'sitemap/sample.php',
					'sitemap_history'                 => LIB_PATH . 'sitemap/history.php',
					'sitemap_pagesoperations'         => LIB_PATH . 'sitemap/additional/pages.php',
					'sitemapmenu'                     => LIB_PATH . 'sitemap/additional/menu.php',
					'sitemapxml'                      => LIB_PATH . 'sitemap/additional/sitemap.xml.php',
					'sitemapcmsforms'                 => LIB_PATH . 'sitemap/additional/cmsForms.php',
					'sitemap_cms'                     => LIB_PATH . 'sitemap/additional/cms.php',
					'sitemapmisc'                     => LIB_PATH . 'sitemap/additional/misc.php',
					'sitemap_restoreurl'              => LIB_PATH . 'sitemap/additional/restore-url.php',
					'sitemaptemplatehelper'           => LIB_PATH . 'sitemap/additional/template.php',
					'sitemapsorter'                   => LIB_PATH . 'sitemap/additional/sort.php',
					'sitemapaliasesadmin'             => LIB_PATH . 'sitemap/controller/aliases.php',
					'sitemap_gofirstchild_controller' => LIB_PATH . 'sitemap/controller/go-child.php',
					'Sitemap_Controller_Add'          => LIB_PATH . 'sitemap/controller/add.php',
					'sitemap_pagescriptconfig_page'   => LIB_PATH . 'sitemap/controller/page_script_config.php',
					'sitemap_controller_data_list'    => LIB_PATH . 'sitemap/controller/data-list.php',
					'sitemap_controller_edit'         => LIB_PATH . 'sitemap/controller/edit.php',
					'sitemap_movecontroller'          => LIB_PATH . 'sitemap/controller/move.php',
					'sitemap_controller_order'        => LIB_PATH . 'sitemap/controller/order.php',
					'sitemap_controller_search'       => LIB_PATH . 'sitemap/controller/search.php',
					'sitemapstandartaddpage'          => LIB_PATH . 'sitemap/controller/standart-add.php',
					'managedocumentadminpage'         => LIB_PATH . 'sitemap/controller/manage_document.php',
					'SitemapCreateSectionPage'        => LIB_PATH . 'sitemap/controller/create_section.php',
					// Sitemap type
					'sitemappageidcolumn'             => LIB_PATH . 'sitemap/type/sitemapPageId.php',
					// Sitemap controls
					'csitemapselectonce'              => LIB_PATH . 'sitemap/control/select.once.php',
					'csitemapselectcontrol'           => LIB_PATH . 'sitemap/control/select.php',
					// Custom Config
					'cconfig'                         => LIB_PATH . 'custom_config/cconfig.php',
					'cconfigbasecontrol'              => LIB_PATH . 'custom_config/basecontrol.php',
					'cconfigcontrolmanager'           => LIB_PATH . 'custom_config/controls.php',
					'cconfigschema'                   => LIB_PATH . 'custom_config/schema.php',
					'cconfigexception'                => LIB_PATH . 'custom_config/exception.php',
					// Custom Config controllers
					'cconfigadminmanagepage'          => LIB_PATH . 'custom_config/admin/manage.php',
					'cconfigadminindexpage'           => LIB_PATH . 'custom_config/admin/index.php',
					'cconfigadmineditpage'            => LIB_PATH . 'custom_config/admin/edit.php',
					// Exceptions
					'notfoundexception'               => LIB_PATH . 'errors/NotFoundException.php',
					'forbiddenexception'              => LIB_PATH . 'errors/ForbiddenException.php',
					'internalerrorexception'          => LIB_PATH . 'errors/InternalErrorException.php',
					// SystemRegister
					'systemregisterprimitive'         => LIB_PATH . 'system_register/baseclasses.php',
					'systemregisterexception'         => LIB_PATH . 'system_register/baseclasses.php',
					'systemregisterhelper'            => LIB_PATH . 'system_register/helper.php',
					'systemregistersample'            => LIB_PATH . 'system_register/sample.php',
					'systemregister'                  => LIB_PATH . 'system_register/controller.php',
					'SystemRegisterAdministrate'      => LIB_PATH . 'system_register/admin/regedit.php',
					// Test Suite
					'extasytestadminpage'             => LIB_PATH . 'testSuite/admin.php',
					'extasytestmodel'                 => LIB_PATH . 'testSuite/testModel.php',
					'extasytestresultcolumn'          => LIB_PATH . 'testSuite/testResultColumn.php',
					'extasytestadminrouter'           => LIB_PATH . 'testSuite/admin/router.php',
					'extasytestadminlauncher'         => LIB_PATH . 'testSuite/admin/launcher.php',
					'extasytestquickaddadminpage'     => LIB_PATH . 'testSuite/admin/quick_add.php',
					'usersmodule'                     => LIB_PATH . 'Users/init.php',
					'useraccount'                     => LIB_PATH . 'Users/account.php',
					'usersdbmanager'                  => LIB_PATH . 'Users/dbmanager.php',
					'usersmisc'                       => LIB_PATH . 'Users/misc.php',
					'userslogin'                      => LIB_PATH . 'Users/login/login.php',
					'usersforgot'                     => LIB_PATH . 'Users/forgot/forgot.php',
					'usersregistration'               => LIB_PATH . 'Users/registration/registration.php',
					'cuserselect'                     => LIB_PATH . 'Users/control/userselect.php',
					'users_admin_index'               => LIB_PATH . 'Users/admin/index.php',
					'users_admin_search'              => LIB_PATH . 'Users/admin/search.php',
					'usersforgot_send'                => LIB_PATH . 'Users/forgot/controller/send.php',
					'usersregistration_registrate'    => LIB_PATH . 'Users/registration/controller/registration.php',
					//

				)
			);
            spl_autoload_register( array( '\\Extasy\\ClassLocator', 'psr4autoload' ) );
		}

	}
}
