<?
//************************************************************//
//                                                            //
//                Класс строк                                 //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: gis2002@inbox.ru                              //
//                                                            //
//  Разработчик: Gisma (05.12.2006)                           //
//  Модифицирован:  05.12.2006  by Gisma                      //
//                                                            //
//************************************************************//
if (!defined('OUTPUT_LANGUAGE')) {
	define('OUTPUT_LANGUAGE','ENGLISH');
}
define('CMS_STRINGS_PATH',LIB_PATH.'kernel/cms/strings/');
class CMS_Strings {
	private static $instance;
	/**
	*   @var хранит имя текущего языка
	*/
	var $szLanguage;
	/**
	*   @var Хранит сообщения для текущего языка
	*/
	var $aMessages = array();

	public function __construct() {
		// грузим дефолтовый язык
		$this->setActiveLang(OUTPUT_LANGUAGE);
	}
	public function __get($name) {
		if ($name == 'language') {
			return $this->szLanguage;
		}
	}
	public function __set($name,$value) {
		if ($name == 'language') {
			$this->szLanguage = $value;
		}
	}
	/**
	*   @desc Устанавливает текущий язык
	*   @return
	*/
	public function setActiveLang($language) {
		$szPath = CMS_STRINGS_PATH.'langs/'.$language.'.php';
		// проверяем существует ли путь
		if (!is_readable($szPath)) {
			trigger_error('СMS_Strings:setActiveLang нечитаемый путь `'.htmlspecialchars($szPath).'`',E_USER_ERROR);
		}
		// устанавливаем дефолтовый язык
		require_once $szPath;
		if (!empty($aMessages)) {
			$this->aMessages[$language] = $aMessages;
		}
		$this->szLanguage = $language;
	}
	public function addToLang($language,$aMessages) {
		if (empty($this->aMessages[$language])) $this->aMessages[$language] = array();
		$this->aMessages[$language] = array_merge($this->aMessages[$language],$aMessages);
	}
	/**
	*   @desc Возвращает сообщение
	*   @return
	*/
	public function getMessage($message,$language = '') {
		if (empty($language)) {
			$language = $this->szLanguage;
		}
		if (isset($this->aMessages[$language][$message])) {
			return $this->aMessages[$language][$message];
		} else {
			return $message;
		}
	}
	/**
	*   @desc
	*   @return
	*/
	public function setMessage($message,$value,$language = '') {
		if (!empty($language)) {
			$this->aMessages[$language][$message] = $value;
		} else {
			$this->aMessages[$this->szLanguage][$message] = $value;
		}
	}
	public static function getInstance() {
		if (!is_object(self::$instance)) {
			self::$instance = new CMS_Strings();
		}
		return self::$instance;
	}
}
function _msg($string) {
	static $strings = NULL;
	if (empty($strings)) {
		$strings = CMS_Strings::getInstance();
	}
	return $strings->getMessage($string);
}
?>