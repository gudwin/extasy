<?
use \Faid\DB;

//************************************************************//
//                                                            //
//        Класс восстановления урлов в карте сайта            //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (15.12.2008)                           //
//  Модифицирован:  15.12.2008  by Gisma                      //
//                                                            //
//************************************************************//
//  Задача класса установить корректные значение аттрибута    //
//  full_url для рядов таблицы SITEMAP_TABLE                  //
class Sitemap_RestoreUrl {

	protected static $aItem = array();
	protected static $aResult = array(); // Результат работы скрипта
	/**
	 * Восстанавливает адрес только конкретного элемента
	 *
	 * @param int $sitemapId
	 */
	public static function restoreOne( $sitemapId, $sitemapParent = null ) {

		$sitemap = is_array( $sitemapId ) ? $sitemapId : Sitemap_Sample::get( $sitemapId );
		if ( empty( $sitemap ) ) {
			throw new NotFoundException( 'Url keeping process failed, sitemap element (' . $sitemapId . ') not found' );
		}
		//
		if ( is_null( $sitemapParent ) ) {
			$sitemapParent = Sitemap_Sample::get( $sitemap[ 'parent' ] );
		}
		if ( empty( $sitemapParent ) ) {
			$baseUrl = self::getCurrentBaseUrl();
		} else {
			$baseUrl = $sitemapParent[ 'full_url' ];
		}

		if ( self::isDomainPresentInUrlKey( $sitemap ) ) {
			$url = $sitemap[ 'url_key' ];
		} else {
			$url = $baseUrl . $sitemap[ 'url_key' ] . '/';
		}

		// Если старый и новый адрес равны, то и менять ничего не надо
		if ( $url == $sitemap[ 'full_url' ] ) {
			return;
		}
		// Сохраняем изменение
		$sql = 'UPDATE `%s` SET `full_url`="%s" where `id`="%d"';
		$sql = sprintf(
			$sql,
			SITEMAP_TABLE,
			\Faid\DB::escape( $url ),
			$sitemap[ 'id' ] );

		$sitemap[ 'full_url' ] = $url;
		DB::post( $sql );
		// 
		self::updateChildUrls( $sitemap );
	}

	protected static function updateChildUrls( $sitemap ) {
		$child = Sitemap_Sample::selectChild( $sitemap[ 'id' ] );
		foreach ( $child as $row ) {
			self::restoreOne( $row[ 'id' ], $sitemap );
			Sitemap_History::add( $row[ 'id' ], $row[ 'name' ] );
		}
	}

	public static function exec() {
		// Получаем все ряды
		self::loadRows();
		// Обнуляем массив результатов (вдруг, раньше вызывали?-)
		self::$aResult = array();
		foreach ( self::$aItem as $row ) {
			if ( empty( self::$aResult[ $row[ 'id' ] ] ) ) {
				if ( preg_match( '/^http\:\/\//', $row[ 'url_key' ] ) ) {
					self::$aResult[ $row[ 'id' ] ] = $row[ 'url_key' ];
				} else {
					self::$aResult[ $row[ 'id' ] ] = self::getFullUrl( $row[ 'parent' ] ) . $row[ 'url_key' ] . '/';
				}

			}

		}

		// Сохраняем ряды
		self::storeRows();

	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Загружает во внутренний кеш всю таблицу сайта
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function loadRows() {
		$sql         = 'SELECT * FROM `%s` ORDER by `id` ASC ';
		$sql         = sprintF( $sql, SITEMAP_TABLE );
		self::$aItem = DB::query( $sql );
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Сохраняет результат работы метода exec
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function storeRows() {
		$sqlTemplate = 'UPDATE `%s` SET `full_url`="%s" where `id`="%d"';
		foreach ( self::$aResult as $nId => $szFullUrl ) {
			$sql = sprintf( $sqlTemplate,
							SITEMAP_TABLE,
							\Faid\DB::escape( $szFullUrl ),
							intval( $nId ) );
			DB::post( $sql );
		}
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Возвращает полный урл для страницы
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function getFullUrl( $nId ) {

		if ( !empty( self::$aResult[ $nId ] ) ) {
			return self::$aResult[ $nId ];
		}
		// Получаем информацию о верхнем ряде
		$sql   = 'SELECT * FROM `%s` WHERE `id`="%d" LIMIT 0,1';
		$sql   = sprintf( $sql, SITEMAP_TABLE, $nId );
		$aInfo = DB::get( $sql );

		if ( empty( $aInfo ) ) {
			return '';
		}

		// Вызываем с верхним рядом
		if ( !empty( $aInfo[ 'parent' ] ) ) {
			$szResult = self::getFullUrl( $aInfo[ 'parent' ] ) . $aInfo[ 'url_key' ] . '/';

		} else {
			$szResult = '/' . $aInfo[ 'url_key' ] . '/';
		}
		return $szResult;
	}

	protected static function getCurrentBaseUrl() {
		$request = \Extasy\CMS::getInstance()->getDispatcher()->getRequest();
		$result  = sprintf( '//%s/', $request->domain() );
		return $result;
	}

	protected static function isDomainPresentInUrlKey( $sitemap) {
		return '//' == substr( $sitemap['url_key'], 0, 2);
	}
}

?>