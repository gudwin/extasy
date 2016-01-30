<?php
namespace Faid {
	use \Faid\StaticObservable;

	/**
	 * Basic page controller (13.01.2006)
	 *
	 */
	class Page extends StaticObservable {
		/**
		 * @var array Массив хранит все существющие схемы обработок для POST
		 */
		protected $aPostSchema = array();
		/**
		 * @var array Массив, хранящий существующие схемы обработок для GET
		 */
		protected $aGetSchema = array();
		/**
		 * Хранит массив схем обработок для _FILES
		 * @var array
		 */
		protected $aFilesSchema = array();

		protected $aFunction = array();

		protected $response = null;
		/**
		 * @var string Строка хранящая имя функции по умолчанию
		 */
		var $szDefault = '';
		/**
		 *
		 * Если установлено в true, то метод всегда будет вызывать метод output по завершению своей работы
		 * @var bool
		 */
		protected $autoRender = false;

		/**
		 *
		 * Enter description here ...
		 */
		function __construct() {
		}

		/**
		 *   Дефолтовая функция куда передается управления страницой
		 * @return
		 */
		public function main() {
		}

		/**
		 * @desc Добавляет новый обработчик _GET
		 * @return
		 */
		public function addPost( $name, $action ) {
			// добавляем функцию
			$this->aPostSchema[ $name ] = $action;
		}

		/**
		 * Добавляет новый обработчик _FILES
		 */
		public function addFiles( $name, $action ) {
			$this->aFilesSchema[ $name ] = $action;
		}

		/**
		 * @desc Добавляет новый обработчик _POST
		 * @return
		 */
		function addGet( $name, $action ) {
			$this->aGetSchema[ $name ] = $action;
		}

		/**
		 * Вызов данного метода стартует внутрениий роутер класса. Данный роутер определяет тип запроса, поступившего на страницу.
		 * Потом осуществляется поиск метода класса, который может быть вызван, сравнение осуществляется по переменным запроса.
		 */
		public function process() {
			self::callEvent( 'Page::init' );
			$szMethod = isset( $_SERVER[ 'REQUEST_METHOD' ] ) ? $_SERVER[ 'REQUEST_METHOD' ] : 'GET';

			if ( $szMethod == 'GET' ) {
				$found = $this->callProcs( $this->aGetSchema, $_GET );
			} else if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

				if ( !empty( $_FILES ) ) {
					$found = $this->callProcs( $this->aFilesSchema, $_FILES );
				}
				$found = $this->callProcs( $this->aPostSchema, $_POST );
			}

			// После всех вызовов, вызываем дефолтовый перехватчик
			if ( !$found ) {
				$this->main();
			}
			if ( $this->autoRender ) {

				$this->output();
			}
		}

		/**
		 * @desc Отыскивает функцию совпадающую по параметра на вызываемую
		 */
		protected function callProcs( $aSchema, $aData ) {

			$result = false;
			uksort( $aSchema, array( __CLASS__, 'sortParams' ) );
			// определяем массивы данных
			$aDataArguments = array_keys( $aData );
			// определяем активную схему
			// схема - список необходимых переменных находящихся в запросе, чтобы вызвалась функция ассоциированная с ними
			$szSchema = -1;
			foreach ( $aSchema as $key => $value ) {
				$aParams = $this->getSchemaParams( $key );
				if ( sizeof( array_intersect( $aDataArguments, $aParams ) ) == sizeof( $aParams ) ) {

					$szSchema = $key;
					break;
				}
			}

			// для активной схемы
			if ( $szSchema >= 0 ) {
				// собираем аргументы по схеме
				$aParams   = $this->getSchemaParams( $szSchema );
				$aArgument = array();

				foreach ( $aParams as $szName ) {
					$aArguments[ ] = $aData[ $szName ];
				}
				$method = $aSchema[ $szSchema ];
				if ( is_callable( array( &$this, $method ) ) ) {
					self::callEvent( 'Page::foundFunc', array( $this, $method ) );
					call_user_func_array( array( &$this, $method ), $aArguments );
					$result = true;
				} else {
					trigger_error( 'Page::callProcs Method `' . $aSchema[ $szSchema ] . '` doesn`t exists' );
				}
			} else {

			}
			return $result;

		}

		/**
		 * Данный метод завершает работу страницы и просто выводит алерты
		 */
		protected function output() {
			if ( !is_null( $this->response ) ) {

				$this->response->render();
			}
			self::callEvent( 'Page.output' );
		}

		/**
		 * Вызов возвращает данные на отправку

		 */
		protected function outputJSON( $data = array() ) {
			print json_encode( $data );
			die();
		}

		/**
		 * Сортирует параметры по следующему правилу:
		 * 1. более длинные селекторы воспринимаются как более длинные
		 * 2. в остальных случаях строковое сравнение
		 */
		protected function sortParams( $a, $b ) {
			if ( strlen( $a ) > strlen( $b ) ) {
				return false;
			} elseif ( strlen( $a ) == strlen( $b ) ) {
				return $a < $b;
			} else {
				return true;
			}
		}


		///////////////////////////////////////////////////////////////////////////
		// Privated methods
		/**
		 * Конвертирует список параметров в массив
		 */
		private function getSchemaParams( $szSchema ) {
			$aParams = explode( ',', $szSchema );
			// Фильтруем пустые значения
			$aParams = array_filter( $aParams );
			return $aParams;
		}
		///////////////////////////////////////////////////////////////////////////
		// Public statis methods
		/**
		 * @desc Выполняет редирект на указанный url
		 */
		public static function jump( $url = '' ) {
			if ( substr( $url, 0,2 ) == '//') {
				$url = 'http:'. $url;
			}

			if ( defined( 'CLI_MODE' )) {
				printf( 'Redirect to "%s" called', $url );

			}
			if ( !headers_sent() ) {
				/** высылаем Header */
				header( 'HTTP/1.1 302 Found' );
				header( "Location: " . $url );
			} else {
				printf( '<script type="text/javascript">document.location.href=%s;</script>' . "\r\n",
						json_encode( $url ) );
			}
			die();
		}

		/**
		 * Enter description here ...
		 */
		static public function jumpBack() {
			$szUrl = !empty( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '/';
			self::jump( $szUrl );
		}
	}
}
?>
