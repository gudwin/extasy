<?
use \Faid\DB;
use \Faid\DBSimple;

//************************************************************//
//                                                            //
//            Класс управления документами                    //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (19.10.2008)                           //
//  Модифицирован:  19.10.2008  by Gisma                      //
//                                                            //
//************************************************************//

class Sitemap {
	/**
	 *   Добавляет новый документ в систему.
	 *
	 * @param $szName      string Заголовок (Тайтл) документа
	 * @param $szUrlKey    string часть урла, которая должна быть включена в адрес
	 * @param $szDocument  string имя документа
	 * @param $nDocumentId int индекс документа
	 * @param $nParent     int индекс верхнего документа
	 *
	 * @return int
	 */
	public static function addPage( $szName, $szUrlKey, $szDocument, $nDocumentId, $nParent ) {
		// Защищаем данные
		$szName      = htmlspecialchars( $szName );
		$szUrlKey    = \Faid\DB::escape( $szUrlKey );
		$nDocumentId = intval( $nDocumentId );
		$nParent     = intval( $nParent );
		$aParent = self::checkParentExists( $nParent );
		try {
			// Проверяем существует ли страница
			self::seekRegisteredDocumentById( $szDocument, $nDocumentId, false );
			// Проверяем существует ли страница с указанным $szUrlKey
			self::seekPageByUrlInParent( $szUrlKey, $nParent );
			// Формируем запрос
			$nId = self::insertIntoBD( $szName, $szUrlKey, $szDocument, $nDocumentId, $aParent );
		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException( 'Url_Key ("' . $szUrlKey . '") already exists. ' . $e->getMessage() );
		}

		// Обновляем сортировку
		self::order( $nParent );
		self::updateParentCount( $nParent );
		self::restoreUrl( $nId );


		Sitemap_History::add( $nId, $szName );
		return $nId;
	}

	protected static function loadPath( $path ) {
		$baseDirs = array( APPLICATION_PATH, LIB_PATH, VENDOR_PATH, EXTASY_PATH );
		foreach ( $baseDirs as $baseDir ) {
			$possibleResult = $baseDir . $path;
			$isGood         = file_exists( $possibleResult )
				&& is_file( $possibleResult )
				&& is_readable( $possibleResult );
			if ( $isGood ) {
				return $possibleResult;
			}
		}
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Добавляет новый скрипт в систему, задается через имя скрипта ($szName) и путь к нему ($szPath)
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function addScript( $szName, $szPath, $szUrl, $nParent, $szAdminEditUrl = '', $bOrderEnable = 0 ) {
		// Защищаем данные
		$szName = htmlspecialchars( $szName );
		$szUrl  = \Faid\DB::escape( $szUrl );

		$szRealPath = self::loadPath( $szPath );
		$failed     = empty( $szRealPath );
		if ( $failed ) {
			throw new SiteMapException( 'Script path "' . $szPath . '" not readable' );
		}
		$szPath         = \Faid\DB::escape( $szPath );
		$szAdminEditUrl = \Faid\DB::escape( $szAdminEditUrl );
		$bOrderEnable   = intval( $bOrderEnable );

		$nParent = intval( $nParent );
		// Проверяем существует ли скрипт физически
		if ( !file_exists( $szRealPath ) || !is_readable( $szRealPath ) ) {

		}

		self::checkParentExists( $nParent );

		try {
			//self::checkByName($nParent,$szName);
			//
			self::seekPageByUrlInParent( $szUrl, $nParent );
			// Добавляем скрипт в базу данных
			$nId = self::insertScriptIntoBD( $szName, $szPath, $szUrl, $nParent, $szAdminEditUrl, $bOrderEnable );

		}
		catch ( SitemapException $e ) {
			throw new SiteMapException( 'Script already defined in Database' );
		}
		// Обновляем порядок
		self::order( $nParent );
		self::updateParentCount( $nParent );
		self::restoreUrl( $nId );

		Sitemap_History::add( $nId, $szName );
		return $nId;
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Удаляет документ $szDocument с индексом $nDocumentId
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function removeDocument( $szDocument, $nDocumentId ) {
		try {
			// Проверяем существует ли страница
			$aPage = self::seekRegisteredDocumentById( $szDocument, $nDocumentId, true );
		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException( 'Page ("' . $szDocument . '","' . $nDocumentId . '") not found' );
		}
		self::remove( $aPage[ 'id' ] );
	}

	public static function remove( $nId ) {
		$aChild = Sitemap_Sample::selectChild( $nId );
		foreach ( $aChild as $row ) {
			self::remove( $row[ 'id' ] );
		}
		$aInfo = SiteMap_Sample::getRowFromBD( $nId );
		if ( empty( $aInfo[ 'script' ] ) ) {
			// Значит это документ...
			try {
				$szClassName = $aInfo[ 'document_name' ];
				if ( !class_exists( $szClassName ) ) {
					throw new NotFoundException( "Document {$szClassName} not found" );
				}
				$model = new $szClassName();
				$found = $model->get( $aInfo[ 'document_id' ] );
				if ( $found ) {
					$model->delete();
				}
			}
			catch ( Exception $e ) {

			}
		}
		// Восстанавливаем сортировку если требуется 
		self::deleteRowFromBD( $aInfo[ 'id' ] );
		self::order( $aInfo[ 'parent' ] );

	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Удаляет скрипт из карты сайта. Скрипт идентифицируется по пути к нему ($szPath) и url ($szUrl)
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function removeScript( $szPath, $szUrl ) {
		$szPath = \Faid\DB::escape( $szPath );
		$szUrl  = \Faid\DB::escape( $szUrl );
		try {
			// Получаем скрипт из БД
			$aScriptInfo = self::getScript( $szPath, $szUrl );
			$nId         = $aScriptInfo[ 'id' ];
			$nParent     = $aScriptInfo[ 'parent' ];
		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException( 'Script not found. Tried to found by path: "' . $szPath . '" and url: "' . $szUrl . '"' );
		}
		// Скрипт найден, формируем запрос на удаление
		self::remove( $nId );

		// обновляем порядок
		self::order( $nParent );
		self::updateParentCount( $nParent );
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Обновляет информацию по документу
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function updatePage( $szDocument, $nDocumentId, $szTitle, $szUrlKey, $nParent ) {
		// Защищаем данные
		$nDocumentId = intval( $nDocumentId );
		$szTitle     = strip_tags( $szTitle );
		$szUrlKey    = \Faid\DB::escape( $szUrlKey );
		$nParent     = intval( $nParent );

		try {
			// Получаем документ
			$aDocument = self::seekRegisteredDocumentById( $szDocument, $nDocumentId, true );

		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException ( 'Page document ("' . $szDocument . '") not found' );
		}
		// проверяем есть ли parent
		self::checkParentExists( $nParent );
		// Проверяем что урл не занят 
		self::checkUrlAvailable( $aDocument[ 'id' ], $nParent, $szUrlKey );
		// Шлем запрос в бд
		self::updatePageInBD( $aDocument[ 'id' ], $szTitle, $szUrlKey, $nParent );

		// Формируем запрос на обновление
//		self::order( $aDocument[ 'parent' ] );
//
		//
		self::restoreUrl( $aDocument[ 'id' ] );
		//
		Sitemap_History::add( $aDocument[ 'id' ], $aDocument[ 'name' ] );

	}

	/**
	 * Хелпер для сохранения станицы без изменения его parent-а, т.е. только для изменения титула страницы и её url_key
	 *
	 * @param array $aPage
	 */
	public static function savePage( $aPage ) {
		try {
			ArrayHelper::indexesExists( array( 'document_name', 'document_id', 'name', 'url_key' ), $aPage );
		}
		catch ( Exception $e ) {
			throw new SiteMapException( 'Save page failed. Argument data incomplete' );
		}
		//
		try {
			// Получаем документ
			$aDocument = self::seekRegisteredDocumentById( $aPage[ 'document_name' ], $aPage[ 'document_id' ], true );
		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException ( 'Page ("' . $aPage[ 'document_name' ] . '","' . $aPage[ 'document_id' ] . '") not found' );
		}

		self::updatePageInBD( $aDocument[ 'id' ], $aPage[ 'name' ], $aPage[ 'url_key' ], $aDocument[ 'parent' ] );
		//
		self::restoreUrl( $aDocument[ 'id' ] );
		//
		Sitemap_History::add( $aDocument[ 'id' ], $aPage[ 'name' ] );

	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Обновляет информацию о скрипте
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function updateScript( $id,
										 $szName,
										 $szPath,
										 $szUrl,
										 $nParent,
										 $szAdminEditUrl = '',
										 $nOrderEnable = 0 ) {
		// Защищаем данные
		$id             = intval( $id );
		$szName         = \Faid\DB::escape( $szName );
		$szUrl          = \Faid\DB::escape( $szUrl );
		$szPath         = \Faid\DB::escape( $szPath );
		$szAdminEditUrl = \Faid\DB::escape( $szAdminEditUrl );
		$nParent        = intval( $nParent );
		$nOrderEnable   = intval( $nOrderEnable );


		try {
			// Получаем скрипт
			$aScript = Sitemap_Sample::get( $id );
			if ( empty( $aScript[ 'script' ] ) ) {
				throw new Exception ( 'Isn`t script' );
			}
		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException( 'Update script failed. Script ("' . $szPath . '","' . $szUrl . '") not found' );
		}

		// проверяем есть ли parent
		self::checkParentExists( $nParent );

		// Обновляем скрипт в бд
		self::updateScriptInBD( $aScript[ 'id' ], $szName, $szPath, $szUrl, $nParent, $szAdminEditUrl, $nOrderEnable );

		self::order( $nParent );

		// Если мы перенесли скрипт к другому владельцу пересортируем дочерние элементы бывшего владельца
		if ( $nParent != $aScript[ 'parent' ] ) {
			self::order( $aScript[ 'parent' ] );
		}
		//
		self::restoreUrl( $aScript[ 'id' ] );
		Sitemap_History::add( $aScript[ 'id' ], $szName );
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function setupScriptChildDocuments( $szPath, $aChildDocument ) {
		$szPath = \Faid\DB::escape( $szPath );

		// Тупо удаляем все старые
		$sql = 'DELETE FROM `%s` WHERE STRCMP(`path`,"%s") = 0';
		$sql = sprintf( $sql,
						SITEMAP_SCRIPT_CHILD_TABLE,
						$szPath );
		DB::post( $sql );
		// Тупо добавляем
		foreach ( $aChildDocument as $row ) {
			$row = \Faid\DB::escape( $row );
			$sql = 'INSERT INTO `%s` SET `path`="%s",`document_name`="%s"';
			$sql = sprintf( $sql,
							SITEMAP_SCRIPT_CHILD_TABLE,
							$szPath,
							$row );
			DB::post( $sql );
		}

	}

	/**
	 * Удялет любое упоминание документа из таблицы script_child. Т.е. документ
	 * удаляется как дочерний для ВСЕХ скриптов
	 *
	 * @param string $documentName
	 */
	public static function deleteDocumentFromScriptChilds( $documentName ) {
		$sql = 'delete from %s where `document_name` = "%s" ';
		$sql = sprintf( $sql, SITEMAP_SCRIPT_CHILD_TABLE, \Faid\DB::escape( $documentName ) );
		DB::post( $sql );
	}

	/**
	 *   Сортирует документы дочерние к $nParentId в указанном порядке
	 * @return
	 */
	public static function order( $parentId ) {

		// Защищаем данные
		$parentId = intval( $parentId );

		// Рутовый раздел автоматической сортировке пока что не поддается
		if ( empty( $parentId ) ) {
			return;
		}
		$page = SiteMap_Sample::getRowFromBD( $parentId );
		// Получаем дочерние объекты
		$children = Sitemap_Sample::selectChild( $page[ 'id' ] );
		// В зависимости от сортировки делаем так
		usort( $children,
			   create_function( '$a,$b',
								'return ($a["order"] > $b["order"]);' ) );
		// Записываем результат
		self::storeOrderResult( $page[ 'id' ], $children );
		return;

	}

	/**
	 *   Функция с помощью которой можно установить порядок напрямую, игнорируя тип сортировки
	 *   верхнего документа
	 * @return
	 */
	public static function manualOrder( $nParent, $aId ) {
		require_once LIB_PATH . 'kernel/functions/array.func.php';
		// Защищаем и проверяем все данные, на области допустимых значений
		$nId = intval( $nParent );
		$aId = ArrayHelper::checkArrayWithInt( $aId );

		// Проверяем существует ли данный ряд в бд, если это не рутовый элемент
		if ( !empty( $nId ) ) {
			try {
				// Получаем страницу
				$aRow = SiteMap_Sample::getRowFromBD( $nId );
			}
			catch ( SiteMapException $e ) {
				// Знач. страницы такой нету
				throw new SiteMapException( 'Page with id="' . $nParent . '" original (' . $nParent . ') not found' );
			}
		}
		// Конвертируем данные для сохранения
		$aOrder = array();
		foreach ( $aId as $row ) {
			$aOrder[ ] = array( 'id' => $row );
		}
		self::storeOrderResult( $nId, $aOrder );
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Возвращает всех детей указанного нода
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function selectChild( $nPageId ) {
		$nPageId = intval( $nPageId );
		try {
			// Получаем страницу
			if ( !empty( $nPageId ) ) {
				$aRow = SiteMap_Sample::getRowFromBD( $nPageId );
			}

		}
		catch ( SiteMapException $e ) {
			// Знач. страницы такой нету
			throw new SiteMapException( 'Page with id="' . $nPageId . '" not found' );
		}
		//
		$aChild = SiteMap_Sample::selectChild( $nPageId );
		return $aChild;
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Отыскивает страницу
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public static function getPage( $szDocument, $nDocumentId ) {
		$szDocument  = \Faid\DB::escape( $szDocument );
		$nDocumentId = intval( $nDocumentId );
		//
		try {
			$aDocument = self::seekRegisteredDocumentById( $szDocument, $nDocumentId, true );
		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException( 'getPage failed. Page ("' . $szDocument . '","' . $nDocumentId . '") not found' );
		}
		return $aDocument;
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Проверяет не занят ли такой url уже в этой папке
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	protected static function seekPageByUrlInParent( $szUrl, $nParent ) {
		$aFoundPage = Sitemap_Sample::seekByUrlInParent( $szUrl, $nParent );
		if ( !empty( $aFoundPage ) ) {
			throw new SiteMapException ( 'Page exists' );
		}
		return $aFoundPage;

	}

	/**
	 * Проверяет существование документа
	 *
	 * @param string $szDocument  имя документа
	 * @param int    $nDocumentId индекс документа
	 * @param bool   $bMustExists если true, то документ должен существовать
	 */
	protected static function seekRegisteredDocumentById( $szDocument, $nDocumentId, $bMustExists = true ) {
		$aPage = Sitemap_Sample::getByDocumentId( $szDocument, $nDocumentId );
		if ( !empty( $aPage ) == ( $bMustExists ) ) {
			return $aPage;
		}
		throw new SiteMapException ( 'Search registered document failed' );


	}

	protected static function insertIntoBD( $szName, $szUrlKey, $szDocument, $nDocumentId, $aParent ) {
		$szName      = strip_tags( $szName );
		$szName      = \Faid\DB::escape( $szName );
		$szUrlKey    = \Faid\DB::escape( $szUrlKey );
		$szDocument  = \Faid\DB::escape( $szDocument );
		$nDocumentId = intval( $nDocumentId );

		$condition = array(
			'parent' => $aParent[ 'id' ]
		);
		DBSimple::update( 'sitemap', '`order` = `order` + 1', $condition );

		$visible = SystemRegisterHelper::getValue( '/System/Sitemap/visible' );
		$sql     = 'INSERT INTO `%s` SET `name`="%s",`url_key`="%s",`document_name`="%s",`document_id`="%d",`parent`="%d",`order`="%d",date_created=NOW(),date_updated=NOW(),revision_count=0, `sitemap_xml_priority` = "0.1", `sitemap_xml_change`="weekly",`visible`="%d"';
		$sql     = sprintf( $sql,
							SITEMAP_TABLE,
							$szName,
							$szUrlKey,
							$szDocument,
							$nDocumentId,
							$aParent[ 'id' ],
							0,
							$visible );

		DB::post( $sql );

		return DB::$connection->insert_id;
	}

	protected static function checkScriptExists( $szPath, $szUrl ) {
		$aFound = Sitemap_Sample::getScript( $szPath, $szUrl );
		if ( !empty( $aFound ) ) {
			throw new SiteMapException( 'Script already exists' );
		}
	}

	protected static function insertScriptIntoBD( $szName, $szPath, $szUrl, $nParent, $szAdminEditUrl, $bOrderEnable ) {
		$szName         = \Faid\DB::escape( $szName );
		$szPath         = \Faid\DB::escape( $szPath );
		$szUrl          = \Faid\DB::escape( $szUrl );
		$nParent        = intval( $nParent );
		$szAdminEditUrl = \Faid\DB::escape( $szAdminEditUrl );
		$bOrderEnable   = intval( $bOrderEnable );

		$sql = 'INSERT INTO `%s` SET `name`="%s",`url_key`="%s",`script`="%s",`parent`="%d",`script_admin_url`="%s",date_created=NOW(),date_updated=NOW(),'
			. ' `script_manual_order`="%d", `sitemap_xml_priority` = "0.1", `sitemap_xml_change`="weekly",`visible`="1" ';
		$sql = sprintf( $sql,
						SITEMAP_TABLE,
						$szName,
						$szUrl,
						$szPath,
						$nParent,
						$szAdminEditUrl,
						$bOrderEnable );
		DB::post( $sql );
		return DB::$connection->insert_id;
	}

	protected static function deleteRowFromBD( $nId ) {
		$nId = intval( $nId );
		$sql = 'DELETE FROM `%s` WHERE id="%d"';
		$sql = sprintf( $sql,
						SITEMAP_TABLE,
						$nId );
		DB::post( $sql );
	}

	public static function getScript( $szPath, $szUrl ) {
		$aScript = Sitemap_Sample::getScript( $szPath, $szUrl );
		if ( empty( $aScript ) ) {
			throw new SiteMapException ( 'Script ("' . $szPath . '","' . $szUrl . '") not found in database' );
		}
		return $aScript;
	}

	protected static function updatePageInBD( $nId, $szTitle, $szUrlKey, $nParent ) {
		$nId      = intval( $nId );
		$szTitle  = strip_tags( $szTitle );
		$szTitle  = \Faid\DB::escape( $szTitle );
		$szUrlKey = \Faid\DB::escape( $szUrlKey );
		$nParent  = intval( $nParent );
		$sql      = 'UPDATE `%s` SET `name`="%s", `url_key`="%s",`parent`="%d",`date_updated`=NOW(),`revision_count` = `revision_count` + 1 WHERE `id`="%d" ';
		$sql      = sprintf( $sql,
							 SITEMAP_TABLE,
							 $szTitle,
							 $szUrlKey,
							 $nParent,
							 $nId );
		DB::post( $sql );
	}

	protected static function updateScriptInBD( $nId,
												$szName,
												$szPath,
												$szUrl,
												$nParent,
												$szAdminEditUrl,
												$bOrderEnable ) {
		$nId            = intval( $nId );
		$szName         = strip_tags( $szName );
		$szName         = \Faid\DB::escape( $szName );
		$szPath         = \Faid\DB::escape( $szPath );
		$szUrl          = \Faid\DB::escape( $szUrl );
		$nParent        = intval( $nParent );
		$szAdminEditUrl = \Faid\DB::escape( $szAdminEditUrl );
		$bOrderEnable   = intval( $bOrderEnable );

		$sql = 'UPDATE `%s` SET `name`="%s",`script`="%s",`url_key`="%s",`parent`="%d",`script_admin_url`="%s",`script_manual_order`="%d", `date_updated`=NOW(), `revision_count` = `revision_count` + 1 WHERE `id`="%d"';
		$sql = sprintf( $sql,
						SITEMAP_TABLE,
						$szName,
						$szPath,
						$szUrl,
						$nParent,
						$szAdminEditUrl,
						$bOrderEnable,
						$nId );
		DB::post( $sql );
	}

	protected static function storeOrderResult( $nId, $aResult ) {
		$nId = intval( $nId );
		$i   = 0;
		foreach ( $aResult as $row ) {
			$sql = 'UPDATE `%s` SET `order`="%d" WHERE `id`="%d" and `parent`="%d"';
			$sql = sprintf( $sql,
							SITEMAP_TABLE,
							$i,
							intval( $row[ 'id' ] ),
							$nId );
			DB::post( $sql );
			$i++;
		}
	}

	/**
	 *
	 * Данный метод обновляет видимость документа
	 *
	 * @param int $sitemapId
	 * @param int $visible
	 */
	public static function updateVisible( $sitemapId, $visible ) {
		$sitemapId = IntegerHelper::toNatural( $sitemapId );
		$visible   = IntegerHelper::toNatural( $visible );
		$sql       = 'UPDATE `sitemap` SET `visible`="%d" WHERE `id`="%d"';
		$sql       = sprintf( $sql, $visible, $sitemapId );
		DB::post( $sql );
	}

	public static function updateParentCount( $nId ) {
		if ( $nId == 0 ) {
			return;
		}
		$nId    = intval( $nId );
		$nCount = Sitemap_Sample::getCount( $nId );
		//
		$sql = 'UPDATE `%s` SET `count`=%d WHERE `id`="%d"';
		$sql = sprintf(
			$sql,
			SITEMAP_TABLE,
			$nCount,
			$nId );
		DB::post( $sql );
	}

	protected static function checkParentExists( $nParent ) {
		if ( empty( $nParent ) ) {
			return;
		}
		try {
			$aParent = SiteMap_Sample::getRowFromBD( $nParent );
		}
		catch ( SiteMapException $e ) {
			throw new SiteMapException( 'Parent element (' . $nParent . ') not found ' );
		}
		return $aParent;

	}

	/**
	 * Восстанавливает все урлы в системе или только один, если соотв. указано
	 *
	 * @param $sitemapId int Индекс конкретной страницы для генерации URL
	 */
	protected static function restoreUrl( $sitemapId = 0 ) {

		Sitemap_RestoreUrl::restoreOne( $sitemapId );


	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Проверяет не существует ли страницы с таким именем
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	protected static function checkByName( $nParent, $szName ) {
		$aResult = Sitemap_Sample::getByNameAndParent( $nParent, $szName );
		if ( !empty( $aResult ) ) {
			throw new SiteMapException( 'Page with name "' . $szName . '" already exists' );
		}
	}

	/**
	 * Проверяет что урл доступен
	 */
	protected static function checkUrlAvailable( $sitemapId, $parentId, $urlKey ) {
		$sitemapId = IntegerHelper::toNatural( $sitemapId );
		$parentId  = IntegerHelper::toNatural( $parentId );
		$urlKey    = \Faid\DB::escape( $urlKey );
		// Получаем все документы с указанным url_key и parent
		$sql  = 'select * from `%s` where parent = "%d" and `url_key`="%s" ';
		$sql  = sprintf( $sql, SITEMAP_TABLE, $parentId, $urlKey );
		$data = DB::query( $sql );
		// Если их 0, то всё ок
		if ( sizeof( $data ) == 0 ) {
			return;
		}
		// Если их больше 1
		if ( sizeof( $data ) > 1 ) {
			// бросаем исключение
			$message = 'Sitemap tree conflict in parent_id=%d and url_key=%s ';
			$message = sprintf( $message, $parentId, $urlKey );
			throw new SiteMapException( $message );
		}
		// Если одна, то сравниваем индексы
		$data = $data[ 0 ];
		if ( $data[ 'id' ] != $sitemapId ) {
			// не совпадают
			// бросаем исключение
			$message = 'Sitemap tree conflict for sitemap_id=%d in parent_id=%d and url_key=%s url already used by sitemap_id="%d"';
			$message = sprintf( $message, $sitemapId, $parentId, $urlKey, $data[ 'id' ] );
			throw new SiteMapException( $message );
		}
	}
}

?>