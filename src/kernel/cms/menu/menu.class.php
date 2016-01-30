<?
//************************************************************//
//                                                            //
//        Управлению меню в админке                           //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email:   gisma@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma (16.07.2007)                           //
//  Модифицирован:  21.10.2008  by Gisma                      //
//                                                            //
//************************************************************//
//  21.10.2008 - удалено сохранение хеша меню на локальный    //
//  винчестер в папку _cms                                    //
//************************************************************//

// Класс меню, предназначен для подключения, меню, автоматически сформированному по инсталляии модулей
// Класс предназначен, для програмного управления меню и добавления записей, в меню

class CMS_Menu {
	static protected $instance = NULL;

	 protected $items = array();

	public function __construct() {
		self::$aMenu = $this->sortMenu($aResult);
	}

	/**
	 * Добавляет в меню
	 */
	public function add(MenuItem $menuItem ) {
		$this->items[] = $menuItem;
	}

	/**
	 *   Возвращает текущее меню
	 * @return array
	 */
	public function get() {

		return self::$aMenu;
	}

	protected static function sortMenu($aData) {
		ksort($aData);
		$aResult = array();
		foreach ($aData as $key => $row) {
			if ( preg_match('/^[0-9]+/', $key) ) {
				$key = preg_replace('/^[0-9]+/', '', $key);

			}
			if ( is_array($row) ) {
				$row = self::sortMenu($row);
			}
			$aResult[ $key ] = $row;
		}

		return $aResult;
	}

	public static function getInstance() {
		if ( !is_object(self::$instance) ) {
			self::$instance = new CMS_Menu();
		}

		return self::$instance;
	}
}

?>