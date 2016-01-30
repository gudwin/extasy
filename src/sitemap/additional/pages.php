<?
use \Faid\DB;

/**
 * 25.03.2010 Метод selectChildWithoutAdditional (и соотв. selectChildWithAdditional) теперь поддерживают языковые версии
 */
class Sitemap_PagesOperations {
	public static $nTotalCount = 0;

	/**
	 * Дополняет sitemap данные данными из таблицы моделей. Если указан параметр $bWidthAdditional также берутся данные для парсинга
	 *
	 * @param $aData           array массив данных
	 * @param $bWithAdditional bool подключать ли данные для парсинга
	 */
	public static function toPreview( $aData, $bWithAdditional = false ) {

		Trace::addMessage( 'DB', '<b>Старт метода Sitemap_PagesOperations::toPreview</b>' );
		$aResult    = array();
		$objectList = self::toObject( $aData );
		foreach ( $aData as $key => $row ) {
			if ( !isset( $objectList[ $key ] ) ) {
				continue;
			}
			$obj = $objectList[ $key ];
			if ( !is_object( $obj ) ) {
				continue;
			}

			$aInsert = $row;
			if ( $bWithAdditional ) {
				$aInsert[ 'additional' ] = $objectList[ $key ]->getPreviewParseData();
			} else {
				$aInsert[ 'document' ] = $objectList[ $key ]->getData();
			}
			$aResult[ ] = $aInsert;
		}

		return $aResult;
	}

	public static function toObject( $aData ) {

		$aResult = array();
		foreach ( $aData as $key=>$row ) {
			// Проверяем, что документ видим 
			if ( empty( $row[ 'visible' ] ) ) {
				// Если мы на фронт-енде, то пропускаем данный элемент
				if ( !defined( 'CMS' ) ) {
					continue;
				}
			}


			// Сюда мы сложим данные, которые мы добавим в результат
			$aInsert = $row;
			// Для начала проверим, что это вообще документ
			if ( !empty( $row[ 'document_name' ] ) ) {

				$szModel   = $row[ 'document_name' ];
				$oDocument = new $szModel( array(), $row );
				// Отыскиваем документ в таблице модели
				$found      = $oDocument->get( $row[ 'document_id' ] );

				$aResult[ $key ] = $oDocument;
			} else {
				$aResult[ $key ] = null;
			}


		}
		return $aResult;
	}

	/**
	 * Дополняет ряды данных данными из таблицы sitemap. Если указан параметр $bWidthAdditional также берутся данные для парсинга
	 *
	 * @param $szDocumentName  string имя документа
	 * @param $aData           array массив данных
	 * @param $bWithAdditional bool подключать ли данные для парсинга
	 */
	public static function appendPageInfo( $szDocumentName, $aData, $bWithAdditional = false ) {
		//
		$validator      = new \Extasy\Validators\IsModelClassNameValidator( $szDocumentName );
		if ( !$validator->isValid() ) {
			throw new \ForbiddenException( "Not a model: {$szDocumentName}" );
		}
		$szModel = $szDocumentName;
		$aSQL    = array();
		foreach ( $aData as $row ) {
			// Т.е. если направляем данные из sitemap - таблицы соберем верные ряды
			if ( is_object( $row ) ) {
				$aSQL[ ] = intval( $row->getId() );
			} else {
				$aSQL[ ] = intval( $row[ 'id' ] );
			}
		}
		if ( empty( $aData ) ) {
			return array();
		}

		// Если мы на фронтенде, то фильтруем документы по видимости
		if ( !defined( 'CMS' ) ) {
			$sqlVisible = ' and `visible` = 1';
		} else {
			$sqlVisible = '';
		}
		//
		$sql      = 'SELECT * FROM `%s` WHERE `document_id` IN (%s) and `document_name` = "%s" %s';
		$sql      = sprintf( $sql,
							 SITEMAP_TABLE,
							 implode( $aSQL, ',' ),
							 DB::escape( $szDocumentName),
							 $sqlVisible );
		$aTmp     = DB::query( $sql );
		$aUrlInfo = array();
		foreach ( $aTmp as $row ) {
			// Если передан объект, то корректно обрабатываем
			if ( is_object( $row ) ) {
				$aUrlInfo[ $row[ 'document_id' ] ] = $row->getId();
			} else {
				$aUrlInfo[ $row[ 'document_id' ] ] = $row;
			}

		}
		//
		$aResult = array();
		foreach ( $aData as $key => $row ) {
			$documentId = is_object( $row ) ? $row->getId() : intval( $row[ 'id' ] );
			if ( !isset( $aUrlInfo[ $documentId ] ) ) {
				continue;
			}
			$aResult[ $key ] = self::appendParseData( $aUrlInfo[ $documentId ], $row, $bWithAdditional );
			{
				$aInsert = $aUrlInfo[ $documentId ];
				if ( !empty( $bWithAdditional ) ) {
					if ( !is_object( $row ) ) {
						$oDocument = new $szModel( $row );
					}
					$aInsert[ 'additional' ] = $oDocument->getPreviewParseData();

				} else {
					if ( is_object( $row ) ) {
						$aInsert[ 'additional' ] = $row->getData();
					} else {
						$aInsert[ 'additional' ] = $row;
					}
				}
				$aResult[ $key ] = $aInsert;

			}
		}
		return $aResult;

	}

	/**
	 *
	 * Добавляет предков к данному списку элементов
	 *
	 * @param array $dataList
	 */
	public static function appendParentsInfo( $dataList, $withAdditional = false ) {
		$parentList = array();
		foreach ( $dataList as $row ) {
			if ( !empty( $row[ 'parent' ] ) ) {
				if ( !in_array( $row[ 'parent' ], $parentList ) ) {
					$parentList[ ] = $row[ 'parent' ];
				}
			}
		}
		if ( empty( $parentList ) ) {
			return array();
		}
		$sql    = 'select * from `%s` where `id` IN (%s)';
		$sql    = sprintf( $sql, SITEMAP_TABLE, implode( ',', $parentList ) );
		$dbRows = DB::query( $sql );
		foreach ( $dbRows as $key => $row ) {
			$parentsList[ $row[ 'id' ] ] = $row;
		}

		$result = array();
		foreach ( $dataList as $key => $row ) {
			if ( isset( $parentsList[ $row[ 'parent' ] ] ) ) {
				$parentSitemapRow                 = $parentsList[ $row[ 'parent' ] ];
				$parentSitemapRow                 = self::appendParseData( $parentSitemapRow,
																		   array(),
																		   $withAdditional );
				$dataList[ $key ][ 'parentInfo' ] = $parentSitemapRow;
			}

		}
		return $dataList;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $nId
	 * @param unknown_type $nCount
	 * @param unknown_type $nStart
	 */
	public static function selectChildDocuments( $nId, $nCount = 0, $nStart = 0 ) {
		$data = self::selectChildWithoutAdditional( $nId, $nCount, $nStart );
		return self::toObject( $data );
	}

	public static function selectChildWithAdditional( $nId, $nCount = 0, $nStart = 0, $orderCondition = '' ) {
		$aItem = self::selectChildWithoutAdditional( $nId, $nCount, $nStart, $orderCondition );
		//
		return self::toPreview( $aItem, true );
	}

	public static function selectChildWithoutAdditional( $nId, $nCount = 0, $nStart = 0, $orderCondition = '' ) {
		return self::selectChilds( $nId, !empty( $szLangSQL ) ? $szLangSQL : '', $orderCondition, $nStart, $nCount );
	}

	protected static function selectChilds( $nSitemapId, $szAdditionalSQL, $orderCondition, $nStart, $nCount ) {
		$nSitemapId = intval( $nSitemapId );
		$nCount     = intval( $nCount );
		$nStart     = intval( $nStart );
		$nStart     = $nStart < 0 ? 0 : $nStart;

		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `%s` WHERE `parent`="%d" %s ORDER by %s';

		if ( !empty( $nCount ) ) {
			$sql .= ' LIMIT %d,%d';
		} else {
		}
		$orderCondition = empty( $orderCondition ) ? '`order` asc' : $orderCondition;
		if ( !defined( 'CMS' ) ) {
			$szAdditionalSQL .= 'and `visible`=1 ';
		}
		$sql = sprintf( $sql,
						SITEMAP_TABLE,
						$nSitemapId,
						$szAdditionalSQL,
						$orderCondition,
						$nStart,
						$nCount );

		$aItem = DB::query( $sql );
		//
		if ( !empty( $nCount ) ) // Если передана информация о пейджинге, то вычисляем его
		{
			$aFound            = DB::get( 'SELECT FOUND_ROWS() as `totalcount`' );
			self::$nTotalCount = $aFound[ 'totalcount' ];
		}
		return $aItem;
	}

	/**
	 * Осуществляет выборку по дочерним документам. В выборку попадают документы только одного определенного типа.
	 *
	 * @param nId    int индекс в карте сайта
	 * @param nCount int количество документов попадающих в пейджинг
	 * @param nStart int стартовая позиция пейджинга
	 */
	public static function selectChildWithAdditionalByDocument( $nId, $szDocument, $nCount = 0, $nStart = 0 ) {
		$aItem = self::selectChildWithoutAdditionalByDocument( $nId, $szDocument, $nCount, $nStart );
		//
		return self::toPreview( $aItem, true );
	}

	public static function selectChildWithoutAdditionalByDocument( $nId, $szDocument, $nCount = 0, $nStart = 0 ) {
		$szDocument = \Faid\DB::escape( $szDocument );
		$nId        = intval( $nId );
		$nCount     = intval( $nCount );
		$nStart     = intval( $nStart );
		$nStart     = $nStart < 0 ? 0 : $nStart;

		// Проверяем работают ли языковые версии
		$szAdditionalSQL = ' and `document_name`="' . $szDocument . '" ';

		return self::selectChilds( $nId, $szAdditionalSQL, '', $nStart, $nCount );
	}

	public static function selectParent() {
		$aInfo   = \Extasy\sitemap\Route::GetCurrentUrlInfo();
		$nId     = $aInfo[ 'id' ];
		$aResult = array();
		while ( $nId != 0 ) {
			$aRow       = self::getParent( $nId );
			$aResult[ ] = $aRow;
			$nId        = $aRow[ 'parent' ];
		}
		$aResult = array_reverse( $aResult );
		return $aResult;
	}

	public static function getParent( $nId ) {
		$sql = 'SELECT * FROM `%s` WHERE `id`="%d"';
		$sql = sprintf( $sql,
						SITEMAP_TABLE,
						$nId );
		return DB::get( $sql );
	}

	///////////////////////////////////////////////////////////////////////////
	// Protected methods
	protected static function appendParseData( $sitemapInfo, $dbRowData = array(), $bWithAdditional = false ) {
		if ( !emptY( $sitemapInfo ) ) {
			if ( !empty( $bWithAdditional ) ) {
				if ( !is_object( $dbRowData ) || empty( $dbRowData ) ) {

					$validator      = new \Extasy\Validators\IsModelClassNameValidator( $sitemapInfo['document_name'] );
					if ( !$validator->isValid() ) {
						throw new \ForbiddenException( "Not a model: {$sitemapInfo['document_name']}" );
					}
					$szModel   = $sitemapInfo[ 'document_name' ];
					$oDocument = new $szModel( $dbRowData );
				}
				$sitemapInfo[ 'additional' ] = $oDocument->getPreviewParseData();
			} else {
				if ( is_object( $dbRowData ) ) {
					$sitemapInfo[ 'additional' ] = $dbRowData->getData();
				} else {
					if ( empty( $dbRowData ) ) {
						$validator      = new \Extasy\Validators\IsModelClassNameValidator( $sitemapInfo[ 'document_name' ] );
						if ( !$validator->isValid() ) {
							throw new \ForbiddenException( "Not a model: {$sitemapInfo[ 'document_name' ]}" );
						}

						$szModel = $sitemapInfo[ 'document_name' ];
						$oDocument = new $szModel();
						$oDocument->get( $sitemapInfo[ 'document_id' ] );
						$sitemapInfo[ 'additional' ] = $oDocument->getData();
					} else {
						$sitemapInfo[ 'additional' ] = $dbRowData;
					}

				}
			}
		}
		return $sitemapInfo;

	}
}

?>