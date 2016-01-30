<?php
/**
 *
 * Pattern observable
 * @date 18.02.2012
 * @author Gisma
 *
 */
namespace Faid {
	class StaticObservable {
		const eventCallEvent = 'faid::callEvent';
		const eventAddEventListener = 'faid::addEventListener';
		/**
		 *
		 * Enter description here ...
		 * @var unknown_type
		 */
		protected static $eventMap = array();

		///////////////////////////////////////////////////////////////////////////
		// Public static methods
		/**
		 *
		 * Enter description here ...
		 * @param string $event
		 * @param callable $callback
		 */
		public static function addEventListener( $event, $callback) {
			$event = strtolower( $event );
			//
			if (!isset( self::$eventMap[$event] )) {
				self::$eventMap[$event] = array();
			}
			self::$eventMap[$event][] = $callback;
			self::callEvent(self::eventAddEventListener);
		}
		///////////////////////////////////////////////////////////////////////////
		// Protected static methods
		/**
		 *
		 * Enter description here ...
		 * @param string $event
		 */
		protected static function callEvent( $event ) {
			$event = strtolower( $event );
			$arguments = func_get_args();
			array_shift($arguments);

			if (isset(self::$eventMap[$event])) {
				foreach ( self::$eventMap[$event] as $callback ) {
					call_user_func_array($callback, $arguments);
				}
				if (self::eventCallEvent != $event ) {
					self::callEvent(self::eventCallEvent,$event,$arguments);
				}
			}
		}
		/**
		 *
		 * Enter description here ...
		 * @param string $event
		 */
		protected static function callFilter( $event, $data ) {
			$event = strtolower( $event );
			//
			if (isset(self::$eventMap[$event])) {
				foreach ( self::$eventMap[$event] as $callback ) {
					$data = call_user_func($callback, $data );
				}
			}
			return $data;
		}
	}
}
?>
<?php
/**
 * Provides functions for storing and reading configuration data
 */
namespace Faid\Configure {
	use Faid\StaticObservable as StaticObservable;

	/**
	 * Class Configure
	 * If you want to listen when Configure options are changed you have to attach event listener to event
	 * <b>"Configure.write"</b>.
	 * <code>
	 * use \Faid\Configure
	 * Configure.addEventListener( 'Configure.write', function ( $key, $data ) {
	 *      print ('Property "%s% changed to new value:&lt;br&gt;%s', $key, print_r( $data, true) );
	 * });
	 * </code>
	 */
	class Configure extends StaticObservable {
		/**
		 * Map of all data
		 * @var array
		 */
		public static $data = array();

		/**
		 * Return data by given $key
		 *
		 * @param $key
		 *
		 * @return array
		 * @throws ConfigureException
		 */
		public static function read( $key ) {
			// split keys
			$keys = self::explode( $key );
			// read value
			$data = self::$data;

			foreach ( $keys as $part ) {
				if ( isset( $data[ $part ] ) ) {
					$data = $data[ $part ];
				} else {

					throw new ConfigureException( $key );
				}
			}
			return $data;
		}

		/**
		 * Writes key to database
		 *
		 * @param $key
		 * @param $newData array
		 *
		 * @throws ConfigureException
		 */
		public static function write( $key, $newData ) {
			// split keys
			$keys = self::explode( $key );
			// write values
			$data = & self::$data;
			foreach ( $keys as $part ) {
				if ( !isset( $data[ $part ] ) ) {
					$data[ $part ] = array();
				}
				$data = & $data[ $part ];
			}
			$data = $newData;
			self::callEvent( 'Configure.write', $key, $newData );
		}

		/**
		 * Explodes given string to array that contains path to data
		 *
		 * @param $key
		 *
		 * @return array
		 */
		private static function explode( $key ) {
			return explode( '.', $key );
		}
	}
}
?>
<?php
/**
 * Contains Configure exception description
 */
namespace Faid\Configure {
	/**
	 * Class ConfigureException
	 */
	class ConfigureException extends \Exception {
		/**
		 * Constructs event
		 * @param string $key
		 */
		public function __construct( $key ) {
			$msg = 'Failed to find key `%s` in Configure data';
			$msg = sprintf( $msg, $key );
			parent::__construct( $msg );
		}
	}
}
?>
<?php

namespace Faid\Debug {
	use \Faid\Configure\Configure;
	/**
	 * That class used for handling exceptions and error in run time
	 */
	class Debug {
		///////////////////////////////////////////////////////////////////////////
		// Public static method
		public static function out() {
			displayCallerCode(1);
			$aData = func_get_args();
			var_dump_list($aData);
			die();
		}

		/**
		 * Sets default config values
		 */
		public static function setDefaults() {
			// Write default config
			Configure::write('Debug', false);
			Configure::write('Error.Handler', array('\Faid\Debug\Debug', 'errorHandler'));
			Configure::write('Error.Level', E_ALL | E_WARNING | E_STRICT);
			Configure::write('Exception.Handler', array('\Faid\Debug\Debug', 'exceptionHandler'));
			Configure::write('Exception.Renderer', '\Faid\Debug\ExceptionRenderer');
			Configure::write('Error.Renderer', '\Faid\Debug\ErrorRenderer');
			Configure::write('FatalError.Handler',null);
			// Link exception and error event handlers
			self::linkErrorHandlers();
			// register handler for fatal errors
			self::registerShutdown();
			// setup event listener for changing events
			self::setEventListeners();
		}

		/**
		 * Default exception handler, calls default exception renderer
		 *
		 * @param $exception
		 */
		public static function exceptionHandler($exception) {
			$className = Configure::read('Exception.Renderer');
			call_user_func(array($className, 'render'), $exception);
			die();
		}

		/**
		 * @desc
		 * @return
		 */
		public static function errorHandler($errno, $errstr, $errfile = '', $errline = '') {
			$className = Configure::read('Error.Renderer');
			call_user_func(array($className, 'render'), $errno, $errstr, $errfile, $errline);
		}

		/**
		 *
		 */
		public static function enable() {
			self::linkErrorHandlers();
		}

		/**
		 * Disable error handler
		 */
		public static function disable() {
			set_error_handler(
				function () {
				}, E_ALL | E_WARNING | E_STRICT
			);
			set_exception_handler(
				function () {
				}
			);
		}

		public static function getFileSource($file, $current) {
			$fileSource  = file($file);
			$from        = max(0, $current - 5);
			$to          = min($from + 10, sizeof($fileSource));
			$isPlainText = (php_sapi_name() == "cli") ;
			$result      = '';
			if ( $isPlainText ) {
				$result .= sprintf("Error source: %s \r\n", $file);
				$template = "%10s[%6d]%s\r\n";
			} else {
				$result .= sprintf('<div class="error" style="margin-bottom:20px"><h2>Error source:</h2> <span style="font-style:italic;">"%s"</span>', $file);
				$template = '<div class="error_line" style="%s;height:18px;min-width:600px;clear:left;" ><span style="float:left;margin-right:20px;line-height:18px;">[%6d]</span><span style="line-height:18px;">%s</span></div>' . "\r\n";
			}
			for ($i = $from; $i < $to; $i++) {
				$currentStyle = '';
				if ( !$isPlainText ) {
					$currentStyle     = 'border-bottom:1px solid gray;';
					$fileSource[ $i ] = str_replace(' ', '&nbsp;', htmlspecialchars($fileSource[ $i ]));
					$fileSource[ $i ] = str_replace("\t", '<span style="width:20px;display:inline-block;"><!-- --></span>', $fileSource[ $i ]);
					if ( $i == $current - 1 ) {
						$currentStyle .= 'background-color:#eee;';
					}
				}
				if ( $i == $current - 1 ) {
					if ( !$isPlainText ) {
						$currentStyle .= 'background-color:#eee;';
					} else {
						$currentStyle = ' ACTIVE ';
					}
				}
				$result .= sprintf($template, $currentStyle, $i + 1, $fileSource[ $i ]);
			}
			if ( $isPlainText ) {

			} else {
				$result .= '</div>';
			}

			return $result;
		}

		public static function registerShutdown() {
			register_shutdown_function(array('\Faid\Debug\Debug', 'fatalErrorShutDown'));
		}

		/**
		 * That function not customizeable, because fatal errors usually happens because of business logic
		 */
		public static function fatalErrorShutDown() {
			$a = error_get_last();
			if ( !is_null($a) ) {
				$isFatalError = $a['type'] === E_ERROR || $a['type'] === E_USER_ERROR || $a['type'] == E_COMPILE_ERROR;
				if ( $isFatalError ) {

					$callback = Configure::read('FatalError.Handler');

					if ( is_callable( $callback )) {
						call_user_func( $callback, $a['message'], $a['file'],$a['line']);
					} else {
						if ( !empty($a[ 'file' ]) ) {
							print self::getFileSource($a[ 'file' ], $a[ 'line' ]);
						}
						printf('<h2 style="color:#CC0000">Fatal Error: %s</h2>', $a[ 'message' ]);
					}
				}
			}
		}

		/**
		 * Setups handlers for errors and exceptions
		 */
		protected static function linkErrorHandlers() {
			$errorHandler     = Configure::read('Error.Handler');
			$errorLevel       = Configure::read('Error.Level');
			$exceptionHandler = Configure::read('Exception.Handler');
			// enable error handler
			set_error_handler($errorHandler, $errorLevel);
			// enable exception handler
			set_exception_handler($exceptionHandler);
		}
		protected static function setEventListeners() {
			Configure::addEventListener('Configure.write', array('\Faid\Debug\Debug', 'onConfigureWrite'));
		}

		public static function onConfigureWrite($key, $data) {
			$isDebugKey = (strpos('Error', $key) === 0) || (strpos('Exception', $key) === 0);
			if ( $isDebugKey ) {
				// re-init error handlers
				self::linkErrorHandlers();
			} else {
			}
		}

	}


	function array_strip_slashes(&$aValue) {
		if ( is_array($aValue) ) {
			$aNew = array();
			foreach ($aValue as $key => $value) {
				if ( is_array($aValue[ $key ]) )
					array_strip_slashes($aValue[ $key ]);
				else
					$aValue[ $key ] = stripslashes($value);
				$aNew[ stripslashes($key) ] = $aValue[ $key ];
			}
			$aValue = $aNew;
		} else {
			$aValue = stripslashes($aValue);
		}
	}

	function var_dump2($var = NULL) {
		displayCallerCode(1);
		$aData = func_get_args();
		var_dump_list($aData);

	}

	function var_dump_list($aData) {
		foreach ($aData as $var) {
			var_dump($var);
		}
	}

	function _debugWithOutput() {
		displayCallerCode(1);
		var_dump2( func_get_args());
		die();
	}

	/**
	 * Выводит массив данных в виде таблицы. Функция применяется к двумерным массивам
	 * @param array $table     двухмерный массив
	 * @param int $columnWidth ширина колонки
	 */
	function outputDisplayTable($table, $columnWidth = 10) {
		if ( empty($table) ) {
			printf(' Empty Table ');
		}
		$columnHeader = array_keys($table[ 0 ]);
		// Для этого считаем, сколько у нас колонок
		$columnCount = count(array_keys($columnHeader));
		// Рассчитываем ширину таблицы
		$totalWidth = $columnCount * $columnWidth + 1;

		// Выводим разделительную линию
		$line = "\r\n" . '|' . str_repeat('-', $totalWidth - 2) . '|' . "\r\n";
		$tpl  = '%' . ($columnWidth - 2) . '.' . ($columnWidth - 2) . 's |';
		print $line;
		// Выводим заголовки таблицы
		print('|');
		foreach ($columnHeader as $row) {
			printf($tpl, $row);
		}
		// Выводим разделительную линию
		print $line;
		// Выводим ряды
		foreach ($table as $key => $row) {
			// Начало ряда
			print '|';

			// Каждая колонка
			foreach ($row as $value) {
				printf($tpl, $value);
			}
			print $line;
		}

	}




	function defaultDebugBackTrace($output = true, $trace = NULL) {
		// А это значит, что режим отладки врублен!
		// выводим бегтрейс ошибки
		if ( empty($trace) ) {
			$trace = debug_backtrace();
		} else {

		}
		$tplError = ' [%d] %s:%s %s%s%s ';
		$result   = '';
		foreach ($trace as $key => $row) {
			//
			$result .= sprintf(
				$tplError,
				$key,
				isset($row[ 'file' ]) ? $row[ 'file' ] : '',
				isset($row[ 'line' ]) ? $row[ 'line' ] : '',
				isset($row[ 'class' ]) ? $row[ 'class' ] : '',
				isset($row[ 'type' ]) ? $row[ 'type' ] : '',
				isset($row[ 'function' ]) ? $row[ 'function' ] : ''
			);
			// Если выводим ошибку в CLI-моде

			if ( "cli" == php_sapi_name()) {
				// То добавляем перевод строки
				$result .= "\r\n";
			} else {
				// Иначе html-тег + перевод строки
				$result .= sprintf("<br/>\r\n");
			}
		}
		if ( $output ) {
			print $result;
		}

		return $result;
	}

	/**
	 *
	 * Выводит кусок исходных кодов из файла вызвавшего функцию
	 * @param int $fromErrorHandler устанавливайте значение данной переменной, только если она вызывается из промежуточной функции (например перехватчика ошибок)
	 */
	function displayCallerCode($fromErrorHandler = 0, $output = true) {
		// Отображаем блок текста
		$trace = debug_backtrace();
		$caller = $trace[0 + $fromErrorHandler];

		$result = '';
		if (!isset($caller['file'])) {
			if ( isset( $trace[0 + $fromErrorHandler + 1] )) {
				$caller = $trace[0 + $fromErrorHandler + 1];
			}
		}
		if (isset($caller['file'])) {
			$result = Debug::getFileSource( $caller['file'], $caller['line'] );
		} else {
			$result = '<hr/> <h2>Empty error source</h2> <hr/>';
		}
		if ( $output ) {
			print $result;
		}
		return $result;
	}

	function null_func() {
	}

	Debug::setDefaults();
}
?>
<?php
namespace Faid\Debug {
	use \Faid\Configure\Configure;
	class baseRenderer {
		protected static function isDebugEnabled( ) {
			$debug = Configure::read('Debug');
			return $debug;
		}
		protected static function cleanOutputIfNecessary( ) {
			if ( sizeof(ob_list_handlers()) > 0 ) {
				ob_clean();
			}
		}
	}
}
?>
<?php
namespace Faid\Debug {

	class ErrorRenderer extends baseRenderer {
		public static function render( $errno, $errstr, $errfile = '', $errline = '' ) {
			$skip = !self::isDebugEnabled() || self::ignoreError( $errno );

			if ( $skip ) {
				return;
			}
			self::cleanOutputIfNecessary();

			$message = self::buildErrorMessage( $errno, $errstr, $errfile, $errline );

			print $message;
			print Debug::getFileSource( $errfile, $errline );
		}

		protected static function ignoreError( $errno ) {
			// Проверяем, возможно сейчас установлен режим игнорирования этого типа ошибки
			$currentErrorLevel = ini_get( 'error_reporting' );
			$result            = !( $errno & $currentErrorLevel );
			return $result;
		}

		protected static function buildErrorMessage( $errno, $errstr, $errfile, $errline ) {
			if ( "cli" == php_sapi_name() ) {
				$template = ' Error type: %s;  %s [%s:%s]' . "\r\n";
			} else {
				$template = "<div style='color:red;font-size:18px'><strong> Error type: %s; Error information [%s:%s]</strong></div>";
			}
			$message = sprintf( $template, $errno, $errstr, $errfile, $errline );
			return $message;
		}
	}
}
?>
<?php
namespace Faid\Debug {

	class ExceptionRenderer extends baseRenderer {
		/**
		 * @param $exception
		 */
		public static function render( \Exception $exception ) {
			$skip = !self::isDebugEnabled();

			if ( $skip ) {
				return;
			}

			self::cleanOutputIfNecessary();

			print nl2br($exception);
			print Debug::getFileSource( $exception->getFile(), $exception->getLine() );
		}
	}
}
?>
<?php
namespace Faid {
	use \Faid\Configure\Configure;

	class DB extends StaticObservable {
		/**
		 *
		 * В данной версии хранится последний выполненный SQL-запрос
		 * @var unknown_type
		 */
		public static $lastSQL;
		/**
		 * Mysql connection handler
		 * @var \mysqli
		 */
		public static $connection;

		/**
		 * Check if connection with mysql server was established or not
		 */
		public static function checkConnection() {
			if ( !empty( self::$connection ) ) {
				// connection established, exit function
			} else {
				// no, we have to connect Mysql-server
				$data             = Configure::read( 'DB' );
				self::$connection = new \mysqli( $data[ 'host' ],
												 $data[ 'user' ],
												 $data[ 'password' ],
												 $data[ 'database' ],
												 isset( $data[ 'socket' ] ) ? $data[ 'socket' ] : null );
				if ( self::$connection->connect_error ) {
					$msg = sprintf( 'DB failed to connect mysql server. Mysql respone - %s',
									self::$connection->connect_error );
					throw new \Exception( $msg );
				}
				self::$connection->set_charset( 'utf8' );
			}
		}

		static public function get( $sql ) {
			self::checkConnection();
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_get', $sql );
			$result = self::$connection->query( $sql );
			if ( !$result ) {
				throw new \Exception( 'DB <span style="color:Darkred">' . self::$connection->error . '</span> Query:<br/><pre>' . $sql . '</pre>' );
			}
			return $result->fetch_assoc();
		}

		static public function post( $sql, $bIgnore = false ) {
			self::checkConnection();
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_post', $sql );
			$result = self::$connection->query( $sql );

			if ( !$result ) {
				if ( $bIgnore ) {
					return null;
				} else {
					throw new \Exception( 'DB <span style="color:Darkred">' . self::$connection->error . '</span> Query:<br/><pre>' . $sql . '</pre>' );

				}

			}
			return false;
		}

		static public function query( $sql, $bUserFetchAssoc = true ) {
			self::checkConnection();
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_query', $sql );
			$result = self::$connection->query( $sql );
			if ( !$result ) {
				throw new \Exception( 'DB <span style="color:Darkred">' . self::$connection->error . '</span> Query:<br/><pre>' . $sql . '</pre>' );
			}
			$method = $bUserFetchAssoc ? MYSQL_ASSOC : MYSQL_NUM;
			if ( is_callable( array( $result, 'fetch_all' ) ) ) {
				$result = $result->fetch_all( $method );
			} elseif ( is_object( $result ) ) {
				$rows       = array();
				$methodName = $bUserFetchAssoc ? 'fetch_assoc' : 'fetch_row';
				while ( $row = $result->$methodName() ) {
					$rows[ ] = $row;
				};
				$result = $rows;
			}

			return $result;

		}

		/**
		 * Возвращает только одно поле из результат запроса
		 *
		 * @param string $sql
		 * @param string $field
		 */
		static function getField( $sql, $field ) {
			self::$lastSQL = $sql;
			self::callEvent( 'DB::before_getField', $sql );
			$result = self::get( $sql );
			if ( !isset( $result[ $field ] ) ) {
				throw new \Exception ( 'Field `' . $field . '` not found' );
			}
			return $result[ $field ];
		}

		/**
		 *
		 * Возвращает последний вставленный индекс
		 */
		static function getInsertId() {
			return self::$connection->insert_id;
		}

		static public function getAutoIncrement( $table ) {
			self::checkConnection();
			$table = self::$connection->real_escape_string( $table );
			$rows  = self::query( "SHOW TABLE STATUS LIKE '$table'", self::$connection );
			return $rows[ 0 ][ 'Auto_increment' ];
		}

		/**
		 *
		 */
		static public function escape( $value ) {
			if ( is_array( $value ) ) {
				throw new \InvalidArgumentException( 'Can`t be an array' );
			}
			self::checkConnection();
			return self::$connection->real_escape_string( $value );
		}

		/**
		 *
		 */
		static public function setConnection( $connection ) {
			self::$connection = $connection;
		}

		static public function getConnection() {
			return self::$connection;
		}
	}

	/**
	 *
	 * Простой хелпер для примитивных запросов в бд (заебало ошибаться в мелочах ;)
	 * @author Gisma
	 *
	 */
}
?>
<?php
namespace Faid {
	/**
	 * Class DBSimple
	 * @package Faid
	 */
	class DBSimple {
		protected static $isSelect = false;

		/**
		 *
		 * Простой Select * запрос
		 *
		 * @param string $table имя таблицы
		 * @param mixed $where  возможные условия выборки, может быть как строкой, так и массив, если как массив, то в формате ключ => значение, ключи комбинируются условием and
		 * @param string $order возможные условия сортировки
		 */
		public static function select($table, $where = '', $order = '') {
			DB::checkConnection();
			$condition = self::getCondition($where);
			$sql       = 'select SQL_CALC_FOUND_ROWS * from %s where %s ';
			$sql       = sprintf($sql, $table, $condition);
			if ( !empty($order) ) {
				$sql .= sprintf('order by %s', $order);
			}

			return DB::query($sql);
		}

		/**
		 *
		 * Enter description here ...
		 *
		 * @param string $table    имя таблицы
		 * @param mixed $condition возможные условия выборки, может быть как строкой, так и массив, если как массив, то в формате ключ => значение, ключи комбинируются условием and
		 */
		public static function get($table, $condition, $order = '') {
			self::$isSelect = true;
			DB::checkConnection();
			$condition = self::getCondition($condition);
			$sql       = 'select * from %s where %s ';
			$sql       = sprintf($sql, DB::$connection->real_escape_string($table), $condition);
			if ( !empty($order) ) {
				$sql .= $order;
			}
			$sql .= ' LIMIT 0,1';

			return DB::get($sql);
		}

		public static function insert($table, $data) {
			$pieces = array();
			foreach ($data as $key => $row) {
				if ( is_int($key) ) {
					$pieces[ ] = $row;
				} else {
					$pieces[ ] = sprintf(
						'`%s` = "%s" ',
						DB::$connection->real_escape_string($key),
						DB::$connection->real_escape_string($row)
					);
				}
			}
			$sql = 'insert %s set %s ';

			$sql = sprintf($sql, DB::$connection->real_escape_string($table), implode(',', $pieces));
			DB::post($sql);

			return DB::$connection->insert_id;
		}

		public static function update($table, $setCondition, $whereCondition) {
			DB::checkConnection();
			$setCondition   = self::getCondition($setCondition, ' , ', true);
			$whereCondition = self::getCondition($whereCondition);
			$sql            = 'update %s set %s where %s';
			$sql            = sprintf($sql, DB::$connection->real_escape_string($table), $setCondition, $whereCondition);
			DB::post($sql);
		}

		public static function delete($table, $whereCondition) {
			DB::checkConnection();
			$whereCondition = self::getCondition($whereCondition);
			$sql            = 'delete from %s where %s';
			$sql            = sprintf($sql, DB::$connection->real_escape_string($table), $whereCondition);
			DB::post($sql);
		}

		/**
		 *
		 * Возвращает кол-во рядов попадающих под запрос
		 * @param string $table    имя таблицы
		 * @param array $condition условие запроса
		 */
		public static function getRowsCount($table, $condition = array()) {
			$condition = self::getCondition($condition);
			$sql       = 'select count(*) as `count` from %s where %s';
			$sql       = sprintf($sql, DB::$connection->real_escape_string($table), $condition);

			return intval( DB::getField($sql, 'count') );
		}

		private static function getCondition($condition, $glue = ' and ', $updateCondition = false) {
			if ( is_array($condition) && !empty($condition) ) {
				$sqlCond = array();
				foreach ($condition as $key => $row) {
					// Если ключ является числом, то это означает, что данная строка
					// идет без имени колонки и должна быть просто вставлена в запрос
					if ( is_int($key) ) {
						$sqlCond[ ] = !$updateCondition
							? '(' . $row . ')'
							: $row;
					} else {
						$sqlCond[ ] = sprintf(
							'`%s` = "%s"',
							DB::$connection->real_escape_string($key),
							DB::$connection->real_escape_string($row)
						);
					}
				}

				return implode($glue, $sqlCond);
			} elseif ( empty($condition) ) {
				// Возвращаем тогда всегда верное условие
				return '1';
			}

			return $condition;
		}
	}
}
?>
<?php
namespace Faid {
	use \Faid\Configure\Configure;

	class UParser extends StaticObservable {
		static public function parsePHPFile( $szTemplateFile, $viewVariables = null ) {

			$szOldObContents = ob_get_contents();
			if ( sizeof( ob_list_handlers() ) > 0 ) {
				ob_clean();
			} else {
				ob_start();
			}
			self::callEvent( 'UParser::before_parse', $szTemplateFile, $viewVariables );
			if ( file_exists( $szTemplateFile ) ) {
				if ( !empty( $viewVariables ) ) {
					extract( $viewVariables );
				}
				include $szTemplateFile;
				$content = ob_get_contents();
			} else {
				throw new Exception( 'Failed to parse PHP code' );
			}
			ob_clean();
			print $szOldObContents;
			self::callEvent( 'UParser::after_parse', $szTemplateFile, $viewVariables, $content );
			return $content;
		}

		/**
		 * @param $szContent
		 * @param $aVariables
		 *
		 * @return string
		 * @throws \Exception
		 */
		static public function parsePHPCode( $szContent, $viewVariables = null ) {

			self::callEvent( 'UParser::before_parse_code', $szContent, $viewVariables );
			try {
				$baseDir = Configure::read( 'UParser.tmp_dir' );
			}
			catch ( ConfigureException $e ) {
				throw new \Exception( 'Directive "UParser.tmp_dir" not defined ' );
			}
			$szOld = ob_get_contents();
			ob_clean();

			$__szPath = $baseDir . session_id();
			file_put_contents( $__szPath, $szContent );
			if ( !empty( $viewVariables ) ) {
				extract( $viewVariables );
			}

			include $__szPath;
			unlink( $__szPath );
			$szContent = ob_get_contents();
			ob_clean();
			print $szOld;

			self::callEvent( 'UParser::after_parse_code', $szContent, $viewVariables, $szContent );

			return $szContent;

		}
	}
}
?>
<?php
//************************************************************//
//                                                            //
//              Базовый класс документа                       //
//       Copyright (c) 2006-2011  Extasy Team                 //
//       Email:   dmitrey.schevchenko@gmail.com               //
//                                                            //
//  Разработчик: Gisma (26.06.2007)                           //
//                                                            //
//************************************************************//
namespace Faid {

	abstract class Model extends StaticObservable {

		const ModelName = '';

		/**
		 * @var string Имя колонки, в которой хранится основной (primary) индекс таблицы БД
		 */
		protected $index = 'id';

		/**
		 *
		 */
		protected $columns = array();

		/**
		 *
		 * Enter description here ...
		 *
		 * @param unknown_type $initialData
		 */
		public function __construct($initialData = array()) {
			$this->setData($initialData);
		}

		/**
		 * Возвращает одно из данных документа
		 */
		public function __get($szKey) {
			return $this->attr($szKey);
		}

		public function __isset($name) {
			return isset($this->columns[ $name ]);
		}

		/**
		 * Возвращает одно из данных документа
		 */
		public function __set($szKey, $szValue) {
			$this->setData(
				array(
					 $szKey => $szValue
				)
			);
		}

		/**
		 * Возвращает атрибут в виде объекта
		 */
		public function attr($columnName) {
			if ( !isset($this->columns[ $columnName ]) ) {
				// Иначе бросаем исключение
				$szText = ('In model "%s" attribute `%s` not found');
				$szText = sprintf($szText, static::ModelName, $columnName);
				throw new \Exception($szText);
			}

			return $this->columns[ $columnName ];

		}

		/**
		 * Returns current index value
		 * @return int
		 */
		public function getId() {
			return $this->columns[ $this->index ];
		}

		/**
		 * Returns index column name
		 * @return string
		 */
		public function getIndex() {
			return $this->index;
		}

		/**
		 * Данная функция вызывается перед каждым обновлением документа (перед insert и update) её задача - проверка
		 * документа на валидность
		 * Переопределяйте эту функцию в дочерних документах
		 */
		public function validate() {
			return true;
		}

		/**
		 *
		 * Enter description here ...
		 */
		public static function getModelName() {
			return static::ModelName;
		}

		/**
		 * @desc Возвращает внутренние данные документ
		 * @return
		 */
		public function getData() {
			return $this->columns;
		}

		/**
		 * @desc Устанавливает значение внутренних данных
		 * @return
		 */
		public function setData(array $newData) {
			foreach ($newData as $key => $row) {
				$this->columns[ $key ] = $row;
			}
		}

		///////////////////////////////////////////////////////////////////////////
		// Abstract methods
		/**
		 *
		 * Enter description here ...
		 *
		 * @param unknown_type $index
		 */
		public abstract function get($index);

		/**
		 *
		 * Enter description here ...
		 */
		public abstract function insert();

		/**
		 *
		 * Enter description here ...
		 */
		public abstract function update();

		/**
		 *
		 * Enter description here ...
		 */
		public abstract function delete();

	}
}
?>
<?php
namespace Faid {
	class Exception extends \Exception {
	}
}
?>
<?php
namespace Faid {
	abstract class Validator {

		public function isValid() {
			return $this->test();
		}
		abstract protected function test();
	}
}

?>
<?php


namespace Faid\Validators {


	class Exception extends \Exception {

	}
}
?>
<?php

namespace Faid\Validators {


	class FileInSecuredFolder {
		protected $baseFolder = null;
		protected $offset = null;

		public function __construct( $baseFolder ) {
			$validFolder = file_exists( $baseFolder ) && is_dir( $baseFolder );
			if ( !$validFolder ) {
				throw new Exception( sprintf( '$baseFolder not exists - "%s"', $baseFolder ) );
			}
			$this->baseFolder = $baseFolder;
		}

		protected function test() {
		}

		public function isValid( $path ) {
			$path = realpath( $path );
			if ( empty( $path ) ) {
				return false;
			}
			$basePart = substr( $path, 0, strlen( $this->baseFolder ) );

			$valid = $basePart === $this->baseFolder;

			return $valid;
		}

		public function getOffset( $path ) {
			if ( $this->isValid( $path ) ) {
				return substr( $path, strlen( $this->baseFolder ) );
			} else {
				throw new \InvalidArgumentException( 'Incorrect path' );
			}
		}

	}
}
?>
<?php

namespace Faid\View {
	use \Faid\StaticObservable;

	class View extends StaticObservable {
		protected $rendered = false;

		/**
		 * @var array
		 */
		protected $helpers = array();

		/**
		 * @var array
		 */
		protected $viewVars = array();

		/**
		 * @var View
		 */
		protected $layout = NULL;

		/**
		 * @var string
		 */
		protected $viewPath = '';

		/**
		 * @param $filePath
		 */
		public function __construct($filePath) {
			//
			$filePath = $this->getFilePath($filePath);
			//
			$this->filePath = $filePath;
		}
		public function __isset( $key ) {
			$key = strtolower($key);
			foreach ($this->helpers as $helperName => $helper) {
				$isSame = strtolower($helperName) === $key;
				if ( $isSame ) {
					return true;
				}
			}
			return false;
		}
		/**
		 * @param $key
		 *
		 * @return mixed
		 * @throws Exception
		 */
		public function __get($key) {
			$key = strtolower($key);

			//
			foreach ($this->helpers as $helperName => $helper) {
				$isSame = strtolower($helperName) === $key;
				if ( $isSame ) {
					return $helper;
				}
			}
			//
			throw new Exception(sprintf('Helper class `%s` not found inside view', $key));
		}

		public function addHelper($helper, $helperName = '') {

			//
			if ( !is_object($helper) ) {
				//
				if ( empty($helperName) ) {
					$helperName = $helper;
				}
				if ( !class_exists($helper) ) {
					$error = sprintf('Helper class `%s` not found', $helper);
					throw new Exception($error);
				}
				$helper = new $helper();
			} else {
				if ( empty($helperName) ) {
					$helperName = get_class($helper);
				}
			}
			//
			$this->helpers[ $helperName ] = $helper;
		}

		/**
		 * @param $layoutFile
		 */
		public function setLayout($layoutFile) {
			$layoutFile   = is_string($layoutFile) ? new View($layoutFile) : $layoutFile;
			$this->layout = $layoutFile;
		}

		/**
		 *
		 */
		public function getLayout() {
			return $this->layout;
		}

		/**
		 * Returns path to file
		 * @return mixed
		 */
		public function getPath() {
			return $this->filePath;
		}

		/**
		 *
		 */
		public function getViewVars() {
			return $this->viewVars;
		}

		/**
		 *
		 */
		public function setViewVars($newViewVars) {
			$this->viewVars = $newViewVars;
		}

		/**
		 * @param $key
		 * @param $data
		 */
		public function set($key, $data = NULL) {
			if ( is_null($data) ) {
				if ( is_array($key) || is_object($key) ) {
					foreach ($key as $varName => $value) {
						$this->set($varName, $value);
					}
				}
			} else {
				$this->viewVars[ $key ] = $data;
			}

		}

		/**
		 * @param $key
		 *
		 * @return mixed
		 */
		public function get($key) {
			if ( !isset($this->viewVars[ $key ]) ) {
				throw new Exception("Can`t find variable '$key' ");
			}

			return $this->viewVars[ $key ];
		}

		public function isRendered() {
			return $this->rendered;
		}

		/**
		 * @param array $vars
		 *
		 * @return string
		 */
		public function render() {
			//
			$this->beforeRender();
			//
			$oldContents = ob_get_contents();
			//
			if ( sizeof(ob_list_handlers()) > 0 ) {
				ob_clean();
			} else {
				ob_start();
			}
			//
			$content = $this->renderFile($this->viewVars, $this->filePath);
			//
			if ( !empty($this->layout) ) {
				$vars                         = $this->viewVars;
				$vars[ 'content_for_layout' ] = $content;
				$this->layout->set($vars);
				$content = $this->layout->render();
			}
			//
			print $oldContents;
			//
			$this->rendered = true;

			//
			return $content;
		}

		protected function renderFile($vars, $file) {
			extract($vars);
			include $file;
			$result = ob_get_contents();
			ob_clean();

			return $result;
		}

		/**
		 *
		 */
		protected function beforeRender() {
			// iterate over all helpers and call "beforeRender" method with $this as argument
			foreach ($this->helpers as $name => $helper) {
				$isCallable = is_callable(array($helper, 'beforeRender'));
				if ( $isCallable ) {
					$helper->beforeRender($this);
				}
				if ( !is_int($name) ) {
					$this->viewVars[ $name ] = $helper;
				}
			}
			// call event
			self::callEvent('View.render', $this);
		}

		/**
		 * @param $path
		 */
		protected function getFilePath($path) {
			if ( !file_exists($path) || !is_file($path) ) {
				$error = sprintf('Failed to find file - %s', $path);
				throw new Exception($error);
			}

			return realpath($path);
		}
	}
}
?>
<?php

namespace Faid\View {
    class Exception extends \Exception {

    }
}
?>
<?php
namespace Faid\Controller {
	use \Faid\Response\Response;
	use \Faid\Response\HttpResponse;
	use \Faid\StaticObservable;
	use \Faid\View\View;

	/**
	 * Basic page Controller (13.01.2006)
	 *
	 */
	class Controller extends StaticObservable
	{
		/**
		 * @var null
		 */
		protected $request = NULL;

		/**
		 * @var Response
		 */
		protected $response = NULL;

		/**
		 * @var View
		 */
		protected $view = NULL;
		/**
		 * @var bool
		 */
		protected $rendered = false;

		/**
		 *
		 * Enter description here ...
		 */
		public function __construct()
		{
		}

		/**
		 * Has to be called
		 */
		public function beforeAction( $request )
		{

		}

		/**
		 *
		 */
		public function render()
		{
			$this->view->render();
			$this->rendered = true;
		}

		/**
		 * Called by dispatcher after action method was called
		 */
		public function afterAction()
		{
			//
			if (empty( $this->response )) {
				//
				$isNeedRender = !empty( $this->view ) && !$this->view->isRendered();
				//
				$this->response = new HttpResponse();
				//
				if ( $isNeedRender ) {
					//
					$this->response->setData( $this->view->render() );
				} else {
				}
			}
			//
			if (!$this->response->isSent()) {
				//
				$this->response->send();
			}
		}

		/**
		 *
		 */
		protected function send( ) {
			//
			if ( empty( $this->response )) {
				//
				$isViewNotRendered = !empty( $this->view ) && !$this->view->isRendered();
				//
				if ( $isViewNotRendered) {
					//
					$this->getDefaultHTTPResponse();
					//
					$this->response->setData( $this->view->render());
				}
			}
			if ( !empty( $this->response )) {
				$this->response->send( );
			}
			return $this->response;
		}
		public function set( $key, $value ) {
			if ( !empty( $this->view )) {
				$this->view->set( $key, $value );
			} else {
				throw new Exception('View not defined');
			}
		}

		/**
		 * @return Response
		 */
		protected function getDefaultHTTPResponse( ) {
			if ( !empty( $this->response )) {
				//
				$this->response = new HttpResponse();
			}
			return $this->response;
		}
	}
}
?>
<?php
/**
 * @package faid
 */
namespace Faid\Response {
	use \Faid\StaticObservable;
	abstract class Response extends StaticObservable {
		/**
		 * @var bool
		 */
		protected $sent = false;
		/**
		 * Sets data for response
		 */
		public function setData( ) {
			self::callEvent('response.setData', $this, func_get_args());
		}
		/**
		 * Returns response data
		 */
		public abstract function getData();
		/**
		 * Outputs response to user
		 */
		public function send() {
			$this->sent = true;
		}
		public function isSent( ) {
			return $this->sent;
		}
	}
}
?>
<?php
namespace Faid\Response {
	use \Faid\Response as baseResponse;
	class HttpResponse extends Response {
		protected $content = '';
		protected $mimeType = '';
		/**
		 * Sets data to display
		 * @param $html
		 */
		public function setData( $html = '' ) {
			$this->content = $html;
			parent::setData( $html );
		}
		public function setMimeType( $type ) {
			$this->mimeType = $type;
		}
		public function getMimeType() {
			return $this->mimeType;
		}
		/**
		 * Sends file
		 * @param $fileName
		 * @param $mimeType
		 * @param $content
		 */
		public function sendFile( $fileName, $mimeType = null ) {
			// We'll be outputting a PDF
			$this->sendContentTypeHeader( $mimeType );
			// It will be called downloaded.pdf
			header('Content-Disposition: attachment; filename="'.$fileName.'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".strlen($this->content));
			if ( ob_get_level() ) {
				ob_clean();
			}
			// The PDF source is in original.pdf
			print $this->content;
		}

		/**
		 * Sends redirect request
		 * @param $url
		 */
		public function redirect( $url ) {
			header('Location: '. $url );
		}
		public function getData( ) {
			return $this->content;
		}
		public function send(){
			parent::send();
			if ( ob_get_level() ) {
				ob_clean();
			}
			$this->sendHeaders();
			print $this->content;
		}
		protected function sendHeaders() {
			if ( !headers_sent( )) {
				if ( !empty( $this->mimeType )) {
					$this->sendContentTypeHeader( $this->mimeType );
				}
			}
		}
		protected function sendContentTypeHeader( $mimeType = null ) {
			if ( empty( $mimeType )) {
				$mimeType = $this->mimeType;
			}
			header('Content-type: '.$mimeType);
		}
	}
}
?>
<?php
/**
 * @package Faid\Response
 */
namespace Faid\Response {
	use \Faid\Response as baseResponse;
	use \Faid\Debug;
	/**
	 * Class json
	 * @package Faid\Response
	 */
	class JsonResponse extends Response {
		protected $data = array();
		public function set( $key, $value) {
			$this->data[ $key ] = $value;
		}
		public function setData( $key = null, $value = null ) {
			if ( is_array( $key) ) {
				$this->data = array_merge( $this->data, $key );
			} else {
				$this->data[ $key ] = $value;
			}
			parent::setData( $this->data );
		}
		public function getData( ) {
			return $this->data;
		}
		public function send(){
			parent::send();
			print json_encode( $this->data );
		}
	}
}
?>
<?php
namespace Faid\Dispatcher {
    use Faid\Request\Request;
    use \Faid\StaticObservable;
    use \Faid\Request\HttpRequest;

    class Dispatcher extends StaticObservable
    {
        /**
         * @var array
         */
        protected $routes = array();

        /**
         * @var HttpRequest
         */
        protected $request = null;

        /**
         * @param HttpRequest $request
         */
        public function __construct(Request $request)
        {
            $this->request = $request;
        }

        public function getRequest()
        {
            return $this->request;
        }

        public function getNamed($name)
        {
            foreach ( $this->routes as $route ) {
                if ( $route->getName() == $name ) {
                    return $route;
                }
            }
            throw new RouteException(sprintf('Route with name %s not found', $name));
        }

        /**
         * @param Route $route
         */
        public function addRoute(Route $route)
        {
            $this->routes[] = $route;
        }


        /**
         * @return Route
         */
        public function run()
        {
            $route = $this->findRoute($this->request);
            //
            self::callEvent('Dispatcher.Route', $route);
            //
            $route->prepareRequest();

            //
            return $route;
        }

        /**
         * @param $request
         *
         * @return HttpRoute
         */
        protected function findRoute()
        {
            foreach ($this->routes as $row) {
                if ($row->test($this->request)) {
                    return $row;
                }
            }
            throw new RouteException('No matched route was found');
        }
    }
}
?>
<?php
namespace Faid\Dispatcher {
    class Route
    {
        protected $ready = false;

        protected $action = false;

        protected $controller = false;

        protected $request = false;

        protected $callback = false;

        protected $name = '';

        /**
         * @param $request
         */
        public function __construct($config = array())
        {
            $defaultConfig = array(
                'controller' => '',
                'action' => '',
                'request' => null,
                'callback' => null,
                'name' => null,
            );
            $config = array_merge($defaultConfig, $config);

            $this->action = $config['action'];
            $this->controller = $config['controller'];
            $this->request = $config['request'];
            $this->callback = $config['callback'];
            $this->name = !empty($config['name']) ? $config['name'] : uniqid('route_');
            $this->ready = false;
        }
        public function getName() {
            return $this->name;
        }
        /**
         *
         */
        public function getAction()
        {
            return $this->action;
        }

        /**
         *
         */
        public function getController()
        {
            return $this->controller;
        }

        /**
         * @param $action
         */
        public function setAction($action)
        {
            $this->action = $action;
        }

        /**
         * @param $controller
         */
        public function setController($controller)
        {
            $this->controller = $controller;
        }

        public function getCallback()
        {
            return $this->callback;
        }

        /**
         * @param $request
         *
         * @return bool
         */
        public function test($request)
        {
            $this->request = $request;
            //
            $this->ready = false;

            //
            return false;
        }

        /**
         * Called by dispatcher class. Main idea is - prepare request class to be called from controller
         */
        public function prepareRequest()
        {

        }

        protected function getRouteCallback()
        {
            if (empty($this->callback)) {
                //
                if (!empty($this->controller)) {
                    if (!is_object($this->controller)) {
                        $this->controller = new $this->controller();
                    }
                    if ($this->controller instanceof \Faid\Controller\Controller) {
                        $this->controller->beforeAction($this->request);
                    }
                    $callback = array($this->controller, $this->action);
                } else {
                    $callback = $this->action;
                }
            } else {
                $callback = $this->callback;
            }
            //
            if (!is_callable($callback)) {
                throw new RouteException('Route failed to dispatch. Callback not callable');
            }

            return $callback;

        }

        /**
         * @throws RouteException
         */
        public function dispatch()
        {
            if (!$this->ready) {
                throw new RouteException();
            }
        }
    }
}
?>
<?php
namespace Faid\Dispatcher {
	use \Faid\Request\HttpRequest;

	class HttpRoute extends Route {
		/**
		 * @var string
		 */
		protected $urlTemplate = '';

		/**
		 * @param $urlTemplate
		 */
		public function __construct( $config = array() ) {

			if ( empty($config['url']) ) {
				throw new RouteException('Route url template not specified');
			}
			$this->urlTemplate = $config['url'];

			//
			parent::__construct( $config );
		}
		/**
		 * @return string
		 */
		public function getUrlTemplate() {
			return $this->urlTemplate;
		}

		/**
		 * @param $request
		 *
		 * @return bool
		 */
		public function test($request) {
			parent::test($request);
			//
			$regExp = $this->getRegexp();
			//
			$this->ready = @preg_match($regExp, $request->url());

			//
			return $this->ready;
		}

		protected function getRegExp() {
			$result = '@' . $this->urlTemplate . '@si';
			// Replace wildcard
			$result = str_replace('*', '(.*)', $result);
			//
			$result = preg_replace('/:(\w+)/', '([\w-\.]+)', $result);

			//
			return $result;
		}

		/**
		 * @throws RouteException
		 * @return HttpRoute
		 */
		public function dispatch() {
			parent::dispatch();
			//
			$callback = $this->getRouteCallback();
			call_user_func($callback, $this->request, $this );
			//
			$isController = is_array( $callback ) && is_object( $callback[0]) && ( $callback[0] instanceof \Faid\Controller\Controller );
			//
			if ( $isController ) {
				$controller = $callback[0];
				$controller->afterAction( );
			}
		}
        public function buildUrl( $data = []) {
            krsort( $data );
            $search = [];
            $replacements = [];

            foreach ( $data as $key=>$row ) {
                $search[] = ':' . $key;
                $replacements[] = $row;
            }
            return str_replace( $search, $replacements, $this->urlTemplate );
        }
		/**
		 *
		 */
		public function prepareRequest() {
			$regExp = $this->getRegExp();
			preg_match($regExp, $this->request->url(), $matches);
			//
			$unnamedParamIndex = 1;

			if ( preg_match_all("/(\*)|:([\w-]+)/", $this->urlTemplate, $argument_keys) ) {
				$params = array();
				// grab array with matches
				$argument_keys = $argument_keys[ 0 ];

				// loop trough parameter names, store matching value in $params array
				foreach ($argument_keys as $key => $name) {
					if ( '*' != $name ) {
						$name = substr($name, 1);
					}
					if ( isset($matches[ $key + 1 ]) ) {
						if ( '*' != $name ) {
							if ( !in_array($name, array('action', 'controller')) ) {
								$params[ $name ] = $matches[ $key + 1 ];
							} else {
								$this->$name = strtolower($matches[ $key + 1 ]);
							}
						} else {
							$list = explode('/', $matches[ $key + 1 ]);
							foreach ($list as $row) {
								$name = 'param' . $unnamedParamIndex;
								$unnamedParamIndex++;
								$params[ $name ] = $row;
							}
						}


					}
				}
				$this->request->set($params);
			}

		}
	}
}
?>
<?php
namespace Faid\Dispatcher {
	class RouteException extends \Exception {
	}
}
?>
<?php
namespace Faid\Request {
	class Request extends \Faid\StaticObservable {
		protected $data = array();
		protected $validationErrors = array();

		public function __construct( $initialData = array()) {
			$this->set( $initialData );
		}

		public function __set($key, $value) {
			return $this->set( $key, $value );
		}

		public function __get($key) {
			return $this->get( $key );
		}
		public function set( $key, $value = null) {
			if ( !is_scalar( $key )) {
				$data = $key;
				foreach ( $data as $key=>$row ) {
					$this->set( $key, $row );
				}
				return ;
			}
			$this->data[ $key ] = $value;
		}
		public function get( $key ) {
			if ( !isset( $this->data[ $key ])) {
				$error = sprintf( 'Parameter `%s` not found', $key );
				throw new \Exception( $error );
			}
			return $this->data[ $key ];
		}
		public function url( ) {

		}
		public function domain( $domainName = null ) {

		}
		/**
		 * @param bool $uri
		 *
		 * @return bool
		 */
		public function uri( $uri = null) {
		}
		public function addValidator($fieldName, $validationMethod) {

		}

		public function getValidationErrors() {

		}

		public function validate() {

		}
	}
}
?>
<?php
namespace Faid\Request {
	class HttpRequest extends Request {
		protected $uri = false;
        protected $url = null;
		protected $domainName = '';
		/**
		 * @param array $data
		 */
		public function __construct( $data = array() ) {
			if ( empty( $data )) {
				$data = $_REQUEST ;
			}
			//
			parent::__construct( $data ) ;
			//
			$this->detectURI();
			$this->detectDomain();
		}

		/**
		 * @return string
		 */
		public function url( $url = null ) {
            if ( is_null( $url )) {
                if ( is_null( $this->url)) {
                    $https = !empty( $_SERVER['HTTPS']) ? true : false;
                    $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
                    $result = sprintf('http%s://%s%s',
                        $https ? 's' : '',
                        $host,
                        $this->uri( )
                    );
                    return $result;
                }
                return $this->url;
            } else {
                $this->url = $url;
                $this->uri = null;
            }

		}

		/**
		 * @return mixed
		 */
		public function getMethod( ) {
			return $_SERVER['REQUEST_METHOD'];
		}
		public function domain( $domainName = null ) {
			if ( !empty( $domainName )) {
				$this->domainName =$domainName;
			} else {
				return $this->domainName;
			}

		}
		/**
		 * @param bool $uri
		 *
		 * @return bool
		 */
		public function uri( $uri = null) {
			if ( !empty( $uri )) {
				$this->uri = $uri;
                $this->url = null;
			}
			return $this->uri;
		}

		protected function detectURI( ) {
			if (!empty($_SERVER['PATH_INFO'])) {
				$uri = $_SERVER['PATH_INFO'];
			} elseif (isset($_SERVER['REQUEST_URI'])) {
				$uri = $_SERVER['REQUEST_URI'];
			} elseif (isset($_SERVER['PHP_SELF']) && isset($_SERVER['SCRIPT_NAME'])) {
				$uri = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']);
			} elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
				$uri = $_SERVER['HTTP_X_REWRITE_URL'];
			} elseif ($var = env('argv')) {
				$uri = $var[0];
			}
			if (strpos($uri, '?') !== false) {
				list($uri) = explode('?', $uri, 2);
			}
			if (empty($uri) || $uri == '/' || $uri == '//') {
				$uri = '/';
			}
			$this->uri = $uri;
		}
		protected function detectDomain( ) {
			if ( !empty( $_SERVER['HTTP_HOST'])) {
				$this->domainName = $_SERVER['HTTP_HOST'];
			}
		}
	}
}
?>
<?php
namespace Faid\Request {
	class CommandLineRequest extends Request {
		protected $uri = false;
		protected $domainName = '';
		/**
		 * @param array $data
		 */
		public function __construct( $data = array() ) {
			if ( empty( $data )) {
				$data = $_REQUEST ;
			}
			//
			parent::__construct( $data ) ;
			//
			if ( !empty( $data) ) {
				$this->parseURL();
			}

		}

		/**
		 * @return string
		 */
		public function url() {
			$result = sprintf( 'http://%s%s', $this->domain(), $this->uri());
			return $result;
		}

		/**
		 * @return mixed
		 */
		public function getMethod( ) {
			return 'GET';
		}
		public function domain( $domainName = null ) {
			if ( !empty( $domainName )) {
				$this->domainName =$domainName;
			} else {
				return $this->domainName;
			}

		}
		/**
		 * @param bool $uri
		 *
		 * @return bool
		 */
		public function uri( $uri = null ) {
			if ( !is_null( $uri )) {
				if ( !empty( $uri )) {
					if ( $uri[0] != '/') {
						$uri  = '/'. $uri;
					}
				} else {
					$uri = '/';
				}

				$this->uri = $uri;
			}
			return $this->uri;
		}

		protected function parseURL( ) {
			if ( preg_match( '/http\:\/\/([^\/]+)(.*)/i',$this->data[0], $matchers)) {
				$this->domainName = $matchers[1];
				$this->uri = $matchers[2];
			}
			if ( empty( $this->uri )) {
				$this->uri = '/';
			}
		}

	}
}
?>
<?php
namespace Faid\Request {
	class ValidationException extends \Exception {
	}
}
?>
<?php
namespace Faid\Request\Validator {
	class Validator {
		public function beforeValidate($request) {

		}

		public function validate($request) {

		}
	}
}
?>
<?php
namespace Faid\Request\Validator {
	class Email extends Validator {

	}
}
?>
<?php
namespace Faid\Request\Validator {
	class Integer extends Validator {

	}
}
?>
<?php
namespace Faid\Request\Validator {
	class Url extends Validator {

	}
}
?>
<?php
namespace Faid {
use \Faid\Configure\Configure;



class SimpleCache {
	const ConfigurePath = 'SimpleCache';
	protected static $basePath = '';

	protected static $instance = null;
	public static function getInstance() {
		if ( empty( self::$instance )) {
			self::factoryInstance();
		}
		return self::$instance;
	}

	/**
	 * @return \Faid\Cache\Engine\CacheEngineInterface
	 */
	protected static function factoryInstance() {
		$engineClass = Configure::read( self::ConfigurePath . '.Engine');
		self::$instance = new $engineClass();
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function get($key) {
		return self::getInstance()->get( $key );
	}
	public static function set($key, $value, $timeActual = 0 ) {
		return self::getInstance()->set( $key, $value, $timeActual );
	}
	public static function clear($key) {
		self::getInstance()->clear( $key );
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public static function isActual($key ) {
		return self::getInstance()->isActual( $key );
	}

}

}
?>
<?php


namespace Faid\Cache {

	class Exception extends \Exception {

	}
}
?>
<?php
namespace Faid\Cache\Engine {

	interface CacheEngineInterface {
		public function get( $key );

		public function set( $key, $value, $timeActual = 0);

		public function clear( $key );

		public function isActual( $key );
	}
}
?>
<?php
namespace Faid\Cache\Engine {
	use \Faid\Cache\Exception;
	use \Faid\Configure\Configure;

	class FileCache implements CacheEngineInterface {

		const ConfigurePath = 'SimpleCache.FileCache';


		protected $basePath = '';

		protected $lastLoadedFile = '';
		protected $lastLoadedData = array();

		/**
		 * Создает кеш и сохраняет его на файловую систему
		 *
		 * @param string $key  ключ хеша
		 * @param mixed  $data данные для хеширования
		 */
		public function set( $key, $data, $timeActual = 0 ) {
			// Проверяем ключ на пустоту

			if ( empty( $key ) or preg_match( '#..\/.\/#', $key ) ) {
				throw new Exception( 'Invalid cache name' );
			}

			// Создаем файл
			$path = $this->getPath( $key );

			$data = array(
				'expire' => time() + $timeActual,
				'data'     => $data
			);
			$data = serialize( $data );
			//

			file_put_contents( $path, $data, LOCK_EX );

//			Trace::addMessage( 'SimpleCache', 'Cache `' . $key . '` created' );
		}

		/**
		 * Синоним метода load
		 *
		 * @param string $key
		 */
		public function get( $key ) {
			$path = $this->getPath( $key );
			if ( $path == $this->lastLoadedFile ) {
				return $this->lastLoadedData['data'];
			}
			$this->loadData( $path );
			if ( !$this->testIfCurrentCacheActual()) {
				throw new Exception('Cache "'.$key.'" not actual');
			}
//			Trace::addMessage( 'SimpleCache', 'Cache `' . $key . '` loaded' );
			return $this->lastLoadedData[ 'data' ];
		}

		protected function loadData( $path ) {
			$validator = new \Faid\Validators\FileInSecuredFolder( $this->basePath );
			if ( !$validator->isValid( $path ) ) {
				throw new Exception( 'File restricted by security settings: ' . $path );
			}

			$data                 = file_get_contents( $path );
			$this->lastLoadedData = unserialize( $data );
			$this->lastLoadedFile = $path;
		}

		/**
		 * Удаляет кеш
		 *
		 * @param string $key
		 */
		public function clear( $key ) {
			$path = self::getPath( $key );
			if ( file_exists( $path ) && is_file( $path ) ) {
				unlink( $path );
			} else {
				throw new Exception( 'Path `' . $key . '` isn`t file' );
			}
//			Trace::addMessage( 'SimpleCache', 'Cache `' . $key . '` cleared' );
		}

		/**
		 * Проверяет время последнего обновления кеша
		 *
		 * @param string $key
		 * @param int    $time
		 */
		public function cacheOlder( $key, $time ) {
			// Если время отрицательное, то воспринимаем его как смещение от текущего момента
			// т.е. оно равно = текущеее время - abs($time)
			if ( $time < 0 ) {
				$time = time() + $time;
			}
			$path = self::getPath( $key );
			if ( !file_exists( $path ) ) {
				throw new Exception( 'Uknown path="' . $path . '"' );
			}
			$timeModified = filemtime( $path );
			if ( $timeModified < $time ) {
				$message = sprintf( 'Cache `%s` older than %s', $key, date( 'Y-m-s H:i:s', $time ) );
//				Trace::addMessage( 'SimpleCache', $message );
				return true;
			} else {
				$message = sprintf( 'Cache `%s` still actual', $key );
//				Trace::addMessage( 'SimpleCache', $message );
				return false;
			}
		}

		public function isActual( $key ) {
			$path = $this->getPath( $key );
			if ( $path == $this->lastLoadedFile ) {
				return $this->lastLoadedData;
			}
			try {
				$this->loadData( $path );
			} catch (Exception $e ) {
				return false;
			}
			return $this->testIfCurrentCacheActual();

		}
		protected function testIfCurrentCacheActual() {
			if ( time() >= $this->lastLoadedData[ 'expire' ] ) {
				return false;
			}
			return true;
		}
		protected function getPath( $key ) {
			$path = $this->basePath . $key;

			return $path;
		}

		public function __construct() {
			$key            = self::ConfigurePath . '.BaseDir';
			$this->basePath = Configure::read( $key );
		}
	}

}
?>
<?php
namespace Faid\Cache\Engine {
	use \Memcache as PeclMemcache;
	use \Faid\Cache\Exception;

	class Memcache implements CacheEngineInterface {
		const ConfigurePath = 'SimpleCache.Memcache';
		protected $config = null;
		protected $prefix = '';
		/**
		 * @var PeclMemcache
		 */
		protected $instance = null;

		public function __construct() {
			$this->autoloadConfig();

			$this->instance = new PeclMemcache();
			foreach ( $this->config[ 'servers' ] as $row ) {
				$result = $this->instance->addserver( $row[ 'host' ], !empty( $row[ 'port' ] ) ? $row[ 'port' ] : null );
				if ( !$result ) {
					throw new Exception( 'Failed to connect to server:' . print_r( $row, true ) );
				}
			}
		}



		public function get( $key ) {
			$key = $this->prefix . $key;
			$flags = null;
			$result = $this->instance->get( $key, $flags );
			// $flags stays untouched if $key was not found on the server
			// @see http://php.net/manual/ru/memcache.get.php#112056
			if ( empty( $result ) && empty( $flags ) ) {
				throw new Exception( 'Cache "' . $key . '" not found' );
			}
			return $result;
		}

		public function set( $key, $value, $timeActual = null ) {
			$key = $this->prefix . $key;
			$this->instance->set( $key, $value, 0, $timeActual );
		}

		public function clear( $key ) {
			$key = $this->prefix . $key;
			$this->instance->delete( $key );
		}

		public function isActual( $key ) {
			$key = $this->prefix . $key;
			$flags  = 0;
			$result = $this->instance->get( $key, $flags );
			// $flags stays untouched if $key was not found on the server
			// @see http://php.net/manual/ru/memcache.get.php#112056
			if ( empty( $result ) && empty( $flags ) ) {
				return false;
			}
			return true;
		}
		public function getInstance() {
			return $this->instance;
		}
		protected function autoloadConfig() {
			$this->config = \Faid\Configure\Configure::read( self::ConfigurePath );
			$valid        = isset( $this->config[ 'servers' ] ) && is_array( $this->config[ 'servers' ] );
			if ( !$valid ) {
				throw new Exception( 'Memcache config not valid' );
			}
			$this->prefix = !empty( $this->config['prefix'] ) ? $this->config['prefix'] : '';
		}
	}
}
?>