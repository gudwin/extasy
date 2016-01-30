<?
use \Faid\DB;

class Sitemap_CMS {
	const RootDocumentsKey = '/System/Sitemap/RootDocuments/';


	public static function setupRootDocuments( array $documentList ) {
		$register = new SystemRegister( self::RootDocumentsKey );
		SystemRegisterHelper::import( $register, $documentList );
		SystemRegisterSample::createCache();
	}

	public static function getRootDocuments() {
		$register = new SystemRegister( self::RootDocumentsKey );
		return SystemRegisterHelper::export( $register->getId() );
	}

	public static function getParentsForDocument( $document_name, $document_id, $nLimitId = 0 ) {
		$aInfo = Sitemap_Sample::getByDocumentId( $document_name, $document_id );
		if ( empty( $aInfo ) ) {
			throw new Exception( 'Document not found (' . $document_name . ',' . $document_id . ')' );
		}
		return self::getParents( $aInfo[ 'id' ], $nLimitId );
	}

	/**
	 * Возвращает предков меню Метод со встроенным кешем результатов
	 *
	 * @param int $id
	 * @param int $nLimitId индекс на котором поиск остановится
	 */
	public static function getParents( $id, $nLimitId = 0 ) {
		static $cache = array();
		$cacheKey = $id . '_' . $nLimitId;
		if ( isset( $cache[ $cacheKey ] ) ) {
			return $cache[ $cacheKey ];
		}
		$aResult = array();
		//
		$nCurrentId = intval( $id );;
		$nLimitId = intval( $nLimitId );

		while ( $nCurrentId != $nLimitId ) {
			$aInfo = Sitemap_Sample::get( $nCurrentId );
			if ( empty( $aInfo ) ) {
				return $aResult;
			}
			$nCurrentId = $aInfo[ 'parent' ];
			array_unshift( $aResult, $aInfo );
		}
		$cache[ $cacheKey ] = $aResult;

		return $aResult;
	}

	/**
	 *
	 * @param unknown $typeName
	 * @param unknown $sitemapInfo
	 * @param unknown $parentMenu
	 */
	public static function generateDocumentSubmenu( $typeName, $sitemapInfo, $parentMenu ) {
		$typeInfo   = $typeName::getFieldsInfo();
		$buttonList = array();
		// Если пусты кнопки, то можем попробовать найти их в информации о типе
		if ( !empty( $typeInfo[ 'cms_buttons' ] ) ) {
			$buttonList = $typeInfo[ 'cms_buttons' ];
		}
		$validator        = new \Extasy\Validators\ModelConfigValidator( $typeName, array('sitemap',RegisteredDocument::ChildrenConfigName ));
		$modelHasChildren = $validator->isValid();
		if ( $modelHasChildren ) {
			reset( $parentMenu );
			$url = current( $parentMenu );
			// Если это не ссылка на sitemap, то
			if ( !preg_match( '/^sitemap/', $url ) ) {

				$url = preg_replace( '/parent=[0-9]+/', 'parent=' . $sitemapInfo[ 'id' ], $url );

				if ( !empty( $sitemapInfo[ 'count' ] ) ) {
					$buttonList[ 'перейти к детям (' . $sitemapInfo[ 'count' ] . ')' ] = $url;
				} else {

					$buttonList[ 'добавить дочерние документы' ] = $url;
				}
			}
		}
		return $buttonList;
	}

	/**
	 * Формирует хлебные крошки для страницы.
	 * ОБЩИЙ ПРИНЦИП: перебор всех предков, по url_key пытаемся восстановить скрипт, который выводит подветку карты сайта
	 * если не найден скрипт, то подключаем основной блок aSitemap

	 */
	public static function selectBegin( $aParent, $szTitle ) {
		$aResult = array();
		// Начинаем перебор с самого первого предка 

		// Устанавливаем первым 100%-рабочий URL, основной контрол карты сайта
		$szUrlKey = '../sitemap/page-list.php?parent=';

		foreach ( $aParent as $key => $row ) {
			// Обрабатываем ситуация url_key = "" (нарпимер, главная страница сайта)
			if ( empty( $row[ 'url_key' ] ) ) {
				$row[ 'url_key' ] = 'index';
			}
			// Вырезаем лидирующий слеш
			if ( $row[ 'url_key' ][ 0 ] == '/' ) {
				$row[ 'url_key' ] = substr( $row[ 'url_key' ], 1 );
			}
			$aResult[ $row[ 'name' ] ] = $szUrlKey . $row[ 'id' ];
		}
		$aResult[ $szTitle ] = '#';

		return $aResult;

	}

	/**
	 * Возвращает список путей, куда может быть перемещен данный документ
	 */
	public static function whereToMove( $documentName ) {
		$validator = new \Extasy\Validators\ModelConfigValidator( $documentName, array('sitemap',RegisteredDocument::ParentsConfigName ));
		$result    = array();
		if ( $validator->isValid() ) {
			$documents = $validator->getData();
			if ( !empty( $documents ) ) {
				foreach ( $documents as $key => $row ) {
					$documents[ $key ] = sprintf( '"%s"', \Faid\DB::escape( $row  ) );
				}
			} else {
				$documents = array(-1);
			}
			$scripts = self::selectDocumentScriptParents( $documentName );
			if ( !empty( $scripts ) ) {
				foreach ( $scripts as $key => $row ) {
					$scripts[ $key ] = sprintf( '"%s"', \Faid\DB::escape( $row[ 'script' ] ) );
				}
			} else {
				$scripts = array( -1 );
			}
			$sql = <<<SQL
	SELECT * FROM `%s` WHERE `script` IN (%s) or `document_name` IN (%s) ORDER by `name` ASC;
SQL;
			$sql = sprintf( $sql, SITEMAP_TABLE, implode( ',', $scripts ), implode( ',', $documents ) );


			$result = DB::query( $sql );

		}

		$documents = self::getRootDocuments();

		foreach ( $documents as $row ) {
			if ( $row[ 'name' ] == $documentName ) {
				array_unshift( $result,
							   array(
								   'id'       => 0,
								   'name'     => 'Корень сайта',
								   'script'   => 'root',
								   'full_url' => '',
							   ) );
			}
		}

		return $result;

	}

	protected static function selectDocumentScriptParents( $documentName ) {
		// Получаем скрипты, где может быть дочерним этот документ
		$sql = 'SELECT * FROM `%s` WHERE `script` in (SELECT `path` FROM `%s` WHERE `document_name` = "%s")';
		$sql = sprintf( $sql, SITEMAP_TABLE, SITEMAP_SCRIPT_CHILD_TABLE, \Faid\DB::escape( $documentName ) );
		return DB::query( $sql );

	}
}

?>