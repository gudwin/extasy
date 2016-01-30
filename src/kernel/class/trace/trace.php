<?
use \Faid\DB,\Faid\UParser;
/** 
 * Класс для отладки системы
 * Возможности:
 * - подсчитывает время работы системы 
 * - осуществляет сохранение сообщений системы
 * - подсчитывает время от старта до указанной точки
 * - выводит информацию о сессии
 * - выводит _GET, _POST, _REQUEST, _SESSION
 * - информация о сервере (OS, текущий путь к скрипту, как работает пхп, какой версии)
 * - подает статистику на браузер
 */ 
class Trace
{
	protected static $bDisabled = true; // Это св-во отвечает за активность работы системы
	protected static $aAlert = array(); // Массив сообщение
	protected static $aDebug = array(); // Массив отладки
	protected static $fStartTime = 0; // Время старта работы приложения
	protected static $szOutputPath = ''; // Путь для записи результата на файловой системе
	/**
	 * Detects if trace output enabled
	 * @return boolean
	 */
	public static function enabled( ) {
		return ! self::$bDisabled;
	}
	/**
	 * Деактивирует вывод результатов работы класса
	 */
	public static function setDisabled($bDisable = true)
	{
		self::$bDisabled = $bDisable;
	}
	/** 
	 * Регистрирует новое сообщение в списке вывода, сообщение добавляется в необходимую категорию. Если категории не существует то создается новая. Если количество аргументов равно 1, то оно воспринимается как простое сообщение без категории
	 * @param $szCategory string имя категории
	 * @param $szMessage string сообщение 
	 */
	public static function addMessage($szCategory,$szMessage = 0,$bError = false)
	{
		$aArg = func_get_args();
		
		if (sizeof($aArg) > 1)
		{
			$aAdd = array(
				'message' => $szMessage,
				'time' => self::timeFromStart(),
				'error' => $bError

				);
			// Тогда мы должны добавить в сообщение в конкретную категорию
			// Перебор существующих категорий
			$bFound = false;
			foreach (self::$aDebug as $key=>$row)
			{
				// Если текущая категория уже создана, то 
				if ($key == $szCategory)
				{
					// Устанавливаем флаг и добавляем сообщение
					$bFound = true;
					self::$aDebug[$szCategory][] = $aAdd;
				}
			}
			// Если флаг не установлен, то мы должны добавить новую группу сообщений 
			if (!$bFound)
			{
				self::$aDebug[$szCategory] = array($aAdd);
			}

		}
		else
		{
			// Добавляем в список алертов
			self::$aAlert[] = $szCategory;
		}
	}
	public static function dbCallBack( $sql ) {
		self::addMessage('Database requests', $sql );
	}
	/** 
	 * Стартует работу скрипта
	 */ 
	public static function start()
	{
		// Старт счетчика
		self::startCounter();
		// Параметры запроса _GET,_POST,_REQUEST,_COOKIE,_SESSION
		self::addGlobalData();
		// Операционная система
		self::addOS();
		// Версия пхп
		self::addPHPVersion();
		// Текущий пользователь
		self::addCurrentUser();
		// Подслушивать события от DB
		self::addDBHelper();
	}
	/** 
	 * Завершает работу скрипта
	 */ 
	public static function finish()
	{
		 
		// Если класс деактивирован, то ничего не делаем
		if (self::$bDisabled) return;
		// Сколько времени прошло до конца работы системы
		self::finishCounter();
		// Добавляем массив сесси
		self::addSession();
		// Текущий уровень памяти
		self::addMemoryUse();
		// Подключаем включенные файлы
		self::addIncludedFiles();
		// Генерируем полный список по всем категориям
		self::generateTimeList();
		// Генерируем шаблон результатов
		$szResult = self::generate();

		if (!empty(self::$szOutputPath))
		{
			// Вывод в общий html (перед закрытием тега body
			$szContent = ob_get_contents();

			file_put_contents(self::$szOutputPath,'<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>'.$szContent.$szResult.'</body></html>');
			$szResult = '';
		} else {
			$szResult = '<div id="__debug" style="display:none;position:absolute;left:0;top:0;background-color:#CCCCCC;z-index:999;width:100%">'
				.$szResult
				.'</div>';
		}
        return $szResult;

	}
	public static function writeToFile($szPath)
	{
		self::$szOutputPath = $szPath;
	}
	/**
	 * Добавляет информацию о массиве сессии
	 */
	public static function addSession()
	{
		$szArray = print_r($_SESSION,true);
		$szArray = str_replace("\n",'<br/>',$szArray);
		self::addMessage('Глобальные массивы','_SESSION => '.$szArray);

	}
	/**
	 * Возвращает время прошедшее со старта счетчика
	 */ 
	protected static function timeFromStart()
	{
		 return microtime(true) - self::$fStartTime;
	}
	/**
	 * Стартуем работу счетчика
	 */
	protected static function startCounter()
	{
		self::$fStartTime = microtime(true);
	}
	/**
	 * Завершаем работу счетчика
	 */
	protected static function finishCounter()
	{
		self::addMessage('Время работы приложения: '.self::timeFromStart());
	}
	/**
	 * Links trace class to DB 
	 */
	protected static function addDBHelper( ) {
		$callback = array( 'Trace', 'dbCallback' );
		DB::addEventListener('DB::before_get', $callback);
		DB::addEventListener('DB::before_post', $callback);
		DB::addEventListener('DB::before_query', $callback);
	}
	/**
	 * Добавляет данные из глобальных массивов
	 */
	protected static function addGlobalData()
	{
		$aData = array(
			'_POST' => $_POST,
			'_GET' => $_GET,
			'_FILES' => $_FILES,
			'_COOKIE' => $_COOKIE,
			'_REQUEST' => $_REQUEST,
			'_SERVER' => $_SERVER,
			);
		foreach ($aData as $key=>$row)
		{
			$szArray = print_r($row,true);
			$szArray = str_replace("\n",'<br/>',$szArray);
			self::addMessage('Глобальные массивы',$key.' => '.$szArray);
		}
	}
	/** 
	 * Добавляет информацию об оперативной системе
	 */ 
	protected static function addOS()
	{
		 self::addMessage('ОС: '.php_uname());
	}
	/**
	 * Добавляет в вывод текущую версию PHP
	 */
	protected static function addPHPVersion()
	{
		 self::addMessage('PHP: '.phpversion());
	}
	/**
	 * Добавляет в вывод информацию о текущем пользователе
	 */
	protected static function addCurrentUser()
	{
		if (is_callable('get_current_user')) {
			if (strpos(ini_get('disable_functions'),'get_current_user') === false) {
				self::addMessage('Текущий пользователь: '.get_current_user());
			}
		} 
		self::addMessage('Текущий пользователь: Нет доступа');
	}
	/**
	 * Добавляет информацию об текущем объеме памяти
	 */
	protected static function addMemoryUse()
	{
		self::addMessage('Максимальное использование памяти: '.memory_get_peak_usage());
	}
	/**
	 * Добавляет информацию об включенных файлах
	 */
	protected static function addIncludedFiles()
	{
		$szContent = print_r(get_included_files(),true);
		$szContent = str_replace("\n", '<br/>',$szContent);
		self::addMessage('Включенные файлы: '.$szContent);
	}
	protected static function generateTimeList() {
		$result = array();
		foreach (self::$aDebug as $row) {
			$result = array_merge($result,$row);
		}
		// Теперь сортируем
		usort($result,create_function('$a,$b','return $a["time"] > $b["time"];'));
		self::$aDebug['All'] = array();
		foreach ($result as $row) {
			self::$aDebug['All'][] = $row; 
		} 
	}
	/**
	 * Генерирует результат
	 */ 
	protected static function generate()
	{
		 $aParse = array(
			 'aMessage' => self::$aDebug,
			 'aAlert' => self::$aAlert,
			 );
		 return UParser::parsePHPFile(dirname(__FILE__).'/template.tpl',$aParse);
	}
	
}
?>