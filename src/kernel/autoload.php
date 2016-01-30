<?php
use \Faid\debug;

/**
 *
 */
function __autoload( $class ) {
	// позволяет сохранить доп массив классов
	static $additionalClasses = array();
	static $additionalFunc = array();
	if ( empty( $additionalClasses ) ) {
		$additionalClasses = array(
			'arrayhelper'         => LIB_PATH . 'kernel/functions/array.func.php',
			'date_helper'         => LIB_PATH . 'kernel/functions/date.func.php',
			'httphelper'          => LIB_PATH . 'kernel/functions/http.php',
			'imagehelper'         => LIB_PATH . 'kernel/functions/image.php',
			'mimehelper'          => LIB_PATH . 'kernel/functions/mime.func.php',
			'integerhelper'       => LIB_PATH . 'kernel/functions/integer.func.php',
			'datehelper'          => LIB_PATH . 'kernel/functions/date.func.php',
			'fieldshelper'        => LIB_PATH . 'kernel/functions/fieldsHelper.php',
			// Class
			'loader'              => CLASS_PATH . 'loader/loader.class.php',
			'exportjs'            => CLASS_PATH . 'exportjs/exportjs.class.php',
			// baseclassess
			'extasypage'          => LIB_PATH . 'kernel/baseclasses/page.php',
			'ccontrol'            => LIB_PATH . 'kernel/baseclasses/control.php',
			// Controls
			'cinput'              => CONTROL_PATH . 'input.php',
			'cselect'             => CONTROL_PATH . 'select.php',
			'ccheckbox'           => CONTROL_PATH . 'checkbox.php',
			'cfile'               => CONTROL_PATH . 'file.php',
			'cdate'               => CONTROL_PATH . 'date.php',
			'ccalendar'           => CONTROL_PATH . 'calendar.php',
			'cimage'              => CONTROL_PATH . 'image.php',
			'clinkstomanycontrol' => CONTROL_PATH . 'links_to_many.php',
			'ckeyvaluelist'       => CONTROL_PATH . 'key_value_list.php',
			'chtmlarea'           => CONTROL_PATH . 'htmlarea.php',
			'cphpsource'          => CONTROL_PATH . 'php_source.php',
			// Прочие классы
			'cdumper'             => CLASS_PATH . 'dumper/dumper.class.php',
			'xmlparser'           => CLASS_PATH . 'xmlparser.class.php',
			// CMS classes
			'adminpage'           => LIB_PATH . 'kernel/cms/adminPage.php',
			'adminconfig'         => LIB_PATH . 'kernel/cms/_pages/config.php',
			'cmsauth'             => LIB_PATH . 'kernel/cms/auth/auth.class.php',
			'cmsusers'            => LIB_PATH . 'kernel/cms/auth/users.php',
			'cmslog'              => LIB_PATH . 'kernel/cms/log/log.class.php',
			'cms_log'             => LIB_PATH . 'kernel/cms/log/log.class.php',
			'cms_menu'            => LIB_PATH . 'kernel/cms/menu/menu.class.php',
			'cms_strings'         => LIB_PATH . 'kernel/cms/strings/strings.class.php',
			// CMS Controllers
			'cms_page_datalist'   => LIB_PATH . 'kernel/cms/_pages/data_list.php',
			'cms_datamanage'      => LIB_PATH . 'kernel/cms/_pages/data_manage.php',
			'cms_datapage'        => LIB_PATH . 'kernel/cms/_pages/data_page.php',
			'phpconsole'          => LIB_PATH . 'kernel/cms/_pages/phpconsole.php',
			'adminsqlconsole'     => LIB_PATH . 'kernel/cms/_pages/sql_console.php',
			'adminorderpage'      => LIB_PATH . 'kernel/cms/_pages/order.php',
			// CMS Design
			'cmsdesign'           => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cms_design'          => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesignlayout'     => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesignlinks'      => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesigndecor'      => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesigntabs'       => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesigntext'       => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesigntable'      => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesignforms'      => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesignmessages'   => LIB_PATH . 'kernel/cms/design/design.class.php',
			'cmsdesignpopup'      => LIB_PATH . 'kernel/cms/design/design.class.php',
			// DAO
			'dao'                 => LIB_PATH . 'kernel/dao/dao.php',
			'dao_service'         => LIB_PATH . 'kernel/dao/dao.php',
			'dao_exception'       => LIB_PATH . 'kernel/dao/dao.php',
			'dao_filesystem'      => LIB_PATH . 'kernel/dao/filesystem/filesystem.class.php',
			'dao_image'           => LIB_PATH . 'kernel/dao/image/image.class.php',
			//
			'trace'               => CLASS_PATH . 'trace/trace.php',
		);
	}
	if ( is_callable( $class ) ) {
		$additionalFunc[ ] = $class;
		return;

	} elseif ( is_array( $class ) ) {
		$lowered = array();
		foreach ( $class as $key => $row ) {
			$lowered[ strtolower( $key ) ] = $row;
		}
		$additionalClasses = array_merge( $additionalClasses, $lowered );

		return;
	}
	$loweredClass = strtolower( $class );
	if ( isset( $additionalClasses[ $loweredClass ] ) ) {
		$path = $additionalClasses[ $loweredClass ];
		require_once $path; // Это чтоб никакая падла не смогла переопределить мою библиотеку классов ;)
		return;
	}
	foreach ( $additionalFunc as $func ) {

		call_user_func( $func, $loweredClass, $class );
	}
}

spl_autoload_register( '__autoload' );