<?
use \Faid\DB;
use \Faid\DBSimple;
use \Faid\Cache\Exception as SimpleCacheException;
use \Faid\SimpleCache;

/**
 * Модуль ведет историю аж с 19.01.2009
 * 2015.02.15 а сегодня он будет основательно переписан - покиляю всю работу с БД
 * Class EventController
 * @package extasycms.events
 */
class EventController {
	protected static $aRuntimeEventListeners = array();

    public static function cleanUp() {
        self::$aRuntimeEventListeners = [];

    }
	public static function addRuntimeEventListener( $eventName, $function, $className = '', $file = '' ) {
		if ( !isset( self::$aRuntimeEventListeners[ $eventName ] ) ) {
			self::$aRuntimeEventListeners[ $eventName ] = array();
		}
		self::$aRuntimeEventListeners[ $eventName ][ ] = array(
			'function' => $function,
			'class'    => $className,
			'file'     => $file
		);
	}


	/**
	 * Определяет есть ли у события методы-слушатели
	 */
	public static function hasListeners( $szEventName ) {
		if ( !empty( self::$aRuntimeEventListeners[ $szEventName ] ) ) {
			return true;

		} else {
			return false;
		}
	}

	/**
	 * Вызывает указанное событие $szEvent. Основное назначение отфильтровать поданные данные, каждый слушатель данного события должен вернуть
	 * данные, которые заместят начальный массив aData
	 *
	 * @param $szEventName string имя события
	 * @param $initialData array данные к событию
	 */
	public static function callFilter( $eventName, $initialData ) {
		$result = $initialData;
		if ( isset( self::$aRuntimeEventListeners[ $eventName ] ) ) {
			$eventListeners = self::$aRuntimeEventListeners[ $eventName ];
			foreach ( $eventListeners as $row ) {
				$result = self::callEventListener( $row, array( $result ) );
			}
		}

		return $result;
	}

	/**
	 * Вызывает событие
	 */
	public static function callEvent( $eventName ) {
		$aArguments = func_get_args();
		array_shift( $aArguments );
		if ( isset( self::$aRuntimeEventListeners[ $eventName ] ) ) {

			$aEventListeners = self::$aRuntimeEventListeners[ $eventName ];
			$aResult         = array();
			foreach ( $aEventListeners as $row ) {
				$aResult[ ] = self::callEventListener( $row, $aArguments );
			}
			return $aResult;
		} else {
			return array();
		}
	}

	///////////////////////////////////////////////////////////////////////////
	//
	protected static function callEventListener( $eventListener, $arguments ) {
		if ( !empty( $eventListener[ 'file' ] ) ) {
			self::loadFile( $eventListener[ 'class' ], $eventListener[ 'function' ], $eventListener[ 'file' ] );
		}
		if ( !empty( $eventListener[ 'class' ] ) ) {

			$func = array( $eventListener[ 'class' ], $eventListener[ 'function' ] );
		} elseif ( !empty( $eventListener[ 'function' ] ) ) {
			$func = $eventListener[ 'function' ];
		} else {
			throw new EventException( 'Don`t know how to call that event listener. Listener info:' . print_r( $eventListener,
																											  true ) );
		}
		if ( is_callable( $func ) ) {
			$result = call_user_func_array( $func, $arguments );
		} else {
			$errorMsg = sprintf( 'Event listener [%s] not callable ',
								 is_array( $func ) ? ( $func[ 0 ] . ':' . $func[ 1 ] ) : $func );
			CMSLog::addMessage( 'events', $errorMsg );

			return null;
		}

		return $result;
	}

	/**
	 *
	 * Returns path if it exists
	 *
	 * @param unknown_type $file
	 */
	protected static function detectPath( $file ) {
		$testPath1 = realpath( LIB_PATH . $file );
		$testPath2 = realpath( APPLICATION_PATH . $file );

		return !empty( $testPath1 ) ? $testPath1 : $testPath2;
	}

	protected static function loadFile( $class, $function, $file ) {

		$path = self::detectPath( $file );
		if ( empty( $path ) ) {
			throw new EventException( 'Call event failed. Tried to load callback file. File:' . $file );
		}
		$isEmpty = empty( $class ) && empty( $function );
		if ( $isEmpty ) {
			include $path;
		} else {
			require_once $path;
		}
	}
}

?>